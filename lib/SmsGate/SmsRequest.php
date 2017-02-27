<?php

namespace SmsGate;

use common\models\Sms;
use common\models\sms\SmsMessage;
use common\utils\SecurityUtils;

/**
 * 短信接口类
 * 使用说明：
 *     如果想自定义白名单，可使用依赖注入方式\Yii::$container->set('SmsGate\\SmsRequest',['isWhitelistEnabled'=>true,'whiteList'=> $whiteList ]);
 *     isWhitelistEnabled，whiteList可填可不填。不填即使用默认方式；参数$whiteList为数组格式.
 *
 * @author zhanghongyu <zhanghongyu@wangcaigu.com>
 */
class SmsRequest
{
    /**
     * 发送短信方法.
     *
     * @param SmsMessage 对象
     */
    public function send(SmsMessage $message)
    {
        $sms = new Sms();
        $msg_arr = json_decode($message->message, false);
        $data = $sms->sendTemplateSMS(SecurityUtils::decrypt($message->safeMobile), $msg_arr, $message->template_id);
        $statusCode = (string) $data->statusCode;
        if ('000000' === $statusCode) {
            return true;
        } else {
            throw new \Exception($statusCode);
        }
    }
}