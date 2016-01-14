<?php

namespace SmsGate;

use common\models\Sms;
use common\models\sms\SmsMessage;

/**
 * 短信接口类
 * 使用说明：
 *     如果想自定义白名单，可使用依赖注入方式\Yii::$container->set('SmsGate\\SmsRequest',['isWhitelistEnabled'=>true,'whiteList'=> $whiteList ]);
 *     isWhitelistEnabled，whiteList可填可不填。不填即使用默认方式；参数$whiteList为数组格式
 * @author zhanghongyu <zhanghongyu@wangcaigu.com>
 */
class SmsRequest {

    private $isWhitelistEnabled = true;//true开启,false关闭
    private $whiteList;//白名单数组

    public function __construct($isEnabled = false,$list = []) {
        $this->isWhitelistEnabled = $isEnabled;
        $this->whiteList = $list;
    }

    /**
     * 发送短信方法
     * @param SmsMessage 对象
     */
    public function send(SmsMessage $message) {
        $mobile = $message->mobile;
        if ($this->isWhitelistEnabled && !in_array($mobile, $this->whiteList)) {
            throw new \Exception('白名单任务开启，手机号不在白名单');
        }
        $sms = new Sms();
        $msg_arr = json_decode($message->message, false);
        $data = $sms->sendTemplateSMS($message->mobile, $msg_arr, $message->template_id);
        return '000000' === (string)$data->statusCode;
    }

}
