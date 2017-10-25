<?php

namespace common\service;

use common\helpers\HttpHelper;
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

        return(HttpHelper::doGet($url));
    }
}