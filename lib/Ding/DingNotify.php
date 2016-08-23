<?php

namespace Ding;


use DingNotify\Client\Client;
use DingNotify\Client\Text;

class DingNotify
{
    public $corpid;
    public $corpsecret;
    public $agentid;
    public $chatId;
    public $user;
    public $log_path;

    private $_client;

    public function __construct($company = 'wdjf')
    {
        $config = \Yii::$app->params['ding_config'][$company];
        if (!$config) {
            return false;
        }
        $this->log_path = __DIR__ . '/../../console/runtime/ding/';
        if (!file_exists($this->log_path)) {
            @mkdir($this->log_path);
        }
        if (isset($config['corp_id'])) {
            $this->corpid = $config['corp_id'];
        }
        if (isset($config['corp_secret'])) {
            $this->corpsecret = $config['corp_secret'];
        }
        if (isset($config['agent_id'])) {
            $this->agentid = $config['agent_id'];
        }
        if (isset($config['chat_id'])) {
            $this->chatId = $config['chat_id'];
        }
        if (isset($config['user'])) {
            $this->user = $config['user'];
        }
        $client = new Client(['corpid' => $this->corpid, 'corpsecret' => $this->corpsecret, 'agentid' => $this->agentid, 'log_path' => $this->log_path]);
        $client = $client->initNew();
        $this->_client = $client;
    }

    //钉钉向群发送消息服务
    public function charSentText($content)
    {
        $client = $this->_client;
        $text = new Text($content);
        $client->chatSend($this->chatId, $this->user, $text);
    }


    //创建群
    public function chatCreate($chatName)
    {
        if ($chatName) {
            $client = $this->_client;
            return $client->chatCreate($chatName, $this->user, [$this->user]);
        } else {
            return null;
        }
    }

    //获取组织架构
    public function getDepartment()
    {
        $client = $this->_client;
        return $client->getDepartment();
    }

    //获取部门成员
    public function getDepartmentUser($department_id)
    {
        $client = $this->_client;
        return $client->getDepartmentUser($department_id);
    }

    //获取所有用户
    public function getAllUser()
    {
        $client = $this->_client;
        $department = $client->getDepartment();
        $user = [];
        foreach ($department as $v) {
            $user = array_merge($client->getDepartmentUser($v['id']), $user);
        }
        return $user;
    }
}