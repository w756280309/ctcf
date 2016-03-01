<?php
namespace common\service;

use Yii;
use yii\web\Response;
use common\models\sms\SmsTable;
use common\models\user\User;
use common\models\sms\SmsMessage;

/**
 * Desc 主要用于短信生成验证码及其校验
 * Created by Pingter.
 * User: Pingter
 * Date: 15-11-19
 * Time: 下午4:02
 */
class SmsService {

    public function __construct() {
    }

    /**
     * 生成短信验证码
     * @param $type,$uid
     * @return boolean
     */
    public static function createSmscode($type=3,$phone=null,$uid=null) {
        if (!is_int($type)) {
            $type = (int) $type;
        }

        if (!is_string($phone)) {
            $phone = (string) $phone;
        }

        if (!is_string($uid)) {
            $uid = (string) $uid;
        }

        if(!$phone) {
            return ['code' => 1, 'message' => '请输入手机号'];
        }

        $reDate = '/^(13[0-9]|14[0-9]|15[0-9]|17[0-9]|18[0-9])\d{8}$/';
        $re = preg_match($reDate, $phone);
        if (!$re) {
            return ['code' => 1, 'message' => '手机号码输入错误'];
        }

        $user = User::findOne(['mobile'=>$phone]);
        if($phone === $uid && $type === 1 && !empty($user)) {
            return ['code' => 1, 'message' => '手机号已经注册过'];
        }

        $time = time();
        $sms = SmsTable::find()->where(['temp_uid' => $uid, 'status' => SmsTable::STATUS_UNUSE])->andFilterWhere(['>=','end_time',$time])->orderBy("id desc")->one();
        $model = new SmsTable();
        $model->code = $sms?($sms->code):(Yii::$app->functions->createRandomStr());
        $model->time_len = 5;
        $model->type = $type;
        $model->temp_uid = $uid;
        $model->mobile = $phone;
        $model->end_time = $time + $model->time_len * 60;
        $res = $model->save();
        if($res) {
            $message = [
                $model->code,
                $model->time_len
            ];
            $sms = new SmsMessage([
                'uid' => empty($user)?0:$user->id,
                'template_id' => Yii::$app->params['sms']['yzm'],
                'mobile' => $model->mobile,
                'level' => SmsMessage::LEVEL_HIGH,
                'message' => json_encode($message)
            ]);
            //$sms->save();
            \Yii::$container->get('sms')->send($sms);
            return ['code' => 0, 'message' => ''];
        } else {
            return ['code' => 1, 'message' => '验证码生成超时'];
        }
    }

    /**
     * 验证短信验证码
     * @param $type,$uid
     * @return boolean
     */
    public static function validateSmscode($uid=null,$smscode=null) {
        if(!$uid || !$smscode) {
            return ['code' => 1,'message' => '验证码输入错误'];
        }

        $model = SmsTable::find()->where(['temp_uid' => $uid, 'status' => SmsTable::STATUS_UNUSE])->orderBy('id desc')->one();
        if(empty($model)) {
            return ['code' => 1,'message' => '验证码输入错误'];
        }
        if($model->code != $smscode) {
            return ['code' => 1,'message' => '验证码输入错误'];
        }
        if(time()>$model->end_time) {
            return ['code' => 1,'message' => '验证码过期，请重新发送'];
        }

        return ['code' => 0];
    }

    /**
     * 备注验证信息
     * @param $type,$uid
     * @return boolean
     */
    public static function editSms($uid=null,$phone=null) {
        if(!$uid) {
            return false;
        }

        if(empty($phone)) {
            SmsTable::updateAll(['uid' => $uid,'status' => SmsTable::STATUS_USE],['temp_uid' => $uid, 'status' => SmsTable::STATUS_UNUSE]);
        } else {
            SmsTable::updateAll(['uid' => $uid,'status' => SmsTable::STATUS_USE],['temp_uid' => $phone, 'status' => SmsTable::STATUS_UNUSE]);
        }

        return true;
    }

}
