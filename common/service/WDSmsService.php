<?php

namespace common\service;

use common\helpers\HttpHelper;
use common\models\sms\SmsMessage;
use common\models\user\User;
use common\utils\SecurityUtils;
use Yii;

/**
 * 沃动短信通道
 * 发送生日祝福
 * @author ZouJianShuang
 */
class WDSmsService
{
    public $userid;
    public $password;
    public $account;
    public $url;

    public function __construct()
    {
        $this->userid = Yii::$app->params['WoDong']['userid'];
        $this->password = Yii::$app->params['WoDong']['password'];
        $this->account = Yii::$app->params['WoDong']['account'];
        $this->url = Yii::$app->params['WoDong']['url'];
    }

    public function send($mobile, $content)
    {

        if (!$this->canSend($mobile)) {
            return false;
        }

        $user = User::findOne(['safeMobile' => SecurityUtils::encrypt($mobile)]);
        if (!is_null($user)) {
            $smsMessage = SmsMessage::initSms($user, ['content' => $content], 'wodong', SmsMessage::LEVEL_MIDDLE);
            $data = [
                'action' => 'send',
                'userid' => $this->userid,
                'password' => $this->password,
                'mobile' => $mobile,
                'account' => $this->account,
                'content' => $content,
                'json' => 1,
            ];
            $url = $this->url . http_build_query($data);
            $res =json_decode(HttpHelper::doGet($url));

            if (strtolower($res->code) == 'success') {
                $smsMessage->status = SmsMessage::STATUS_SENT;
                $smsMessage->save();
                return true;
            } else {
                Yii::info('沃动短信发送失败，手机号：'.$mobile.'，失败原因：'. $res->data->message);
            }
        } else {
            Yii::info('账号：'.$mobile.'不存在');
        }
        return false;
    }

    /**
     * 判断一个手机号是否可以发送 黑白名单
     *
     * @param $mobile
     *
     * @return bool
     */
    private function canSend($mobile)
    {
        //判断黑名单
        $black_mobile = explode(',', Yii::$app->params['NoSendSms']);
        if (in_array($mobile, $black_mobile)) {
            Yii::info('黑名单用户：' . $mobile);
            return false;
        }

        //判断白名单
        $smsWhiteList = Yii::$app->params['sms_white_list'];

        return !(Yii::$app->params['mock_sms'] && !in_array($mobile, $smsWhiteList));
    }
}