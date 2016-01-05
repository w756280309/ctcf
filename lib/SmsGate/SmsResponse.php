<?php

namespace SmsGate;


/**
 * 短信接口响应类
 *
 * @author zhanghongyu <zhanghongyu@wangcaigu.com>
 */
class SmsResponse {

    private $code; //短信码
    private $sms; //短信数据对象对应表sms

    /**
     * 构造函数
     * @param code 短信码
     * @param sms 短信数据对象对应表sms
     */

    public function __construct(
    $code, $sms
    ) {
        $this->code = $code;
        $this->sms = $sms;
    }

    /**
     * 验证短信正确性
     * @return boolean
     */
    public function validateSms() {
        
    }

    

}
