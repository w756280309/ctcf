<?php
/**
 * 定时任务文件.
 * User: xmac
 * Date: 15-3-19
 * Time: 下午3:51
 */
namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\models\product\OnlineProduct;
use common\models\order\OnlineOrder;
use \common\models\user\User;
use common\models\user\UserAccount;
use common\lib\bchelp\BcRound;
use common\models\user\MoneyRecord;
use common\models\checkaccount\CheckaccountCfca;
use common\models\checkaccount\CheckaccountWdjf;
use common\models\checkaccount\CheckaccountHz;
use common\models\user\RechargeRecord;
use common\models\user\Jiesuan;
use common\lib\cfca\Payment;
use PayGate\Cfca\Settlement\AccountSettlement;
use PayGate\Cfca\Message\Request1341;
use PayLog\PayLogUtils;
use common\lib\cfca\Cfca;

class CrontabController extends Controller
{

    /**
     * 定时 刷新满标 满标生成还款计划
     */
    public function actionUpdatefull(){
        $data = OnlineProduct::find()->where(['finish_rate'=>1,'status'=>2])->all();
        foreach ($data as $dat){
            $pid = $dat['id'];
            OnlineProduct::updateAll(['status'=>3,'sort'=>OnlineProduct::SORT_FULL],['id'=>$pid]);
            //$orders = OnlineOrder::find()->where(['online_pid'=>$pid,'status'=>  OnlineOrder::STATUS_SUCCESS])->asArray()->select('id,order_money,refund_method,yield_rate,expires,uid,order_time')->all();
            //OnlineRepaymentPlan::createPlan($pid,$orders);//转移到开始计息部分
        }

    }
    /**
     * 定时 修改预告期为募集期
     */
    public function actionUpdatenow(){
        OnlineProduct::updateAll(['status'=>2,'sort'=>OnlineProduct::SORT_NOW],' online_status=1 and status=1 and start_date<='.  time());
    }

    /**
     * 定时 修改募集期状态为流标状态
     */
    public function actionUpdateliu(){
        $product = OnlineProduct::find()->where(['del_status' => OnlineProduct::STATUS_USE, 'online_status' => OnlineProduct::STATUS_ONLINE, 'status' => OnlineProduct::STATUS_NOW]);
        $product = $product->andFilterWhere(['<', 'end_date', time()])->all();
        //var_dump($product);exit;

        $bc = new BcRound();
        bcscale(14);
        $transaction = Yii::$app->db->beginTransaction();
        foreach($product as $val) {
             $order = OnlineOrder::find()->where(['online_pid' => $val['id'], 'status' => OnlineOrder::STATUS_SUCCESS])->all();
             foreach($order as $v) {
                 $ua = UserAccount::findOne(['uid' => $v['uid']]);

                 $ua->freeze_balance = $bc->bcround(bcsub($ua->freeze_balance, $v['order_money']),2);
                 $ua->available_balance = $bc->bcround(bcadd($ua->available_balance, $v['order_money']),2);
                 $ua->out_sum = $bc->bcround(bcsub($ua->out_sum, $v['order_money']),2);

                 if(!$ua->save()) {
                     $transaction->rollBack();
                     return false;
                 }

                 $v->status = OnlineOrder::STATUS_CANCEL;
                 if(!$v->save()) {
                     $transaction->rollBack();
                     return false;
                 }

                 $money_record = new MoneyRecord();
                 $money_record->sn = MoneyRecord::createSN();
                 $money_record->type = MoneyRecord::TYPE_ORDER;
                 $money_record->osn = $v->sn;
                 $money_record->account_id = $ua->id;
                 $money_record->uid = $v['uid'];
                 $money_record->balance = $ua->available_balance;
                 $money_record->in_money = $v['order_money'];
                 $money_record->status = MoneyRecord::STATUS_REFUND;

                 if(!$money_record->save()) {
                     $transaction->rollBack();
                     return false;
                 }
             }

             $val->scenario = 'status';
             $val->status = OnlineProduct::STATUS_LIU;
             if(!$val->save()) {
                 $transaction->rollBack();
                 return false;
             }
        }

        if($product) {
            $transaction->commit();
            return true;
        }
        echo 1;
        return false;
    }

