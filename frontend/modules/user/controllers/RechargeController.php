<?php

namespace app\modules\user\controllers;

use Yii;
use common\lib\bchelp\BcRound;
use frontend\controllers\BaseController;
use common\models\user\RechargeRecord;
use common\models\user\MoneyRecord;
use common\models\user\UserAccount;
use common\lib\cfca\Payment;
use yii\web\Controller;
use common\models\user\CfcaLog;

class RechargeController extends BaseController {

    public $layout = 'main';
    //public $enableCsrfValidation = false; //因为中金post的提交。所以要关闭csrf验证
    /**
     * 充值
     */
    public function actionRecharge() {

        $bank_show = Yii::$app->params['bank'];
        foreach ($bank_show as $key => $val) {
            if ($val['status'] == '0') {
                unset($bank_show[$key]);
            }
        }
        $recharge = new RechargeRecord();
        $session = Yii::$app->session;
        $useraccount = $session->get('useraccount');
        $ua = UserAccount::getUserAccount($this->uid, $useraccount);
        if ($recharge->load(Yii::$app->request->post()) && $recharge->validate()) {
            $simpleXML = new \SimpleXMLElement(\Yii::$app->params['cfca']['xmltx1311']);
            //赋值
          //  $payees = '金交中心';
//         $bankID = '700';
	   $account_type = Yii::$app->request->post('account_type');
            $accountType = intval($account_type);
            $refund = $recharge->fund;
            $rebankid = $recharge->bank_id;
            $amount = bcmul($recharge->fund, 100, 2) * 1;
            $simpleXML->Body->InstitutionID = \Yii::$app->params['cfca']['InstitutionID'];
            $sn = RechargeRecord::createSN();
            $recharge = new RechargeRecord();
            $recharge->sn = $sn;
            $recharge->account_id = $ua->id;
            $recharge->uid = $this->uid;
            $recharge->fund = $refund;
            $recharge->bank_id = $rebankid;
            $recharge->remark = '充值投标' . ($recharge->fund) . '元';
            $recharge->status = 0;

//            var_dump($recharge->getErrors(),$recharge->save());exit;
            if (!$recharge->save()) {
                return $this->redirect('/user/recharge/rechstatus?status=defeat');
            }
            $simpleXML->Body->OrderNo = $sn;
            $simpleXML->Body->PaymentNo = $sn;
            $simpleXML->Body->Amount = ($amount);
            $simpleXML->Body->Fee = 0; //例子中都是intval;
//            $simpleXML->Body->PayerID = $this->uid;
//            $simpleXML->Body->PayerName = $this->user->username;
            $simpleXML->Body->Usage = '充值投标';
            $simpleXML->Body->Remark = '充值投标';
            $simpleXML->Body->NotificationURL = \Yii::$app->params['main_url'] . '/user/recharge/rechargecallback';
//            foreach (explode(";", $payees) as $value) {
//                $simpleXML->Body->PayeeList->addChild("Payee", $value);
//            }
            $simpleXML->Body->BankID = $rebankid;
            $simpleXML->Body->AccountType = $accountType;

            $xmlStr = $simpleXML->asXML();

            $cfcalog = new CfcaLog();
            $cfcalog->type = 1;
            $cfcalog->account_id = $ua->id;
            $cfcalog->uid = $this->uid;
            $cfcalog->log_type = 1;
            $cfcalog->response = $xmlStr;
            $cfcalog->save();

            $message = base64_encode(trim($xmlStr));
            $payment = new Payment();
            $signature = $payment->cfcasign_pkcs12(trim($xmlStr));
            $this->layout = FALSE;
            return $this->render('dorecharge', ['message' => $message, 'signature' => $signature]);
        }
        return $this->render('recharge', ['model' => $recharge, 'ua' => $ua, 'bank_show' => $bank_show]);
    }

    public $enableCsrfValidation = false; //局部关闭 csrf

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
