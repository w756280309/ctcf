<?php

namespace Ding;


use DingNotify\Client\Service;

class DingNotify
{
    public $corpid = 'ding9c03c9121e27baf4';
    public $corpsecret = 'FZ2JybJ-t5oKYAZWd0Fm6N1kIIDGt1en0EtTP_ggDus_6gsvNiEpwRO_iF1vlvwb';
    public $agentid = '17714114';
    public $log_path = __DIR__ . '/../../console/runtime/ding/';

    private $_config;
    private $_service;

    public function __construct()
    {
        if (!file_exists($this->log_path)) {
            @mkdir($this->log_path);
        }
        $this->_config = ['corpid' => $this->corpid, 'corpsecret' => $this->corpsecret, 'agentid' => $this->agentid, 'log_path' => $this->log_path];
        $this->_service = new Service($this->_config);
    }

    //向指定钉钉群发送消息
    public function sendMessage($message)
    {
        $this->_service->charSentText($message);
    }
}