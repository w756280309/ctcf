<?php

namespace common\models\user;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "batchpay".
 *
 * @property integer $id
 * @property string $sn
 * @property string $amount
 * @property integer $uid
 * @property integer $account_id
 * @property string $bank_id
 * @property integer $account_type
 * @property string $account_name
 * @property string $account_number
 * @property string $branch_name
 * @property string $province
 * @property string $city
 * @property string $phone_number
 * @property string $identification_type
 * @property string $identification_number
 * @property integer $status
 * @property string $banktxtime
 * @property integer $created_at
 * @property integer $updated_at
 */
class Batchpay extends \yii\db\ActiveRecord
{
    //账户类型： 11=个人账户 12=企业账户
    const AC_TYPE_PER=11;
    const AC_TYPE_ORG=12;
    
    //开户证件类型 0=身份证 1=户口簿 2=护照 3=军官证 4=士兵证 5=港澳居民来往内地通行证 6=台湾同胞来往内地通行证 7=临时身份证 8=外国人居留证 9=警官证 X=其他证件
    const ID_IDCARD = 0;
    const ID_HUKOU = 1;
    const ID_HUZHAO = 2;
    const ID_JUNGUAN = 3;
    const ID_SHIBING = 4;
    const ID_HM = 5;
    const ID_TAIWAN = 6;
    const ID_LINIDCARD = 7;
    const ID_FOREIGNER = 8;
    const ID_POLICE = 9;
    const ID_OTHER = 'X';
    
    //交易状态 10=未处理 20=正在处理 30=代付成功 40=代付失败
    const STATUS_WEI = 10;
    const STATUS_NOW = 20;
    const STATUS_SUCCESS = 30;
    const STATUS_FAIL = 40;
    
    public static function createSN($pre = 'batchpay') {
        $pre_val = Yii::$app->params['bill_prefix'][$pre];
        list($usec, $sec) = explode(" ", microtime());
        $v = ((float) $usec + (float) $sec);

        list($usec, $sec) = explode(".", $v);
        $date = date('ymdHisx' . rand(1000, 9999), $usec);
        return $pre_val . str_replace('x', $sec, $date);
    }
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'batchpay';
    }
    
    public function behaviors() {
        return [
                TimestampBehavior::className(),
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sn', 'uid', 'bank_id', 'account_name','user_bank', 'account_number', 'branch_name', 'province', 'city'], 'required'],
            [['amount'], 'number'],
            [['uid', 'account_id', 'account_type'], 'integer'],
            [['sn', 'province', 'city', 'identification_number'], 'string', 'max' => 32],
            [['bank_id'], 'string', 'max' => 10],
            [['account_name', 'account_number'], 'string', 'max' => 20],
            [['branch_name'], 'string', 'max' => 96],
            [['phone_number'], 'string', 'max' => 16],
            [['identification_type'], 'string', 'max' => 4]
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
            'amount' => 'Amount',
            'uid' => 'Uid',
            'account_id' => 'Account ID',
            'bank_id' => 'Bank ID',
            'account_type' => 'Account Type',
            'account_name' => 'Account Name',
            'account_number' => 'Account Number',
            'branch_name' => 'Branch Name',
            'province' => 'Province',
            'city' => 'City',
            'phone_number' => 'Phone Number',
            'identification_type' => 'Identification Type',
            'identification_number' => 'Identification Number',
            'status' => 'Status',
            'banktxtime' => 'Banktxtime',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
    
    public function addBatchpay($ub){
        $model = new Batchpay();
        $model->sn=self::createSN();
        $model->uid=$ub->uid;
        $model->account_name=$ub->account;
        $model->account_number=$ub->card_number;
        $model->account_type=$ub->account_type;
        $model->bank_id=$ub->bank_id;
        $model->branch_name=$ub->sub_bank_name;
        $model->city=$ub->city;
        $model->province=$ub->province;
        $model->user_bank=$ub->id;
        $res = $model->save();
        if($res){
            $payment = new \common\lib\cfca\Payment();
            
            $simpleXML = new \SimpleXMLElement(\Yii::$app->params['cfca']['xmltx1510']);
            $simpleXML->Body->InstitutionID = \Yii::$app->params['cfca']['InstitutionID'];
            $simpleXML->Body->BatchNo = $model->sn;
            $simpleXML->Body->TotalAmount = 1;
            $simpleXML->Body->TotalCount = 1;
            $simpleXML->Body->Remark = 'UID'.$ub->uid."".$ub->province."".$ub->city."".$ub->sub_bank_name; 
            //$simpleXML->Body->PaymentFlag = 0;//普通代付
            $simpleXML->Body->PaymentFlag = 1;//
            $simpleXML->Body->Item->ItemNo=  $model->sn;
            $simpleXML->Body->Item->Amount=1;
            $simpleXML->Body->Item->BankID=$ub->bank_id;
            $simpleXML->Body->Item->AccountType=$ub->account_type;
            $simpleXML->Body->Item->AccountName=$ub->account;
            $simpleXML->Body->Item->AccountNumber=$ub->card_number;
            $simpleXML->Body->Item->BranchName=$ub->sub_bank_name;
            $simpleXML->Body->Item->Province=$ub->province;
            $simpleXML->Body->Item->City=$ub->city;
            $simpleXML->Body->Item->Note="";
            $simpleXML->Body->Item->PhoneNumber="";
            $simpleXML->Body->Item->Email="";
            $simpleXML->Body->Item->IdentificationType=0;
            $simpleXML->Body->Item->IdentificationNumber="";
            $xmlStr = $simpleXML->asXML();
            
            $rcfcalog = new CfcaLog();
            $rcfcalog->type = CfcaLog::TYPE_BATCHPAY;
            $rcfcalog->uid = $ub->uid;
            $rcfcalog->log_type = 1;
            $rcfcalog->response_code='1510';
            $rcfcalog->response = $xmlStr;
            $rcfcalog->save();
            
            $message = base64_encode(trim($xmlStr));
            $signature = $payment->cfcasign_pkcs12(trim($xmlStr));
            $response=$payment->cfcatx_transfer($message,$signature);	
            $plainText=trim(base64_decode($response[0]));
            $ok=$payment->cfcaverify($plainText,$response[1]);
            $request="";
            $response_code = "";
            if($ok!=1)
            {
                $request = \Yii::$app->functions->createXmlResponse('2002',"验签失败");
            }else{	
                $request=$plainText;
                $simpleXML= new \SimpleXMLElement($plainText);//
                $response_code = $simpleXML->Head->Code;
            }
            $cfcalog = new CfcaLog();
            $cfcalog->type = CfcaLog::TYPE_BATCHPAY;
            $cfcalog->uid = $ub->uid;
            $cfcalog->log_type = 2;
            $cfcalog->response_code=$response_code;
            $cfcalog->response = $request;
            $cfcalog->save();
            return TRUE;
           
        }else{
            return FALSE;
        }
    }
    
}
