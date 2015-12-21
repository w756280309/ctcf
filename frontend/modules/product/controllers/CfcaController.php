<?php

namespace app\modules\product\controllers;

use Yii;
use yii\web\Controller;
use common\models\product\OnlineProduct;
use common\models\product\ProductCategory;
use common\models\product\ProductField;
use common\models\user\UserAccount;
use common\models\user\MoneyRecord;
use common\models\order\OnlineOrder;
use common\models\contract\ContractTemplate;
use common\models\contract\Contract;
use common\models\order\OnlineRepaymentPlan;
use common\lib\product\ProductProcessor;
use common\lib\cfca\Payment;
use common\models\user\RechargeRecord;
use common\lib\bchelp\BcRound;
use common\lib\crontab\Crontab;
use common\models\user\Jiesuan;
use common\models\user\CfcaLog;

class CfcaController extends Controller {

    /**
     * 中金20分钟一次，一共补发5次
     */
    public $enableCsrfValidation = false; //因为中金post的提交。所以要关闭csrf验证

    public function actionRechargebackcallback() {

//        $filename = \Yii::getAlias('@frontend') . "/web/text.txt";
//        $content = file_get_contents($filename) . '\r\n';
//        $fp = fopen($filename, "w"); //文件被清空后再写入 

        $message = \Yii::$app->request->post('message');
        $signature = Yii::$app->request->post("signature");
        $payment = new Payment();

        $responseXML = new \SimpleXMLElement(\Yii::$app->params['cfca']['xmlNotification']);
        $plainText = trim(base64_decode($message));

        $cfcalog = new \common\models\user\CfcaLog();
        $cfcalog->type = 0;
        $cfcalog->account_id = 0;
        $cfcalog->uid = 0;
        $cfcalog->log_type = 2;
        $cfcalog->response = $plainText;
        $cfcalog->save();
        //var_dump($cfcalog->getErrors());    
        $ok = $payment->cfcaverify($plainText, $signature);
        $content .= '';
        if ($ok != 1) {
            $errInfo = "验签失败";
            $responseXML->Head->Code = "2001";
            $responseXML->Head->Message = $errInfo;
            $content .= $errInfo;
        } else {
            $txName = "";
            $simpleXML = new \SimpleXMLElement($plainText);
            $txCode = $simpleXML->Head->TxCode;
            if ($txCode == "1118") {
                //！！！ 在这里添加商户处理逻辑！！！
                //以下为演示代码
                $txName = "商户订单支付状态变更通知";
            } else if ($txCode == "1119") {
                //！！！ 在这里添加商户处理逻辑！！！
                //以下为演示代码
                $txName = "商户订单支付状态变更通知";
            } else if ($txCode == "1138") {
                //！！！ 在这里添加商户处理逻辑！！！
                //以下为演示代码
                $txName = "商户订单退款结算状态变更通知";
            } else if ($txCode == "1318") {
                $InstitutionID = $simpleXML->Body->InstitutionID; //获取返回的机构编号
                //$content .= empty($simpleXML->Body->InstitutionID)?"kong":$simpleXML->Body->InstitutionID;
                if ($InstitutionID != \Yii::$app->params['cfca']['InstitutionID']) {
                    
                } else {
                    $Status = intval($simpleXML->Body->Status); //获取返回结果 状态： 10=未支付 20=已支付
                    $BankNotificationTime = $simpleXML->Body->BankNotificationTime; //获取返回支付平台收到银行通知时间
                    $Amount = $simpleXML->Body->Amount; //支付金额，单位：分
                    $PaymentNo = $simpleXML->Body->PaymentNo; //支付交易流水号
                    $rechareg = RechargeRecord::findOne(['sn' => $PaymentNo]);
                    if (empty($rechareg)) {
                        //$content .= ('不正确的充值单据[' . $PaymentNo . ']');
                        $responseXML->Head->Code = "2001";
                        $responseXML->Head->Message = '不正确的充值单据';
                    } else if ($rechareg->status == RechargeRecord::STATUS_YES) {
                        $responseXML->Head->Code = "2000";
                        $responseXML->Head->Message = "OK";
                    } else if ($Status == 20) {
                        $uid = $rechareg->uid;
                        //$ua = UserAccount::getUserAccount($uid);
                        $ua = UserAccount::findOne($rechareg->account_id); //以当前充值记录需要冲到的账户进行充值
                        $bcround = new \common\lib\bchelp\BcRound();
                        $jiesuan = new \common\models\user\Jiesuan();
                        $cfcalog = new \common\models\user\CfcaLog();
                        bcscale(14);
                        $transaction = Yii::$app->db->beginTransaction();

                        $cfcalog->type = 3;
                        $cfcalog->account_id = $ua->id;
                        $cfcalog->uid = $uid;
                        $cfcalog->log_type = 2;
                        $cfcalog->response = $plainText;

                        if (!$cfcalog->save()) {
                            $transaction->rollBack();
                            $content .= ('cfca状态异常');
                        }

                        $rechareg->status = RechargeRecord::STATUS_YES;
                        $rechareg->bankNotificationTime = $BankNotificationTime;
                        if (!$rechareg->save()) {
                            $transaction->rollBack();
                            $content .= ('修改充值状态异常');
                        }

                        $ua->account_balance = $bcround->bcround(bcadd($ua->account_balance, bcdiv($Amount, 100)), 2); //因为amount以分为单位。所以除以100
                        $ua->available_balance = $bcround->bcround(bcadd($ua->available_balance, bcdiv($Amount, 100)), 2); //因为amount以分为单位。所以除以100
                        $ua->in_sum = $bcround->bcround(bcadd($ua->in_sum, bcdiv($Amount, 100)), 2); //因为amount以分为单位。所以除以100
                        if (!$ua->save()) {
                            $transaction->rollBack();
                            $content .= ('资金记录异常');
                        }

                        $mr_model = new MoneyRecord();
                        $mr_model->sn = MoneyRecord::createSN();
                        $mr_model->osn = $PaymentNo;
                        $mr_model->type = MoneyRecord::TYPE_RECHARGE;
                        $mr_model->account_id = $ua->id;
                        $mr_model->uid = $uid;
                        $mr_model->balance = $ua->available_balance;
                        $mr_model->remark = "资金流水号:" . $mr_model->sn . ',充值流水号:' . $PaymentNo . ',账户余额:' . ($ua->account_balance) . '元，可用余额:' . ($ua->available_balance) . '元，冻结金额:' . $ua->freeze_balance . '元。';
                        $mr_model->status = MoneyRecord::STATUS_SUCCESS;
                        $mr_model->in_money = bcdiv($Amount, 100, 2) * 1;
                        $mrre = $mr_model->save();
                        //var_dump($mr_model->getErrors());
                        if (!$mrre) {
                            $transaction->rollBack();
                            $content .= ('资金记录异常');
                        }
                        $jiesuan->amount = bcdiv($Amount, 100, 2) * 1;
                        $jiesuan->osn = $rechareg->sn;
                        $jiesuan->type = 1;
                        $jiesuan->bankNotificationTime = $BankNotificationTime;
                        $res = $jiesuan->settlement();
                        if ($res === FALSE) {
                            $transaction->rollBack();
                        }
                        $transaction->commit();
                    }
                }
                //以下为演示代码
                $txName = "市场订单支付状态变更通知";
            } else if ($txCode == "1348") {
                //！！！ 在这里添加商户处理逻辑！！！
                $InstitutionID = $simpleXML->Body->InstitutionID; //获取返回的机构编号
                $SerialNumber = $simpleXML->Body->SerialNumber; //
                $OrderNo = $simpleXML->Body->OrderNo; //
                $Amount = $simpleXML->Body->Amount; //
                $Status = $simpleXML->Body->Status;
                $TransferTime = $simpleXML->Body->TransferTime;
                $SuccessTime = $simpleXML->Body->SuccessTime;
                $ErrorMessage = $simpleXML->Body->ErrorMessage;
                $jiesuan = Jiesuan::findOne(['sn' => $SerialNumber]);
                if ($jiesuan) {
                    $jiesuan->status = $Status;
                    $jiesuan->save();
//                    if($jiesuan->status==$Status){
//                        $responseXML->Head->Code = "2002";
//                        $responseXML->Head->Message = '已经结算,40=已经发出结算 50=转账退回（结算撤销）,状态：'.$Status;
//                    }else{
//                        $jiesuan->status=$Status;
//                        $jiesuan->save();
//                    }
                    $rr = RechargeRecord::findOne(['sn' => $OrderNo]);
                    $cfcalog = new \common\models\user\CfcaLog();
                    $cfcalog->type = 4;
                    $cfcalog->account_id = $rr->account_id;
                    $cfcalog->uid = $rr->uid;
                    $cfcalog->log_type = 2;
                    $cfcalog->response = $plainText;
                    $cfcalog->save();
                } else {
                    $responseXML->Head->Code = "2001";
                    $responseXML->Head->Message = '不正确的充值单据';
                }
                \Yii::$app->functions->createXmlResponse('2002', $plainText);

                //$responseXMLStr = $responseXML->asXML();、
                $txName = "市场订单结算状态变更通知";
            } else if ($txCode == "1363") {
                //！！！ 在这里添加商户处理逻辑！！！
                //以下为演示代码
                $txName = "市场订单单笔代收结果通知";
            } else if ($txCode == "1712") {
                //！！！ 在这里添加商户处理逻辑！！！
                //以下为演示代码
                $txName = "预授权成功结果通知";
            } else if ($txCode == "1722") {
                //！！！ 在这里添加商户处理逻辑！！！
                //以下为演示代码
                $txName = "预授权撤销结果通知";
            } else if ($txCode == "1732") {
                //！！！ 在这里添加商户处理逻辑！！！
                //以下为演示代码
                $txName = "预授权扣款结果通知";
            } else if ($txCode == "2018") {
                //！！！ 在这里添加商户处理逻辑！！！
                //以下为演示代码
                $txName = "实时代扣结果通知";
            } else if ($txCode == "3218") {
                //！！！ 在这里添加商户处理逻辑！！！
                //以下为演示代码
                $txName = "P2P支付成功通知（托管户）";
            } else if ($txCode == "4233") {
                //！！！ 在这里添加商户处理逻辑！！！
                //以下为演示代码
                $txName = "用户支付账户注册成功通知";
            } else if ($txCode == "4243") {
                //！！！ 在这里添加商户处理逻辑！！！
                //以下为演示代码
                $txName = "用户支付账户银行账户绑定成功通知（托管户）";
            } else if ($txCode == "4247") {
                //！！！ 在这里添加商户处理逻辑！！！
                //以下为演示代码
                $txName = "用户支付账户银行账户解绑成功通知（托管户）";
            } else if ($txCode == "4253") {
                //！！！ 在这里添加商户处理逻辑！！！
                //以下为演示代码
                $txName = "用户支付账户充值成功通知（托管户）";
            } else if ($txCode == "4257") {
                //！！！ 在这里添加商户处理逻辑！！！
                //以下为演示代码
                $txName = "用户支付账户提现成功通知（托管户）";
            } else if ($txCode == "4263") {
                //！！！ 在这里添加商户处理逻辑！！！
                //以下为演示代码
                $txName = "用户支付账户扣款签约成功通知（托管户）";
            } else {
                $txName = "未知通知类型";
            }
            $content .= $txCode . "_" . $txName;
            $responseXML->Head->Code = "2000";
            $responseXML->Head->Message = "OK";
        }

        // 商户自身逻辑处理完成之后,需要向支付平台返回响应
        $responseXMLStr = $responseXML->asXML();
        $base64Str = base64_encode(trim($responseXMLStr));
//        if ($fp) {
//            $flag = fwrite($fp, $content . $base64Str.'-----'.  date('Y-m-d H:i:s'));
//            //echo "写入字符";
//        } else {
//            //echo "打开文件失败";
//        }
//        fclose($fp);
        print $base64Str;
//        exit;
//          HttpResponse::status(200);
//          HttpResponse::setContentType('text/plain');
//          HttpResponse::setData($base64Str);
//          HttpResponse::send();
    //
    }

