<?php
/**
 * 定时任务文件.
 * User: xmac
 * Date: 15-3-19
 * Time: 下午3:51.
 */
namespace app\controllers;

use Yii;
use yii\web\Controller;

class WapbaseController extends Controller
{
    public $enableCsrfValidation = false; //因为中金post的提交。所以要关闭csrf验证

    public function actionJiesuancallback()
    {
        //接收中金上传报文信息并解析
        $message = \Yii::$app->request->post('message');
        $signature = \Yii::$app->request->post('signature');
        $payment = new \common\lib\cfca\Payment();
        $plainText = trim(base64_decode($message));
        $simpleXML = new \SimpleXMLElement($plainText);

        //录入日志信息
        $trade_log = new \common\models\TradeLog([
            'tx_code' => $simpleXML->Head->TxCode,
            'tx_sn' => $simpleXML->Body->SerialNumber,
            'pay_id' => 0,
            'uid' => 0,
            'account_id' => 0,
            'request' => $plainText,
        ]);

        $xml_path = Yii::getAlias('@common').'/config/xml/cfca_response.xml';
        $xmlresponse = file_get_contents($xml_path);
        $responseXML = new \SimpleXMLElement($xmlresponse);
        $code = '2000';
        $errInfo = 'OK';

        //验证签名
        $ok = $payment->cfcaverify($plainText, $signature);
        //$ok = 1;
        if ($ok != 1) {
            //签名失败，返回错误信息
            $code = '2002';
            $errInfo = '验签失败';
        } else {
            //签名成功
            $txCode = $simpleXML->Head->TxCode;
            if ($txCode == '1348') {
                $SerialNumber = $simpleXML->Body->SerialNumber; //获取 原结算交易流水号
                $OrderNo = $simpleXML->Body->OrderNo; //获取 结算订单号
                $Status = $simpleXML->Body->Status; //获取 返回状态
                //file_put_contents('zuo.txt', $SerialNumber."\n");
                $jiesuan = \common\models\user\Jiesuan::findOne(['sn' => $SerialNumber]);
                if ($jiesuan) {
                    $jiesuan->status = $Status == 40 ? (\common\models\user\Jiesuan::STATUS_ACCEPT) : $Status;
                    $jiesuan->save();
                } else {
                    $code = '2001';
                    $errInfo = '不正确的充值单据';
                }

                $recharge = \common\models\user\RechargeRecord::findOne(['sn' => $OrderNo]);
                if ($recharge) {
                    //记录日志信息
                    $trade_log->uid = $recharge->uid;
                    $trade_log->account_id = $recharge->account_id;

                    $recharge->settlement = $Status == 40 ? (\common\models\user\RechargeRecord::SETTLE_ACCEPT) : $Status;
                    $recharge->save();
                } else {
                    $code = '2001';
                    $errInfo = '不正确的充值单据';
                }
            } else {
                $code = '2001';
                $errInfo = '调用接口错误';
            }
        }

        $responseXML->Head->Code = $code;
        $responseXML->Head->Message = $errInfo;
        //file_put_contents('zuo.txt', $errInfo, FILE_APPEND);
        $responseXMLStr = $responseXML->asXML();
        $base64Str = base64_encode(trim($responseXMLStr));
        //$signature=$payment->cfcasign_pkcs12(trim($responseXMLStr));

        //记录日志信息
        $trade_log->response_code = $code;
        $trade_log->response = $responseXMLStr;
        $trade_log->save();
//        file_put_contents('zuo.txt', $code."\n");
//        file_put_contents('zuo.txt', $responseXMLStr);
        echo $base64Str;
    }
}