    /**
     * 发起今日结算请求【建议频率高些】
     */
    public function actionLaunchsettlement(){
        $data = RechargeRecord::find()->where(['status'=>1,'settlement'=>0])->limit(1)->all();//找到所有未结算的
        foreach($data as $dat){
            $asettlement = new AccountSettlement($dat);
            $rq1341 = new Request1341(Yii::$app->params['cfca']['institutionId'] , $asettlement);

            $jiesuan = new Jiesuan([
                'sn' => $rq1341->getSettlementSn(),
                'osn' => $dat->sn,
                'pay_id' => 0,//0代表中金
                'type' => 1,
                'amount' => $dat->fund,
                'bank_id' => Request1341::BANK_ID,//本平台赋予的银行的id
                'pay_bank_id' => Request1341::BANK_ID,//支付公司银行id，
                'accountname' => Request1341::ACCOUNT_NAME,
                'accountnumber' => Request1341::ACCOUNT_NUMBER,
                'branchname' => Request1341::BRANCH_NAME,
                'province' => Request1341::PROVINCE,
                'city' => Request1341::CITY
            ]);
            if ($jiesuan->validate()&&$jiesuan->save()) {//成功之后发起结算
                $cfca = new Cfca();
                $resp = $cfca->request($rq1341);

                $cpuser = User::findOne($dat->uid);
                //记录日志
                $log = new PayLogUtils($cpuser,$rq1341,$resp);
                $log->buildLog();

                if ($resp->isSuccess()) {
                    RechargeRecord::updateAll(['settlement' => RechargeRecord::SETTLE_ACCEPT], ['id' => $dat->id]);//修改为已经受理
                }
            }
        }
    }

    /**
     * 批处理结算订单的状态修改【建议频率高些】
     */
    public function actionBatchsettlement(){
        $data = Jiesuan::find()->where(['status'=>  [Jiesuan::STATUS_NO,Jiesuan::STATUS_ACCEPT,Jiesuan::STATUS_IN]])->select('id,sn,osn,amount')->all();//
        if(!empty($data)){
            $xml_path = Yii::getAlias('@common')."/config/xml/cfca_1350.xml";
            $xmltx1350 = file_get_contents($xml_path);
            $InstitutionID = \Yii::$app->params['cfca']['institutionId'];//获取机构ID
            $payment = new Payment();
            $date = date("Y-m-d",strtotime("-1 day"));//获取前日
            foreach ($data as $dat){
                $simpleXML = new \SimpleXMLElement($xmltx1350);
                $simpleXML->Body->InstitutionID = $InstitutionID;
                $simpleXML->Body->SerialNumber = $dat->sn;
                $xmlStr = $simpleXML->asXML();
                $message = base64_encode(trim($xmlStr));

                $signature = $payment->cfcasign_pkcs12(trim($xmlStr));
                $response = $payment->cfcatx_transfer($message, $signature);

                $plainText = (base64_decode($response[0]));
                $ok = $payment->cfcaverify($plainText, $response[1]);
                if($ok==1){//中金验签返回成功
                    $responseXML = new \SimpleXMLElement($plainText);
                    $response_code = $responseXML->Head->Code;
                    if($response_code=="2000"){//数据成功返回
                        $status = $responseXML->Body->Status;
                        if($status==40||$status==50){//40 已经提交成功 50 转账退款
                            Jiesuan::updateAll(['status'=>$status],['id'=>$dat->id]);
                            RechargeRecord::updateAll(['settlement'=>$status],['sn'=>$dat->osn]);
                            $wdjf_model = new CheckaccountWdjf(['order_no'=>$dat->osn,'tx_date'=>$date,'tx_type'=>1341,'tx_sn'=>$dat->sn,'tx_amount'=>($dat->amount),'payment_amount'=>0,'institution_fee'=>0,'bank_notification_time'=>'0']);
                            $wdjf_model->save();
                            //var_dump($wdjf_model->validate(),$wdjf_model->getErrors());
                        }
                    }
                }else{
                    //验签失败处理
                }
            }
        }else{

        }

    }

