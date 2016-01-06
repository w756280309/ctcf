<?php

namespace SmsGate;

use common\models\Sms;
use common\models\sms\SmsMessage;

/**
 * 短信接口类 
 *
 * @author zhanghongyu <zhanghongyu@wangcaigu.com>
 */
class SmsRequest {

    /**
     * 发送短信方法
     * @param SmsMessage 对象
     */
    public function send(SmsMessage $message) {
        $sms = new Sms();
        $msg_arr = json_decode($message->message, false);
        $data = $sms->sendTemplateSMS($message->mobile, $msg_arr, $message->template_id);
        return 2000 === (int)$data->statusCode;
    }

}