    /**
     * 结算11-15点
     */
    public function actionSettlement() {
        $crontab = new Crontab();
        $crontab->settlement();
    }

    /* 批量代付，确定卡的有效性，每日晚上24时 */

    public function actionBatchpay() {
        $crontab = new Crontab();
        $crontab->pay();
    }

    /**
     * 充值自己主动去查
     */
    public function actionRechareg() {

        $list = RechargeRecord::find()->where(['status' => 0])->asArray()->all();
        $InstitutionID = \Yii::$app->params['cfca']['InstitutionID'];
        $bcround = new \common\lib\bchelp\BcRound();
        $transaction = Yii::$app->db->beginTransaction();
        echo "<pre>";
        foreach ($list as $o) {
            $simpleXML = new \SimpleXMLElement(\Yii::$app->params['cfca']['xmltx1320']);
            $simpleXML->Body->InstitutionID = $InstitutionID;
            $simpleXML->Body->PaymentNo = $o['sn'];

            $xmlStr = $simpleXML->asXML();
            $message = base64_encode(trim($xmlStr));
            $payment = new Payment();
            $signature = $payment->cfcasign_pkcs12(trim($xmlStr));
            $response = $payment->cfcatx_transfer($message, $signature);
            $plainText = trim(base64_decode($response[0]));
            $ok = $payment->cfcaverify($plainText, $response[1]);
            if ($ok != 1) {
                
            } else {
                $responseXML = new \SimpleXMLElement($plainText);
                $pay_status = intval($responseXML->Body->Status);
                $Amount = $responseXML->Body->Amount; //支付金额，单位：分
                $BankNotificationTime = $responseXML->Body->BankNotificationTime; //获取返回支付平台收到银行通知时间
                $PaymentNo = $responseXML->Body->PaymentNo; //支付交易流水号
                if ($pay_status == 20 && $o['status'] == 0) {
                    //记录中金返回响应
                    $cfcalog = new CfcaLog();
                    $cfcalog->type = 2;
                    $cfcalog->account_id = $o['account_id'];
                    $cfcalog->uid = $o['uid'];
                    $cfcalog->log_type = 2;
                    $cfcalog->response = $plainText;
                    $logre = $cfcalog->save();
                    if (!$logre) {
                        $transaction->rollBack();
                        return false;
                    }
                    $ua = UserAccount::findOne($o['account_id']); //以当前充值记录需要冲到的账户进行充值

                    $jiesuan = new \common\models\user\Jiesuan();
                    bcscale(14);
                    $rrres = RechargeRecord::updateAll(['status'=>RechargeRecord::STATUS_YES,'bankNotificationTime'=>$BankNotificationTime], ['id'=>$o['id']]);
                    if (!$rrres) {
                        $transaction->rollBack();
                        return false;
                    }

                    $ua->account_balance = $bcround->bcround(bcadd($ua->account_balance, bcdiv($Amount, 100)), 2); //因为amount以分为单位。所以除以100
                    $ua->available_balance = $bcround->bcround(bcadd($ua->available_balance, bcdiv($Amount, 100)), 2); //因为amount以分为单位。所以除以100
                    $ua->in_sum = $bcround->bcround(bcadd($ua->in_sum, bcdiv($Amount, 100)), 2); //因为amount以分为单位。所以除以100
                    if (!$ua->save()) {
                        $transaction->rollBack();
                        return false;
                    }

                    $mr_model = new MoneyRecord();
                    $mr_model->sn = MoneyRecord::createSN();
                    $mr_model->osn = $PaymentNo;
                    $mr_model->type = MoneyRecord::TYPE_RECHARGE;
                    $mr_model->account_id = $ua->id;
                    $mr_model->uid = $o['uid'];
                    $mr_model->balance = $ua->available_balance;
                    $mr_model->remark = "资金流水号:" . $mr_model->sn . ',充值流水号:' . $PaymentNo . ',账户余额:' . ($ua->account_balance) . '元，可用余额:' . ($ua->available_balance) . '元，冻结金额:' . $ua->freeze_balance . '元。';
                    $mr_model->status = MoneyRecord::STATUS_SUCCESS;
                    $mr_model->in_money = bcdiv($Amount, 100, 2) * 1;
                    $mrre = $mr_model->save();
                    //var_dump($mr_model->getErrors());
                    if (!$mrre) {
                        $transaction->rollBack();
                        return false;
                    }
                    $jiesuan->amount = bcdiv($Amount, 100, 2) * 1;
                    $jiesuan->osn = $o['sn'];
                    $jiesuan->type = 1;
                    $jiesuan->bankNotificationTime = $BankNotificationTime;
                    $res = $jiesuan->settlement();
                    if ($res === FALSE) {
                        $transaction->rollBack();
                        return false;
                    }

                }
            }
        }

        $transaction->commit();
    }

    /**
     * 交易对账单
     */
    public function actionMonthlystatement($date = '2015-10-08'){
        $InstitutionID = \Yii::$app->params['cfca']['InstitutionID'];
        $simpleXML = new \SimpleXMLElement(\Yii::$app->params['cfca']['xmltx1810']);
        $simpleXML->Body->InstitutionID = $InstitutionID;
        $simpleXML->Body->Date = $date;

        $xmlStr = $simpleXML->asXML();
        $message = base64_encode(trim($xmlStr));
        $payment = new Payment();
        $signature = $payment->cfcasign_pkcs12(trim($xmlStr));
        $response = $payment->cfcatx_transfer($message, $signature);
        $plainText = trim(base64_decode($response[0]));
        $ok = $payment->cfcaverify($plainText, $response[1]);
        if ($ok != 1) {

        } else {
            $responseXML = new \SimpleXMLElement($plainText);
            echo "<pre>";
            echo $plainText;
        }
    }
    
}