    /**
     * 获取中金对账单【中金每日凌晨5时生成上一日对账单】
     * 建议在凌晨5时之后执行上一日的对账单
     */
    public function actionGetcfcacheckaccount(){
        $date = date("Y-m-d",strtotime("-1 day"));//获取前日
        //$date = date("Y-m-d");//获取今日
        $xml_path = Yii::getAlias('@common')."/config/xml/cfca_1810.xml";
        $xmltx1810 = file_get_contents($xml_path);
        $InstitutionID = \Yii::$app->params['cfca']['institutionId'];//获取机构ID
        $simpleXML = new \SimpleXMLElement($xmltx1810);
        $simpleXML->Body->InstitutionID = $InstitutionID;
        $simpleXML->Body->Date = $date;

        $xmlStr = $simpleXML->asXML();
        $message = base64_encode(trim($xmlStr));
        $payment = new \common\lib\cfca\Payment();
        $signature = $payment->cfcasign_pkcs12(trim($xmlStr));
        $response = $payment->cfcatx_transfer($message, $signature);

        $plainText = (base64_decode($response[0]));
        $ok = $payment->cfcaverify($plainText, $response[1]);
//        print_r($ok);
//        print_r($plainText);exit;
        if($ok==1){//中金验签返回成功
            $is_write = CheckaccountCfca::find()->where(['tx_date'=>$date])->count('id');
            if($is_write){
                return FALSE;
            }else{
                $responseXML = new \SimpleXMLElement($plainText);
                $response_code = $responseXML->Head->Code;
                if($response_code=="2000"){//数据成功返回
                    $connection = \Yii::$app->db;
                    $ar = $responseXML->children();
                    $data = array();
                    $time = time();
                    foreach ($ar->Body->Tx as $tx){
                        $txsn = strval($tx->TxSn);
                        $str = empty($tx->BankNotificationTime)?0:$tx->BankNotificationTime;
                        if($str){
                            $year = substr($str, 0, 4);
                            $month = substr($str, 4, 2);
                            $day = substr($str, 6, 2);
                            $h = substr($str, 8, 2);
                            $i = substr($str, 10, 2);
                            $s = substr($str, 12, 2);
                            $banknotificationtime = $year.'-'.$month.'-'.$day.' '.$h.':'.$i.':'.$s;
                        }else{
                            $banknotificationtime = 0;
                        }
                        //echo $banknotificationtime;
                        $data[]=[$date,$tx->TxType,$txsn,bcdiv($tx->TxAmount,100),$tx->PaymentAmount,$tx->InstitutionAmount,$banknotificationtime,$time,$time];
                    }
                    if(!empty($data)){
                        $res = $connection->createCommand()->batchInsert(CheckaccountCfca::tableName(), ['tx_date', 'tx_type','tx_sn','tx_amount','payment_amount','institution_fee','bank_notification_time','created_at','updated_at'],
                                $data)->execute();
                        if($res){

                        }else{
                            /////失败代码处理
                        }
                    }
                }else{//中金返回失败
                    /////失败代码处理
                }
            }
        }else{
            /////验签失败代码处理
        }

    }

    /**
     * 获取温都金服充值订单前一日【结算在结算定时任务中完成】
     * 建议在凌晨0点至5点之间运行。要保证5点之前执行完毕
     */
    public function actionGetwdjfcheckaccount(){
        $date = date("Y-m-d",strtotime("-1 day"));//获取前日
        echo $date;
        $beginYesterday=mktime(0,0,0,date('m'),date('d')-1,date('Y'));
        $endYesterday=mktime(0,0,0,date('m'),date('d'),date('Y'))-1;
        $is_write = CheckaccountWdjf::find()->where(['tx_date'=>$date])->count('id');
        if($is_write){
            return FALSE;
        }

        $dataobj = RechargeRecord::find()->where(['status'=>RechargeRecord::STATUS_YES])->andFilterWhere(['between','bankNotificationTime',date('Y-m-d H:i:s',$beginYesterday),date('Y-m-d H:i:s',$endYesterday)])->all();
        //var_dump($dataobj);exit;
        $insert_arr = array();
        $time = time();
        foreach($dataobj as $dat){
            $insert_arr[]=[$dat->sn,$date,1341,$dat->sn,$dat->fund,0,0,$dat->bankNotificationTime,  $time,$time];
        }
        if(!empty($insert_arr)){
            $connection = \Yii::$app->db;
            $res = $connection->createCommand()->batchInsert(CheckaccountWdjf::tableName(), ['order_no','tx_date', 'tx_type','tx_sn','tx_amount','payment_amount','institution_fee','bank_notification_time','created_at','updated_at'],
                    $insert_arr)->execute();
            if($res){

            }else{
                /////失败代码处理
                echo "fail";
            }
        }
        return true;
    }

