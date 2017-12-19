<?php

namespace common\service;

use common\models\Sms;
use common\models\sms\SmsTable;
use common\models\sms\SmsMessage;
use common\models\user\User;
use common\service\AliSmsService;
use common\utils\SecurityUtils;
use common\utils\TxUtils;
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
    const DEFAULT_SERVICE_PROVIDER = 'ytx';
    const PROVIDER_YTX = 'ytx';
    const PROVIDER_ALI = 'ali';
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

        $model->time_len = 15;
        $model->end_time = $time + $model->time_len * 60;
        $mockSms = Yii::$app->params['mock_sms'];

        if ($mockSms) {
            $model->code = '888888';
        }

        if ($model->save()) {
            if (!self::canSend($phone)) {
                return ['code' => 0, 'message' => ''];
            }

            $message = [];
            $templateId = null;

            if (1 === $type) {
                $message = [
                    'code' => $model->code,
                ];
                $templateId = Yii::$app->params['sms.ali.template.register'];
            } elseif (2 === $type) {
                $message = [
                    'code' => $model->code,
                ];
                $templateId = Yii::$app->params['sms.ali.template.forget.password'];
            }

            if (!empty($message)) {
                $code = 0;
                $msg = '';

                try {
                    /**
                     * @var AliSmsService $aliSms
                     */
                    $aliSms = Yii::$container->get('alisms');
                    $sn = TxUtils::generateSn('AliSms');
                    $signName = '温都金服';
                    if ($aliSms->send($sn, $phone, $signName, $templateId, $message)) {
                        $user = new User([
                            'id' => 0,
                            'safeMobile' => SecurityUtils::encrypt($phone),
                        ]);

                        $smsMessage = SmsMessage::initSms($user, $message, $templateId);
                        $smsMessage->status = SmsMessage::STATUS_SENT;
                        $smsMessage->serviceProvider = SmsService::PROVIDER_ALI;
                        $smsMessage->save(false);
                    }
                } catch (\Exception $ex) {
                    $code = 1;
                    $msg = $ex->getMessage();
                }

                return [
                    'code' => $code,
                    'message' => $msg,
                ];
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
        $black_mobile = explode(',', Yii::$app->params['NoSendSms']);
        if (in_array($mobile, $black_mobile)) {
            Yii::info('黑名单用户：' . $mobile);
            echo 'black';
            echo $mobile;
            var_dump($black_mobile);
            return true;
        }
        if (null === $user) {
            $user = new User([
                'id' => 0,
                'safeMobile' => SecurityUtils::encrypt($mobile),
            ]);
        }

        $smsMessage = SmsMessage::initSms($user, $data, $templateId, $level);

        if (!self::canSend($mobile)) {
            echo 'cansend';
            $smsMessage->status = SmsMessage::STATUS_SENT;
        }
        var_dump($smsMessage->save());die;
        return $smsMessage->save(false);
    }

    /**
     * 即时发送短信.
     */
    public static function sendNow($mobile, $templateId, array $data = [], User $user = null, $level = SmsMessage::LEVEL_MIDDLE)
    {
        $black_mobile = explode(',', Yii::$app->params['NoSendSms']);
        if (in_array($mobile, $black_mobile)) {
            Yii::info('黑名单用户：' . $mobile);
            return true;
        }
        if (self::canSend($mobile) && !empty($data)) {
            $sms = new Sms();
            $data = $sms->sendTemplateSMS($mobile, $data, $templateId);
            $statusCode = (string) $data->statusCode;

            if ('000000' !== $statusCode) {
                switch ($statusCode) {
                    case '160040':
                        $msg = '手机号接收短信达到数量限制，请稍候重试';
                        break;
                    case '160038':
                        $msg = '短信发送请求过于频繁，请稍候重试';
                        break;
                    default:
                        $msg = '短信发送失败';
                }

                throw new \Exception($msg, $statusCode);
            }
        }

        return true;
    }

    /**
     * 判断一个手机号是否需要实际发送短信.
     *
     * @param string $mobile 明文手机号
     */
    private static function canSend($mobile)
    {
        $smsWhiteList = Yii::$app->params['sms_white_list'];

        return !(Yii::$app->params['mock_sms'] && !in_array($mobile, $smsWhiteList));
    }
}
