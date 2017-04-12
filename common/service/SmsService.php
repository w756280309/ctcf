<?php

namespace common\service;

use common\models\sms\SmsTable;
use common\models\sms\SmsMessage;
use common\models\user\User;
use common\utils\SecurityUtils;
use common\utils\StringUtils;
use Yii;

/**
 * Desc 主要用于短信生成验证码及其校验
 * Created by Pingter.
 * User: Pingter
 * Date: 15-11-19
 * Time: 下午4:02.
 */
class SmsService
{
    /**
     * 生成短信验证码
     *
     * @param $type, $phone $type=1注册 $type=2找回密码
     */
    public static function createSmscode($type, $phone)
    {
        if (!is_int($type)) {
            $type = (int) $type;
        }

        if (!in_array($type, [1, 2])) {
            return ['code' => 1, 'message' => '短信发送失败'];
        }

        if (!is_string($phone)) {
            $phone = (string) $phone;
        }

        if (empty($phone)) {
            return ['code' => 1, 'message' => '请输入手机号'];
        }

        $reDate = '/^(13[0-9]|14[0-9]|15[0-9]|17[0-9]|18[0-9])\d{8}$/';
        $re = preg_match($reDate, $phone);
        if (!$re) {
            return ['code' => 1, 'message' => '手机号码输入错误'];
        }

        $time = time();
        $sms = SmsTable::find()->where(['safeMobile' => SecurityUtils::encrypt($phone), 'status' => SmsTable::STATUS_UNUSE])->andFilterWhere(['>=', 'end_time', $time])->orderBy('id desc')->one();
        $model = new SmsTable([
            'code' => $sms ? ($sms->code) : (Yii::$app->functions->createRandomStr()),
            'type' => $type,
            'safeMobile' => SecurityUtils::encrypt($phone),
        ]);

        $model->time_len = 5;
        $model->end_time = $time + $model->time_len * 60;
        $mockSms = Yii::$app->params['mock_sms'];

        if ($mockSms) {
            $model->code = '888888';
        }

        if ($model->save()) {
            $smsWhiteList = Yii::$app->params['sms_white_list'];
            if ($mockSms && !in_array(SecurityUtils::decrypt($model->safeMobile), $smsWhiteList)) {
                return ['code' => 0, 'message' => ''];
            }

            $message = [];
            $template_id = null;
            if (1 === $type) {
                $message = [
                    $model->code,
                    $model->time_len,
                ];
                $template_id = Yii::$app->params['sms']['yzm'];
            } elseif (2 === $type) {
                $message = [
                    $model->code,
                ];
                $template_id = Yii::$app->params['sms']['forget'];
            }

            if (!empty($message)) {
                $sms = new SmsMessage([
                    'template_id' => $template_id,
                    'safeMobile' => $model->safeMobile,
                    'message' => json_encode($message),
                ]);
                try {
                    $res = \Yii::$container->get('sms')->send($sms);
                    if ($res) {
                        return ['code' => 0, 'message' => ''];
                    }
                } catch (\Exception $ex) {
                    if ('160040' === $ex->getMessage()) {
                        return ['code' => 1, 'message' => '手机号接收短信达到数量限制，请稍候重试'];
                    } elseif ('160038' === $ex->getMessage()) {
                        return ['code' => 1, 'message' => '短信发送请求过于频繁，请稍候重试'];
                    } else {
                        return ['code' => 1, 'message' => '短信发送失败'];
                    }
                }
            }
        }

        return ['code' => 1, 'message' => '验证码生成超时'];
    }

    /**
     * 验证短信验证码
     *
     * @param $phone, $smscode
     */
    public static function validateSmscode($phone, $smscode)
    {
        $model = SmsTable::find()->where(['safeMobile' => SecurityUtils::encrypt($phone), 'status' => SmsTable::STATUS_UNUSE])->orderBy('id desc')->one();
        if (empty($model)) {
            return ['code' => 1, 'message' => '短信验证码输入错误'];
        }
        if ($model->code != $smscode) {
            return ['code' => 1, 'message' => '短信验证码输入错误'];
        }
        if (time() > $model->end_time) {
            return ['code' => 1, 'message' => '短信验证码过期，请重新发送'];
        }

        return ['code' => 0];
    }

    /**
     * 修改短信验证码状态
     *
     * @param $phone
     */
    public static function editSms($phone)
    {
        SmsTable::updateAll(['status' => SmsTable::STATUS_USE], ['safeMobile' => SecurityUtils::encrypt($phone), 'status' => SmsTable::STATUS_UNUSE]);
    }

    /**
     * 异步发送短信.
     */
    public static function send($mobile, $templateId, array $data = [], User $user = null, $level = SmsMessage::LEVEL_MIDDLE)
    {
        $smsMessage = new SmsMessage([
            'template_id' => $templateId,
            'uid' => $user ? $user->id : 0,
            'safeMobile' => SecurityUtils::encrypt($mobile),
            'message' => json_encode($data),
            'level' => $level,
        ]);

        $smsWhiteList = Yii::$app->params['sms_white_list'];
        if (Yii::$app->params['mock_sms'] && !in_array($mobile, $smsWhiteList)) {
            $smsMessage->status = SmsMessage::STATUS_SENT;
        }

        return $smsMessage->save();
    }
}
