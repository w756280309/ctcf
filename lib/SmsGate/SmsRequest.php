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

    private $isTest = true;//true开启,false关闭
    private $testMobile = ['15810036547','18518154492'];//白名单数组
    
    public function getIsTest() {
        return $this->isTest;
    }

    public function getTestMobile() {
        return $this->testMobile;
    }

    /**
     * 发送短信方法
     * @param SmsMessage 对象
     */
    public function send(SmsMessage $message) {
        $mobile = $message->mobile;
        if ($this->isTest && !empty($this->testMobile)) {
            $mobile = current($this->testMobile);
        }
        $sms = new Sms();
        $msg_arr = json_decode($message->message, false);
        $data = $sms->sendTemplateSMS($message->mobile, $msg_arr, $message->template_id);
        return '000000' === (string)$data->statusCode;
    }

}
