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
    public $token_path;

    private $_client;

    public function __construct($company = 'wdjf')
    {
        $config = \Yii::$app->params['ding_config'][$company];
        if (!$config) {
            throw new \Exception('缺少核心参数');
        }
        $this->log_path = __DIR__ . '/../../console/runtime/ding/';
        if (!file_exists($this->log_path)) {
            if (!mkdir($this->log_path)) {
                throw new \Exception('无法创建目录 ' . $this->log_path);
            }
        }
        $this->token_path = $this->log_path;
        if (
            !isset($config['corp_id'])
            || !isset($config['corp_secret'])
            || !isset($config['agent_id'])

        ) {
            throw new \Exception('缺少核心参数');
        }
        $this->corpid = $config['corp_id'];
        $this->corpsecret = $config['corp_secret'];
        $this->agentid = $config['agent_id'];
        if (isset($config['chat_id'])) {
            $this->chatId = $config['chat_id'];
        }
        if (isset($config['user'])) {
            $this->user = $config['user'];
        }
        $client = new Client([
            'corpid' => $this->corpid,
            'corpsecret' => $this->corpsecret,
            'agentid' => $this->agentid,
            'log_path' => $this->log_path,
            'token_path' => $this->token_path,
        ]);
        $this->_client = $client;
    }

    /**
     * 向指定用户发送消息
     * @param string $content       需要发送的消息
     * @param array $user    需要发送的用户或者用户列表,默认使用配置的用户列表
     */
    public function sendToUsers($content,$user = [])
    {
        if (empty($user)) {
            $user = \Yii::$app->params['ding_notify_list'];
        }
        $user = implode('|',$user);
        $content = '[系统通知 '.date('Y-m-d H:i:s').']'. "\n" . $content;
        $text = new Text($content);
        $client = $this->_client;
        $client->companyMessageSend($text, $user);
    }

    /**
     * 钉钉向群发送消息服务
     * @param string $content   要发送的消息
     * @param string $chatId    群组ID, 默认使用配置文件中的群
     * @param string $userId    发消息的用户ID， 默认使用配置文件中的用户
     */
    public function charSentText($content, $chatId = '', $userId = '')
    {
        $client = $this->_client;
        $text = new Text($content);
        if (empty($chatId)) {
            $chatId = $this->chatId;
        }
        if (empty($userId)) {
            $userId = $this->user;
        }
        $client->chatSend($chatId, $userId, $text);
    }


    /**
     * 创建群
     * @param string $chatName  群名字
     * @param string $userId    群主ID，默认使用配置文件中的用户
     * @param array $userList   群成员，默认只用群主，可以加其他人，也可以在建群之后手工添加
     * @return null|string
     */
    public function chatCreate($chatName, $userId = '', $userList = [])
    {
        if ($chatName) {
            $client = $this->_client;
            if (empty($userId)) {
                $userId = $this->user;
            }
            if (!in_array($userId, $userList)) {
                $userList[] = $userId;
            }
            return $client->chatCreate($chatName, $userId, $userList);
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