<?php

namespace app\modules\user\controllers;

use Yii;
use common\lib\bchelp\BcRound;
use frontend\controllers\BaseController;
use common\models\user\RechargeRecord;
use common\models\user\MoneyRecord;
use common\models\user\UserAccount;
use common\lib\cfca\Payment;
use common\models\user\CfcaLog;

class RechargeController extends BaseController {

    public $layout = false;
    public $enableCsrfValidation = false; //因为中金post的提交。所以要关闭csrf验证

    /**
     * 充值
     */
    public function actionRecharge() {
        $uid = $this->uid;
        $bank = Yii::$app->params['bank'];
        $bank_id = Yii::$app->request->post('bankid');
        $account_type = Yii::$app->request->post('account_type');

        $user_account = UserAccount::getUserAccount($uid);
        $recharge = new RechargeRecord();
        $recharge->uid = $uid;
        $recharge->bank_id = $bank_id;

        if ($recharge->load(Yii::$app->request->post()) && $recharge->validate()) {
            if (empty($bank_id) || empty($account_type)) {
                exit('无效的参数');
            }

            if (!in_array($account_type, ['11','12'])) {
                exit('无效的账户类型');
            }

            //录入recharge_record记录
            $recharge->sn = RechargeRecord::createSN();
            $recharge->pay_id = 0;
            $recharge->account_id = $user_account->id;
            $recharge->pay_bank_id = $bank_id;
            $recharge->bankNotificationTime = '0';
            $recharge->status = RechargeRecord::STATUS_NO;

            if (!$recharge->save()) {
                $transaction->rollBack();
                return $this->redirect('/user/recharge/rechstatus?flag=err');
            }

            $xml_path = Yii::getAlias('@common') . "/config/xml/cfca_1311.xml";
            $xmltx1311 = file_get_contents($xml_path);
            $InstitutionID = \Yii::$app->params['cfca']['institutionId'];
            $simpleXML = new \SimpleXMLElement($xmltx1311);
            $simpleXML->Head->InstitutionID = $InstitutionID;
            $simpleXML->Body->OrderNo = $recharge->sn;
            $simpleXML->Body->PaymentNo = $recharge->sn;
            $simpleXML->Body->Amount = $recharge->fund * 100;
            $simpleXML->Body->Fee = 0;
            $simpleXML->Body->Usage = '大额充值';
            $simpleXML->Body->Remark = '大额充值';
            $simpleXML->Body->NotificationURL = \Yii::$app->params['main_url'] . '/user/recharge/rechargecallback';
            $simpleXML->Body->BankID = $recharge->bank_id;
            $simpleXML->Body->AccountType = $account_type;    //个人网银

            $payment = new \common\lib\cfca\Payment();
            $xmlStr = trim($simpleXML->asXML());

            //录入日志信息
            $trade_log = new \common\models\TradeLog([
                'tx_code' => $simpleXML->Head->TxCode,
                'tx_sn' => $simpleXML->Body->PaymentNo,
                'pay_id' => 0,
                'uid' => $uid,
                'account_id' => $user_account->id,
                'request' => $xmlStr
            ]);
            $trade_log->save();

            $message = base64_encode($xmlStr);
            $signature = $payment->cfcasign_pkcs12($xmlStr);

            return $this->render('dorecharge', ['message' => $message, 'signature' => $signature]);
        }

        return $this->render('recharge', ['recharge' => $recharge, 'user_account' => $user_account, 'bank' => $bank]);
    }

