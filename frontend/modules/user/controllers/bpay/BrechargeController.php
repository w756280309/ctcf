<?php

namespace app\modules\user\controllers\bpay;

use Yii;
use common\lib\bchelp\BcRound;
use yii\web\Controller;
use common\models\user\RechargeRecord;
use common\models\user\MoneyRecord;
use common\models\user\UserAccount;
use common\models\TradeLog;
use common\lib\cfca\Payment;
use common\models\user\User;
use common\models\user\Jiesuan;
use common\models\sms\SmsMessage;
use PayGate\Cfca\Message\Request1318;
use PayGate\Cfca\Message\Request1348;

class BrechargeController extends Controller
{
    public $enableCsrfValidation = false; //因为中金post的提交。所以要关闭csrf验证

    /*
     *  充值回调函数 1318
     */

    public function actionRechargecallback()
    {
        $message = \Yii::$app->request->post('message');
        $signature = \Yii::$app->request->post('signature');
        $payment = new Payment();
        $plainText = trim(base64_decode($message));
        $simpleXML = new \SimpleXMLElement($plainText);

        $req = new Request1318(
                $simpleXML->Body->InstitutionID,
                $simpleXML->Body->PaymentNo,
                $simpleXML->Body->Amount,
                $simpleXML->Body->Status,
                $simpleXML->Body->BankNotificationTime
        );

        $PaymentNo = $req->getPaymentNo(); //支付交易流水号
        $recharge = RechargeRecord::findOne(['sn' => $PaymentNo]);
        $user = User::findOne($recharge->uid);

        //录入日志信息
        $trade_log = new TradeLog($user, $req, null);

        $xml_path = Yii::getAlias('@common').'/config/xml/cfca_response.xml';
        $xmlresponse = file_get_contents($xml_path);
        $responseXML = new \SimpleXMLElement($xmlresponse);
        $code = '2000';
        $errInfo = 'OK';

        //验证签名
        $ok = $payment->cfcaverify($plainText, $signature);
        if ($ok != 1) {
            //签名失败，返回错误信息
            $code = '2002';
            $errInfo = '验签失败';
        } else {
            //签名成功
            $txCode = $req->getTxCode();
            $InstitutionID = $req->getInstitutionId(); //获取返回的机构编号

            if ($InstitutionID != \Yii::$app->params['cfca']['institutionId']) {
                $code = '2001';
                $errInfo = '错误的机构编码';
            } else {
                $Status = intval($req->getStatus()); //获取返回结果 状态： 10=未支付 20=已支付
                $BankNotificationTime = $req->getBankNotificationTime(); //获取返回支付平台收到银行通知时间

                if ($txCode === 1318) {
                    if (empty($recharge)) {
                        $txCode = '2001';
                        $errInfo = '不正确的充值单据';
                    } elseif ($Status === 20) {
                        $recharge->bankNotificationTime = $BankNotificationTime;
                        if (!$this->is_updateAccount($recharge, $user)) {
                            $txCode = '2001';
                            $errInfo = '数据库错误';
                        }
                    }
                } else {
                    $code = '2001';
                    $errInfo = '调用接口错误';
                }
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

        echo $base64Str;
    }

    /**
     * 修改充值状态，记录流水信息.
     *
     * @return true 修改数据库成功或充值成功 false 修改数据库失败
     */
    public static function is_updateAccount(RechargeRecord $recharge, User $user)
    {
        if ($recharge->status == RechargeRecord::STATUS_YES) {
            return true;
        } else {
            $uid = $user->id;
            $user_acount = UserAccount::findOne(['type' => UserAccount::TYPE_LEND, 'uid' => $uid]);

            $bc = new BcRound();
            bcscale(14);
            $transaction = Yii::$app->db->beginTransaction();
            //修改充值状态
            $res = RechargeRecord::updateAll(['status' => 1, 'bankNotificationTime' => $recharge->bankNotificationTime], ['id' => $recharge->id]);
            if (!$res) {
                $transaction->rollBack();

                return false;
            }
            //添加交易流水
            $money_record = new MoneyRecord();
            $money_record->sn = MoneyRecord::createSN();
            $money_record->type = MoneyRecord::TYPE_RECHARGE;
            $money_record->osn = $recharge->sn;
            $money_record->account_id = $user_acount->id;
            $money_record->uid = $uid;
            $money_record->balance = $bc->bcround(bcadd($user_acount->available_balance, $recharge->fund), 2);
            $money_record->in_money = $recharge->fund;

            if (!$money_record->save()) {
                $transaction->rollBack();

                return false;
            }

            //录入user_acount记录
            $user_acount->uid = $user_acount->uid;
            $user_acount->account_balance = $bc->bcround(bcadd($user_acount->account_balance, $recharge->fund), 2);
            $user_acount->available_balance = $bc->bcround(bcadd($user_acount->available_balance, $recharge->fund), 2);
            $user_acount->in_sum = $bc->bcround(bcadd($user_acount->in_sum, $recharge->fund), 2);

            if (!$user_acount->save()) {
                $transaction->rollBack();

                return false;
            }
            
            $message = [
                $user->real_name,
                $recharge->fund
            ];
            $sms = new SmsMessage([
                'uid' => $user->id,
                'template_id' => Yii::$app->params['sms']['recharge'],
                'mobile' => $user->mobile,
                'message' => json_encode($message)
            ]);
            $sms->save();

            $transaction->commit();

            return true;
        }

        return false;
    }

    public function actionJiesuancallback()
    {
        $message = \Yii::$app->request->post('message');
        $signature = \Yii::$app->request->post('signature');
        $payment = new Payment();
        $plainText = trim(base64_decode($message));
        $simpleXML = new \SimpleXMLElement($plainText);

        $req = new Request1348(
                $simpleXML->Body->InstitutionID,
                $simpleXML->Body->SerialNumber,
                $simpleXML->Body->OrderNo,
                $simpleXML->Body->Amount,
                $simpleXML->Body->Status,
                $simpleXML->Body->TransferTime,
                $simpleXML->Body->SuccessTime
        );

        $orderNo = $req->getOrderNo(); //支付交易流水号
        $recharge = RechargeRecord::findOne(['sn' => $orderNo]);
        $user = User::findOne($recharge->uid);

        //录入日志信息
        $trade_log = new TradeLog($user, $req, null);

        $xml_path = Yii::getAlias('@common').'/config/xml/cfca_response.xml';
        $xmlresponse = file_get_contents($xml_path);
        $responseXML = new \SimpleXMLElement($xmlresponse);
        $code = '2000';
        $errInfo = 'OK';

        //验证签名
        $ok = $payment->cfcaverify($plainText, $signature);
        if ($ok != 1) {
            //签名失败，返回错误信息
            $code = '2002';
            $errInfo = '验签失败';
        } else {
            //签名成功
            $txCode = $req->getTxCode();
            $InstitutionID = $req->getInstitutionId(); //获取返回的机构编号

            if ($InstitutionID != \Yii::$app->params['cfca']['institutionId']) {
                $code = '2001';
                $errInfo = '错误的机构编码';
            } else {
                if ($txCode === 1348) {
                    $serialNumber = $req->getSerialNumber();
                    $status = intval($req->getStatus());
                    $jiesuan = Jiesuan::findOne(['sn' => $serialNumber]);

                    if (empty($recharge) || empty($jiesuan)) {
                        $txCode = '2001';
                        $errInfo = '不正确的充值单据';
                    } else {
                        $jiesuan->status = ($status === 40) ? (Jiesuan::STATUS_ACCEPT) : $status;
                        $recharge->settlement = ($status === 40) ? (RechargeRecord::SETTLE_ACCEPT) : $status;
                        $transcation = Yii::$app->db->transaction;
                        if (!$recharge->save() || !$jiesuan->save()) {
                            $transcation->rollBack();
                            $txCode = '2001';
                            $errInfo = '数据库错误';
                        } else {
                            $transcation->commit();
                        }
                    }
                } else {
                    $code = '2001';
                    $errInfo = '调用接口错误';
                }
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

        echo $base64Str;
    }
}
