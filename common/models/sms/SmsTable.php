<?php

namespace common\models\sms;

use yii\db\ActiveRecord;

class SmsTable extends ActiveRecord
{
    const STATUS_USE = 1;
    const STATUS_UNUSE = 0;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sms';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type', 'mobile'], 'required'],
            [['created_at'], 'default',  'value' => time()],
            [['status'], 'default',  'value' => 0],
            //['link_val','string']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'code' => '短信码',
            'time_len' => '短信有效时长',
            'type' => '类型',
            'uid' => 'uid',
            'username' => 'username',
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