    /**
     * 温度金服的对账单与中金对账单做比对【建议在凌晨5点之后进行】
     */
    public function actionComparebill(){
        $date = date("Y-m-d",strtotime("-1 day"));//获取前日
        //echo $date;
        $list = (new \yii\db\Query())
                ->select('w.*,c.tx_amount c_tx_amount')
                ->from([CheckaccountWdjf::tableName().' as w'])
                ->innerJoin(CheckaccountCfca::tableName().' as c','c.tx_sn=w.tx_sn')
                ->where(['w.tx_date'=>$date,'c.tx_date'=>$date])->all();//只校正交易金额tx_amount
        //var_dump($list);exit;
        $false_ids = array();
        $success_ids = array();
        foreach ($list as $data){
            if($data['is_checked']==1){//对于已经执行的。不能再执行了
                echo "has been implemented";
                exit;
            }
            if(bccomp($data['tx_amount'], $data['c_tx_amount'])===0){
                $success_ids[]=$data['id'];
            }else{
                $false_ids[]=$data['id'];
            }
        }

        if(!empty($false_ids)){
            CheckaccountWdjf::updateAll(['is_checked'=>1,'is_auto_okay'=>2], ['id'=>$false_ids]);
        }
        if(!empty($success_ids)){
            CheckaccountWdjf::updateAll(['is_checked'=>1,'is_auto_okay'=>1], ['id'=>$success_ids]);
        }
        echo 'finished';
    }

    /**
     * 每日汇总对账单【建议在执行完对账之后执行Comparebill】
     */
    public function actionCheckaccounthz(){
        bcscale(14);
        $date = date("Y-m-d",strtotime("-1 day"));//获取前日
        //先判断有没有录入过
        $beginYesterday=date('Y-m-d',mktime(0,0,0,date('m'),date('d')-1,date('Y')));
        $endYesterday=date('Y-m-d',mktime(0,0,0,date('m'),date('d'),date('Y'))-1);
//        $beginThisMonth=date('Y-m-d', mktime(0,0,0,date('m'),1,date('Y')));//本月的开始日期
//        $endThisMonth=date('Y-m-d', mktime(0,0,0,date('m'),date('t'),date('Y')));//本月的结束日期
        $count = CheckaccountHz::find()->filterWhere(['between','tx_date',$beginYesterday,$endYesterday])->count();
        if($count){
            echo 'has been implemented';
            exit;
        }

        $wdjfobj = CheckaccountWdjf::find()->where('is_checked=1 and (is_auto_okay=1 or (is_auto_okay=2 and is_okay=1))')->andFilterWhere(['between','tx_date',$beginYesterday,$endYesterday])->all();
        //var_dump($wdjfobj);exit;
        $recharge_count = $recharge_sum = $jiesuan_count = $jiesuan_sum = 0;
        foreach ($wdjfobj as $obj){
            if($obj->tx_type=1311){//充值
                $recharge_count++;
                $recharge_sum=  bcadd($recharge_sum, $obj->tx_amount);
            }else if($obj->tx_type=1341){//结算
                $jiesuan_count++;
                $jiesuan_sum=  bcadd($jiesuan_sum, $obj->tx_amount);
            }
        }
        $hzmodel = new CheckaccountHz();
        $bcround = new BcRound();
        $hzmodel->tx_date=  $date;
        $hzmodel->recharge_count=$recharge_count;
        $hzmodel->recharge_sum = $bcround->bcround($recharge_sum, 2);
        $hzmodel->jiesuan_count=$jiesuan_count;
        $hzmodel->jiesuan_sum = $bcround->bcround($jiesuan_sum, 2);
        if($hzmodel->validate()){
            $hzmodel->save();
            echo "finish!";
        }else{
            print_r($hzmodel->getErrors());
        }
    }


}