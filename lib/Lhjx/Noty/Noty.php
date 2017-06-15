<?php

namespace Lhjx\Noty;

use console\command\WechatMessageJob;
use Yii;

class Noty
{
    public static function send(WechatTemplateMessageInterface $msg)
    {
        $res = true;

        try {
            $job = new WechatMessageJob($msg->getParams());
            Yii::$container->get('db_queue')->pub($job);
        } catch (\Exception $e) {
            $res = false;
        }

        return $res;
    }
}