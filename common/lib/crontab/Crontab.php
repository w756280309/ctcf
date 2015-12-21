<?php
/**
 * User: xmac
 * Date: 15-7-13
 * Time: 下午4:04
 */

namespace common\lib\crontab;
use common\models\user\Batchpay;
use common\lib\cfca\Payment;
use common\models\user\CfcaLog;
use common\models\product\OnlineProduct;
use common\models\user\Jiesuan;
use common\models\user\RechargeRecord;
use Yii;
class Crontab{
    
    /**
     * $crontab = new \common\lib\crontab\Crontab();
     * $crontab->pay();
     * 定时多久跑一次  3天内
     */
    public function pay(){
        $start = time()-60*60*24*3;//计算读取3天以内的未成功的
        $batch = Batchpay::find()->where(['status'=> [Batchpay::STATUS_WEI,Batchpay::STATUS_NOW]])->andFilterWhere(['>','updated_at',$start])->all();
        $payment = new Payment();
        foreach ($batch as $pay){
            $response_code = '';
            $simpleXML = new \SimpleXMLElement(\Yii::$app->params['cfca']['xmltx1520']);
            $simpleXML->Body->InstitutionID = \Yii::$app->params['cfca']['InstitutionID'];
            $simpleXML->Body->BatchNo = $pay->sn;
            $xmlStr = $simpleXML->asXML();
            $message = base64_encode(trim($xmlStr));
            $signature = $payment->cfcasign_pkcs12(trim($xmlStr));
            $response=$payment->cfcatx_transfer($message,$signature);	
            $plainText=trim(base64_decode($response[0]));
            $ok=$payment->cfcaverify($plainText,$response[1]);
            if($ok!=1)
            {
                $response = \Yii::$app->functions->createXmlResponse('2002',"验签失败");
                $response_code='2002';
            }else{
                $response = $plainText;
                $responseXml = new \SimpleXMLElement($plainText);
                $response_code = $responseXml->Head->Code;
                if($response_code=='250001'){//未找到对应批次号
                    $pay->status =  Batchpay::STATUS_FAIL;
                    $pay->save();
                }else if($response_code=="2000"){
                    $item = $responseXml->Body->Item;
                    //echo $item->ItemNo;
                    if($pay->status!=$item->Status){
                        $pay->status =  $item->Status;
                        $pay->banktxtime =  $item->BankTxTime;
                        $pay->save();
                        var_dump($pay->getErrors());
                    }
                }
                //记录日志
                $cfcalog = new CfcaLog();
                $cfcalog->type = CfcaLog::TYPE_BATCHPAY_CRONTAB;
                $cfcalog->uid = $pay->uid;
                $cfcalog->log_type = 1;
                $cfcalog->response_code=$response_code;
                $cfcalog->response = $response;
                //$cfcalog->save();
            }
        }
        echo 'success';
    }
    
    
    /**
     * $date格式'2015-7-18'
     * 批量代付交易对账
     */
    public function batchDui($date='2015-7-18') {
        $simpleXML = new \SimpleXMLElement(\Yii::$app->params['cfca']['xmltx1550']);
        
        $simpleXML->Body->InstitutionID = \Yii::$app->params['cfca']['InstitutionID'];
        $simpleXML->Body->Date = $date;
        $xmlStr = $simpleXML->asXML();
        $message = base64_encode(trim($xmlStr));
        $payment = new Payment();
        $signature = $payment->cfcasign_pkcs12(trim($xmlStr));
        $response=$payment->cfcatx_transfer($message,$signature);	
        $plainText=trim(base64_decode($response[0]));
        $ok=$payment->cfcaverify($plainText,$response[1]);
        if($ok!=1)
        {
            $response = \Yii::$app->functions->createXmlResponse('2002',"验签失败");
        }else{
            $response = $plainText;
            
        }
        echo $response;
    }
    
    public function updateOlproStatus(){
        $prolist = OnlineProduct::find()->where(['status'=>  OnlineProduct::STATUS_PRE])->andFilterWhere([">",'start_date',  time()])->all();
        foreach($prolist as $pro){
            $pro->scenario = 'status';
            $pro->status=  OnlineProduct::STATUS_NOW;
            $pro->save();
        }
        $prolistdown = OnlineProduct::find()->where(['status'=> [OnlineProduct::STATUS_PRE,OnlineProduct::STATUS_NOW,OnlineProduct::STATUS_FOUND]])->andFilterWhere(["<",'end_date',  time()])->all();
        foreach($prolist as $pro){
            $pro->scenario = 'status';
            $pro->status=  OnlineProduct::STATUS_NOW;
            $pro->save();
        }
        foreach($prolistdown as $pro){
            $pro->scenario = 'status';
            $pro->status=  OnlineProduct::STATUS_LIU;
            $pro->save();
            $order = new \common\models\order\OnlineOrder();
            $order->cancelOnlinePro($pro->id);
        }
        echo "success";
    }
    
    /**
     * 结算查询，防止中金掉单行为每日11点至15点查询
     */
    public function settlement(){
        $start = time()-60*60*24*3;//计算读取3天以内的未成功的
        $batch = Jiesuan::find()->andFilterWhere(['>','updated_at',$start])->all();
        $payment = new \common\lib\cfca\Payment();
        $InstitutionID = \Yii::$app->params['cfca']['InstitutionID'];
        $xmltx1350 = \Yii::$app->params['cfca']['xmltx1350'];
        $jmodel = new Jiesuan();
        foreach($batch as $jiesuan){
            $jmodel = clone $jiesuan;
                    //var_dump($xmltx1341);
            $simpleXML= new \SimpleXMLElement($xmltx1350);
            // 4.赋值
            $simpleXML->Body->InstitutionID=$InstitutionID;//'000020'
            $simpleXML->Body->SerialNumber=$jmodel->sn;
            $xmlStr = $simpleXML->asXML();//echo $xmlStr;exit;

            $message=base64_encode(trim($xmlStr));
            $signature=$payment->cfcasign_pkcs12(trim($xmlStr));
            $response=$payment->cfcatx_transfer($message,$signature);
            $plainText=trim(base64_decode($response[0]));
            $ok=$payment->cfcaverify($plainText,$response[1]);
            if($ok!=1){
            }else{
                $responseXML= new \SimpleXMLElement($plainText);
                $old_status = intval($jmodel->status);
                $new_status = intval($responseXML->Body->Status);
                if($old_status!=$new_status){
                    $jmodel->status=$new_status;
                    $jmodel->save();
                    if($jmodel->type==1){
                        $rr = RechargeRecord::findOne(['sn'=>$jmodel->osn]);
                        $rr->settlement=$new_status;
                        $rr->save();
                        //记录日志
                         $cfcalog = new CfcaLog();
                         $cfcalog->type = CfcaLog::TYPE_SETTLEMENT;
                         $cfcalog->uid = $rr->uid;
                         $cfcalog->log_type = 2;
                         $cfcalog->response_code='1350';
                         $cfcalog->response = $plainText;
                         $cfcalog->save();
                    }
                }
                
            }    
        }
        
    }
    
}