    /*
     *  充值回调函数
     */
    public function actionRechargecallback() {
        $message = \Yii::$app->request->post('message');
        $signature = Yii::$app->request->post("signature");
        $payment = new Payment();
        $plainText = trim(base64_decode($message));

        //记录中金返回响应
        $cfcalog = new CfcaLog();
        $cfcalog->type = 2;
        $cfcalog->account_id = 0;
        $cfcalog->uid = $this->uid;
        $cfcalog->log_type = 2;
        $cfcalog->response_code="前台通知1";
        $cfcalog->response = $plainText;
        $cfcalog->save();

        $ok = $payment->cfcaverify($plainText, $signature);
        if ($ok != 1) {
//            $errInfo = "验签失败";//
            return $this->redirect('/user/recharge/rechstatus?status=defeat');
        } else {
            $simpleXML = new \SimpleXMLElement($plainText);

            //记录中金返回响应
            $cfcalog = new CfcaLog();
            $cfcalog->type = 2;
            $cfcalog->account_id = 0;
            $cfcalog->uid = $this->uid;
            $cfcalog->log_type = 2;
            $cfcalog->response_code="前台通知2";
            $cfcalog->response = $plainText;
            $cfcalog->save();

            $txCode = $simpleXML->Head->TxCode;
            $InstitutionID = $simpleXML->Body->InstitutionID; //获取返回的机构编号
            if ($InstitutionID != \Yii::$app->params['cfca']['InstitutionID']) {
                //exit('错误的机构编码');
                return $this->redirect('/user/recharge/rechstatus?status=defeat');
            }
            $Status = intval($simpleXML->Body->Status); //获取返回结果 状态： 10=未支付 20=已支付
            $BankNotificationTime = $simpleXML->Body->BankNotificationTime; //获取返回支付平台收到银行通知时间
            $Amount = $simpleXML->Body->Amount; //支付金额，单位：分
            $PaymentNo = $simpleXML->Body->PaymentNo; //支付交易流水号
            if ($txCode == "1318") {//1318-市场订单支付状态变更通知
                $rechareg = RechargeRecord::findOne(['sn' => $PaymentNo]);
                if (empty($rechareg)) {
                    //exit('不正确的充值单据');
                    return $this->redirect('/user/recharge/rechstatus?status=defeat');
                } else if ($rechareg->status == RechargeRecord::STATUS_YES) {//已经充值成功的
                    return $this->redirect('/user/recharge/rechstatus?status=success');
                } else if ($Status == 20) {
                    $ua = UserAccount::findOne($rechareg->account_id);
                    $bcround = new BcRound();
                    bcscale(14);
                    $transaction = Yii::$app->db->beginTransaction();

                    //记录中金返回响应
                    $cfcalog = new CfcaLog();
                    $cfcalog->type = 2;
                    $cfcalog->account_id = $ua->id;
                    $cfcalog->uid = $this->uid;
                    $cfcalog->log_type = 2;
                    $cfcalog->response = $plainText;
                    if (!$cfcalog->save()) {
                        $transaction->rollBack();
                        return $this->redirect('/user/recharge/rechstatus?status=defeat');
                    }

                    //结算部分。
                    $jiesuan = new \common\models\user\Jiesuan();
                    $jiesuan->amount = bcdiv($Amount, 100, 2) * 1;
                    $jiesuan->osn = $rechareg->sn;
                    $jiesuan->type = 1;
                    $res = $jiesuan->settlement();
                    if ($res === false) {
                        $transaction->rollBack();
                        return $this->redirect('/user/recharge/rechstatus?status=defeat');
                    }

                    $rechareg->status = RechargeRecord::STATUS_YES;
                    $rechareg->bankNotificationTime = $BankNotificationTime;
                    if (!$rechareg->save()) {
                        $transaction->rollBack();
                        //exit('修改充值状态异常');
                        return $this->redirect('/user/recharge/rechstatus?status=defeat');
                    }

                    $ua->account_balance = $bcround->bcround(bcadd($ua->account_balance, bcdiv($Amount, 100)), 2); //因为amount以分为单位。所以除以100
                    $ua->available_balance = $bcround->bcround(bcadd($ua->available_balance, bcdiv($Amount, 100)), 2); //因为amount以分为单位。所以除以100
                    $ua->in_sum = $bcround->bcround(bcadd($ua->in_sum, bcdiv($Amount, 100)), 2); //因为amount以分为单位。所以除以100
                    if (!$ua->save()) {
                        $transaction->rollBack();
                        //exit('资金记录异常');
                        return $this->redirect('/user/recharge/rechstatus?status=defeat');
                    }

                    $mr_model = new MoneyRecord();
                    $mr_model->sn = MoneyRecord::createSN();
                    $mr_model->osn = $PaymentNo;
                    $mr_model->type = MoneyRecord::TYPE_RECHARGE;
                    $mr_model->account_id = $ua->id;
                    $mr_model->uid = $this->uid;
                    $mr_model->balance = $ua->available_balance;
                    $mr_model->remark = "资金流水号:" . $mr_model->sn . ',充值流水号:' . $PaymentNo . ',账户余额:' . ($ua->account_balance) . '元，可用余额:' . ($ua->available_balance) . '元，冻结金额:' . $ua->freeze_balance . '元。';
                    $mr_model->status = MoneyRecord::STATUS_SUCCESS;
                    $mr_model->in_money = bcdiv($Amount, 100, 2) * 1;
                    $mrre = $mr_model->save();
                    //var_dump($mr_model->getErrors());
                    if (!$mrre) {
                        $transaction->rollBack();
                        //exit('资金记录异常');
                        return $this->redirect('/user/recharge/rechstatus?status=defeat');
                    }

                    $transaction->commit();
                    return $this->redirect('/user/recharge/rechstatus?status=success');
                } else {
                    //exit('充值不成功');
                    return $this->redirect('/user/recharge/rechstatus?status=defeat');
                }
            } else {
                //exit('错误返回来源');
                return $this->redirect('/user/recharge/rechstatus?status=defeat');
            }
        }
    }

    /**
     * 充值——是否成功
     */
    public function actionRechstatus($status = null) {
        $this->layout = 'login';
        return $this->render('rechstatus', ['status' => $status]);
    }

    public function actionSec(){

        $rsapath = Yii::getAlias('@common');
//        //$res = Yii::$app->functions->rsaCreateSign($rsapath.'/components/rsa/settlement/rsa_private_key.pem','01760120540001335');
        $res = file_get_contents($rsapath.'/api-rsa/content');
//        //echo $res;exit;
        $unsec = Yii::$app->functions->rsaVerifySign($rsapath.'/components/rsa/settlement/rsa_public_key.pem',  (Yii::$app->params['accountnumber']),$res);
        //$response = \Yii::$app->functions->createXmlResponse('2003',"非法的账号".(Yii::$app->params['accountnumber']));
        var_dump($unsec);exit;
    }

}
