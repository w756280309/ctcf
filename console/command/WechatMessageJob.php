<?php

namespace console\command;

use common\models\queue\Job;
use Doctrine\Common\Cache\RedisCache;
use EasyWeChat\Foundation\Application;
use Yii;

class WechatMessageJob extends Job
{
    public function run()
    {
        $data = $this->getParam('data');
        $linkUrl = $this->getParam('linkUrl');
        $templateId = $this->getParam('templateId');
        $openId = $this->getParam('openId');
        if (null === $openId || empty($data)) {
            return false;
        }
        $options = [
            'debug' => false,       //所有日志均不会记录
            'app_id' => Yii::$app->params['weixin']['appId'],
            'secret' => Yii::$app->params['weixin']['appSecret'],
            /*'log' => [
                'level' => 'debug',
                'file' => Yii::getAlias('@runtime').'/logs/wechat.log',
            ],*/
        ];
        $app = new Application($options);
        $cache = new RedisCache();
        // 创建 redis 实例
        $redis = new \Redis();
        $params = Yii::$app->params['redis_config'];
        $redis->connect($params['hostname'], $params['port']);
        $cache->setRedis($redis);
        $app->access_token->setCache($cache);
        $app->notice
            ->to($openId)
            ->uses($templateId)
            ->data($data)
            ->andUrl($linkUrl)
            ->send();

        return true;
    }
}
