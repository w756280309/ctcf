<?php
namespace common\models\sms;

use yii\db\ActiveRecord;


class SmsTable extends ActiveRecord {
    
    const STATUS_USE = 1;
    const STATUS_UNUSE = 0;
    
//    const TYPE_REG_CODE = 1;
//    const TYPE_FIND_PWD = 2;
//    const TYPE_EXAMIN_PASS = 3;
//    const TYPE_EDIT_BIND_MOBILE = 4;
//    
//    const SMS_CODE_REG = 12552;//PC端手机验证码注册用
//    const SMS_CODE_EXAMIN_PASS = 16883;//  后台审核短信通知结果
//    const SMS_CODE_EXAMIN_UNPASS = 16858;//后台审核短信通知结果 未通过
//    const SMS_CODE_FIND_PWD = 16777;//PC端密码找回手机验证码 应该是17340
//    const SMS_CODE_EDIT_BIND_MOBILE=17342;//端短信修改绑定手机
//    
//    const SMS_TIME_LEN_REG = 30;//注册码有效时间
//    
//    const SMS_STATUS_SUCCESS = '000000';
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'sms';
    }
     
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type','mobile'], 'required'],
            [['created_at'],'default',  'value' => time()],
            [['status'],'default',  'value' => 0],
            //['link_val','string']
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => '短信码',
            'time_len' => '短信有效时长',
            'type' => '类型',
            'uid'=>"uid",
            'username'=>"username",
            'mobile' => '手机号',
            'end_time' => '截止日期',
            'created_at' => '创建时间',
        ];
    }
    
    /*
     * 建议$cond 参数包含uid type mobile
     * $sms_model = new SmsTable();
     * $r = $sms_model->verifyCode(array('uid'=>1,'mobile'=>'15810036547','type'=>3));
     */
//    public function verifyCode($cond = array()){
//        $sms = static::find()->where($cond)->orderBy("id desc")->one();
//        if(is_null($sms)){
//            return array('result'=>0,'error'=>"短信码错误");
//        }
//        if($sms->status==1){
//            return array('result'=>0,'error'=>"短信码已经被使用");
//        }
//        if($sms->end_time<time()){
//            return array('result'=>0,'error'=>"短信码失效");
//        }
//        if(($sms->end_time-$sms->created_at)/60>$sms->time_len){
//            return array('result'=>0,'error'=>"超过".$sms->time_len."分钟有效时长");
//        }
//        return array('result'=>1,'error'=>"",'obj_id'=>$sms->id);
//    }
    
}
