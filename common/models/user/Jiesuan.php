<?php

namespace common\models\user;

use Yii;
use yii\behaviors\TimestampBehavior;
/**
 * This is the model class for table "jiesuan".
 *
 * @property integer $id
 * @property string $sn
 * @property integer $type
 * @property string $osn
 * @property string $amount
 * @property string $bank_id
 * @property string $accountname
 * @property string $accountnumber
 * @property string $branchname
 * @property string $province
 * @property string $city
 * @property integer $status
 * @property string $remark
 * @property string $bankNotificationTime
 * @property integer $created_at
 * @property integer $updated_at
 */
class Jiesuan extends \yii\db\ActiveRecord
{
    //状态
    const STATUS_NO = 0; //结算未处理
    const STATUS_ACCEPT = 10; //结算请求已经受理
    const STATUS_IN = 30; //结算进行中
    const STATUS_YES = 40; //结算已经执行（已发送转账指令）
    const STATUS_FAULT = 50; //转账退回
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'jiesuan';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sn', 'bank_id'], 'required'],
            [['sn'], 'unique'],
//            [['type', 'status'], 'integer'],
//            [['amount'], 'number'],
//            [['sn', 'osn', 'accountnumber'], 'string', 'max' => 32],
//            [['bank_id'], 'string', 'max' => 10],
//            [['accountname'], 'string', 'max' => 64],
//            [['branchname'], 'string', 'max' => 48],
//            [['province', 'city'], 'string', 'max' => 16],
//            [['remark'], 'string', 'max' => 100],
//            [['bankNotificationTime'], 'string', 'max' => 30]
        ];
    }
    
    public function behaviors() {
        return [
                TimestampBehavior::className(),
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sn' => 'Sn',
            'type' => 'Type',
            'osn' => 'Osn',
            'amount' => 'Amount',
            'bank_id' => 'Bank ID',
            'accountname' => 'Accountname',
            'accountnumber' => 'Accountnumber',
            'branchname' => 'Branchname',
            'province' => 'Province',
            'city' => 'City',
            'status' => 'Status',
            'remark' => 'Remark',
            'bankNotificationTime' => 'Bank Notification Time',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
    
    public static function createSN($pre = '') {
        list($usec, $sec) = explode(" ", microtime());
        $v = ((float) $usec + (float) $sec);

        list($usec, $sec) = explode(".", $v);
        $date = date('ymdHisx' . rand(1000, 9999), $usec);
        return $pre . str_replace('x', $sec, $date);
    }
    
    /**
     * 结算代码
     * @return boolean
     */
    public function settlement(){
        $obj = static::findOne(['osn'=>  $this->osn,'status'=> self::STATUS_SUCCESS]);
        if($obj){
            return TRUE;
        }
        $rsapath = Yii::getAlias('@common');
        $encrypt_str = file_get_contents($rsapath.'/api-rsa/content');
        $model = new Jiesuan();
        $payment = new \common\lib\cfca\Payment();
        $model->sn=self::createSN();
        $model->osn=  $this->osn;
        $model->amount=  $this->amount;
        $model->bank_id= Yii::$app->params['bank_id'];
        $model->accountname=  Yii::$app->params['accountname'];
        $model->accountnumber=  Yii::$app->params['accountnumber'];
        $model->branchname=  Yii::$app->params['branchname'];
        $model->province=  Yii::$app->params['province'];
        $model->city=  Yii::$app->params['city'];
       $response="";
        if($model->save()){
            $unsec = Yii::$app->functions->rsaVerifySign($rsapath.'/components/rsa/settlement/rsa_public_key.pem',(Yii::$app->params['accountnumber']),$encrypt_str);
            if($unsec===false){
                $response = \Yii::$app->functions->createXmlResponse('2003',"非法的账号".($model->accountnumber));
            }else{
                $InstitutionID = \Yii::$app->params['cfca']['InstitutionID'];
                $xmltx1341 = \Yii::$app->params['cfca']['xmltx1341'];
                //var_dump($xmltx1341);
                $simpleXML= new \SimpleXMLElement($xmltx1341);
                // 4.赋值
                $simpleXML->Body->InstitutionID=$InstitutionID;//'000020'
                $simpleXML->Body->SerialNumber=$model->sn;
                $simpleXML->Body->OrderNo=$model->osn;
                $simpleXML->Body->Amount=$model->amount*100;
                $simpleXML->Body->Remark='remark';
                $simpleXML->Body->AccountType=12;
                $simpleXML->Body->PaymentAccountName='';
                $simpleXML->Body->PaymentAccountNumber='';
                $simpleXML->Body->BankAccount->BankID=Yii::$app->params['bank_id'];//测试只能用700 $model->bank_id;
                $simpleXML->Body->BankAccount->AccountName=$model->accountname;
                $simpleXML->Body->BankAccount->AccountNumber=$model->accountnumber;
                $simpleXML->Body->BankAccount->BranchName=$model->branchname;
                $simpleXML->Body->BankAccount->Province=$model->province;
                $simpleXML->Body->BankAccount->City=$model->city;
                $simpleXML->Body->PaymentNoList="";
                $xmlStr = $simpleXML->asXML();//echo $xmlStr;exit;
                
                $rcharge = RechargeRecord::findOne(['sn'=>$model->osn]);
                $cfcalog = new CfcaLog();
                $cfcalog->type=  CfcaLog::TYPE_SETTLEMENT;
                $cfcalog->account_id=$rcharge->account_id;
                $cfcalog->uid=$rcharge->uid;
                $cfcalog->log_type=1;
                $cfcalog->response=$xmlStr;
                $cfcalog->save();
                $this->sendMail('xmlstr:'.$xmlStr);
                $message=base64_encode(trim($xmlStr));
                $signature=$payment->cfcasign_pkcs12(trim($xmlStr));

                $response=$payment->cfcatx_transfer($message,$signature);	
                $plainText=trim(base64_decode($response[0]));
                $ok=$payment->cfcaverify($plainText,$response[1]);
                $this->sendMail('ok:'.$ok);
                if($ok!=1)
                {
                    $t = \Yii::$app->functions->createXmlResponse('2002',"验签失败");
                    $this->sendMail('t1:'.$t);
                    $cfcalog = new CfcaLog();
                    $cfcalog->type=  CfcaLog::TYPE_SETTLEMENT;
                    $cfcalog->account_id=$rcharge->account_id;
                    $cfcalog->uid=$rcharge->uid;
                    $cfcalog->log_type=1;
                    $cfcalog->response=$t;
                    $cfcalog->save();
                    $this->sendMail('t2:'.$t);
                    $response = $t;
                }else{	
                    $simpleXML= new \SimpleXMLElement($plainText);
                    $cfcalog = new CfcaLog();
                    $cfcalog->type=  CfcaLog::TYPE_SETTLEMENT;
                    $cfcalog->account_id=$rcharge->account_id;
                    $cfcalog->uid=$rcharge->uid;
                    $cfcalog->log_type=1;
                    $cfcalog->response=$simpleXML;
                    $cfcalog->save();
                    if($simpleXML->Head->Code == "2000"){
                        $model->status=  self::STATUS_SUCCESS;
                        $model->save();
                    }
                    $response=$plainText;
                    $this->sendMail('t3:'.$plainText);
                }
            }
            if($this->type==1){
                $rcharge = RechargeRecord::findOne(['sn'=>$model->osn]);
                $cfcalog = new CfcaLog();
                $cfcalog->type=  CfcaLog::TYPE_SETTLEMENT;
                $cfcalog->account_id=$rcharge->account_id;
                $cfcalog->uid=$rcharge->uid;
                $cfcalog->log_type=2;
                $cfcalog->response=$response;
                $cfcalog->save();
                $this->sendMail('t4:'.$plainText);
            }
            return $response;
        }else{
            return FALSE;
        }
    }
    
            
}
