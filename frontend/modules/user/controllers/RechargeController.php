<?php

namespace app\modules\user\controllers;

use Yii;
use common\lib\bchelp\BcRound;
use frontend\controllers\BaseController;
use common\models\user\RechargeRecord;
use common\models\user\MoneyRecord;
use common\models\user\UserAccount;
use common\models\TradeLog;
use common\lib\cfca\Payment;

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

        $user_account = UserAccount::findOne(['uid' => $uid, 'type' => UserAccount::TYPE_BUY]);
        $recharge = new RechargeRecord();
        $recharge->uid = $uid;
        $recharge->bank_id = $bank_id;

        if ($recharge->load(Yii::$app->request->post()) && $recharge->validate()) {
            if (empty($bank_id) || empty($account_type)) {
                exit('无效的参数');
            }

            if (!in_array($account_type, ['11', '12'])) {
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
                return $this->redirect('/user/recharge/recharge-err');
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
            $simpleXML->Body->NotificationURL = \Yii::$app->params['main_url'] . '/user/recharge/rechargecallback';   //？？？？
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
     *  充值回调函数 1318 1348
     */
    public function actionRechargecallback() {
        $message = \Yii::$app->request->post('message');
        $signature = Yii::$app->request->post("signature");
        $payment = new Payment();
        $plainText = trim(base64_decode($message));
        $simpleXML = new \SimpleXMLElement($plainText);

        //录入日志信息
        $trade_log = new TradeLog([
            'tx_code' => $simpleXML->Head->TxCode,
            'tx_sn' => $simpleXML->Body->PaymentNo,
            'pay_id' => 0,
            'uid' => $this->uid,
            'account_id' => Yii::$app->user->accountInfo->id,
            'request' => $plainText
        ]);

        $xml_path = Yii::getAlias('@common') . "/config/xml/cfca_response.xml";
        $xmlresponse = file_get_contents($xml_path);
        $responseXML = new \SimpleXMLElement($xmlresponse);
        $code = "2000";
        $errInfo = 'OK';

        //验证签名
        $ok = $payment->cfcaverify($plainText, $signature);
        if ($ok != 1) {
            //签名失败，返回错误信息
            $code = "2002";
            $errInfo = "验签失败";
        } else {
            //签名成功
            $txCode = $simpleXML->Head->TxCode;
            if (!in_array($txCode, ['1318', '1348'])) {
                $code = "2001";
                $errInfo = "调用接口错误";
            }
        }

        $responseXML->Head->Code = $code;
        $responseXML->Head->Message = $errInfo;
        $responseXMLStr = $responseXML->asXML();
        $base64Str = base64_encode(trim($responseXMLStr));

        //记录日志信息
        $trade_log->response_code = $code;
        $trade_log->response = $responseXMLStr;
        $trade_log->save();

        print $base64Str;
    }

    /**
     * 查询充值状态 1320
     */
    public function actionCheckarchstatus($recharge) {
        $payment = new Payment();
        $xml = Yii::getAlias('@common') . "/config/xml/cfca_1320.xml";
        $content = file_get_contents($xml);
        $simpleXML = new \SimpleXMLElement($content);
        $simpleXML->Head->InstitutionID = \Yii::$app->params['cfca']['institutionId'];
        $simpleXML->Body->PaymentNo = $recharge->sn;
        $xmlStr = $simpleXML->asXML();

        $message = base64_encode(trim($xmlStr));
        $signature = $payment->cfcasign_pkcs12(trim($xmlStr));
        $response = $payment->cfcatx_transfer($message, $signature);
        $plainText = (base64_decode($response[0]));
        $ok = $payment->cfcaverify($plainText, $response[1]);

        if ($ok != 1) {
            return $this->redirect('/user/recharge/recharge-err');
        } else {
            $response_XML = new \SimpleXMLElement($plainText);
            if ($response_XML->Head->Code == "2000") {
                if ($response_XML->Body->Status == 20) {
                    $uid = $this->uid;
                    $bankTxTime = $response_XML->Body->BankNotificationTime;
                    $user_acount = UserAccount::findOne(['type' => UserAccount::TYPE_BUY, 'uid' => $uid]);

                    $bc = new BcRound();
                    bcscale(14);
                    $transaction = Yii::$app->db->beginTransaction();
                    //修改充值状态
                    RechargeRecord::updateAll(['status' => 1, 'bankNotificationTime' => $bankTxTime], ['id' => $recharge->id]);
                    //添加交易流水
                    $money_record = new MoneyRecord();
                    $money_record->sn = MoneyRecord::createSN();
                    $money_record->type = MoneyRecord::TYPE_RECHARGE;
                    $money_record->osn = $recharge->sn;
                    $money_record->account_id = $user_acount->id;
                    $money_record->uid = $uid;
                    $money_record->balance = $bc->bcround(bcadd($user_acount->available_balance, $recharge->fund), 2);
                    $money_record->in_money = $recharge->fund;
                    $money_record->status = MoneyRecord::STATUS_SUCCESS;

                    if (!$money_record->save()) {
                        $transaction->rollBack();
                        return $this->redirect('/user/recharge/recharge-err');
                    }

                    //录入user_acount记录
                    $user_acount->uid = $user_acount->uid;
                    $user_acount->account_balance = $bc->bcround(bcadd($user_acount->account_balance, $recharge->fund), 2);
                    $user_acount->available_balance = $bc->bcround(bcadd($user_acount->available_balance, $recharge->fund), 2);
                    $user_acount->in_sum = $bc->bcround(bcadd($user_acount->in_sum, $recharge->fund), 2);

                    if (!$user_acount->save()) {
                        $transaction->rollBack();
                        return $this->redirect('/user/recharge/recharge-err');
                    }

                    $transaction->commit();
                    return $this->redirect('/user/useraccount/accountcenter');
                } else {
                    return $this->redirect('/user/recharge/recharge-err');
                }
            } else {
                return $this->redirect('/user/recharge/recharge-err');
            }
        }
    }

    /**
     * 充值失败页面
     */
    public function actionRechargeErr() {
        return $this->render('recharge_err');
    }

}
