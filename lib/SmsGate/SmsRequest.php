<?php

namespace SmsGate;

use common\models\Sms;

/**
 * 短信接口类
 *
 * @author zhanghongyu <zhanghongyu@wangcaigu.com>
 */
class SmsRequest {

    private $mobile; //接收手机号
    private $number; //短信编号
    private $msg_arr; //内容数组
    private $sms; //云通讯sms类对象
    private $debug = true; //是否开启调试模式 true开启false关闭

    /**
     * 构造函数
     * @param mobile 接收手机号
     * @param number 短信编号
     * @param msg_arr 内容数组
     */

    public function __construct(
    $mobile, $num, $msg_arr
    ) {
        $this->mobile = $mobile;
        $this->number = $num;
        $this->msg_arr = $msg_arr;
        $this->sms = new Sms();
    }

    public function getMobile() {
        return $this->mobile;
    }

    public function getNumber() {
        return $this->number;
    }

    public function getMsgarr() {
        return $this->msg_arr;
    }

    /**
     * 发送短信
     * @return boolean
     */
    public function isSendSuccess() {
        if ($this->checkNo() && $this->isWhitelist()) {
            $res = $this->sms->sendTemplateSMS($this->mobile, $this->msg_arr, $this->number);
            if ($res === 2000) {
                return true;
            }
            return false;
        } else {
            return false;
        }
    }

    /**
     * 检查短信编号是否是允许范围内的
     */
    public function checkNo() {
        $allow_nums = [];
        if (!in_array($this->number, $allow_nums)) {
            return false;
        }
        return true;
    }

    /**
     * 检查手机号是否在白名单[测试阶段使用]
     */
    public function isWhitelist() {
        $allow_mobile = [];
        if (!in_array($this->mobile, $allow_mobile) && $this->debug === true) {
            return false;
        }
        return true;
    }

}
