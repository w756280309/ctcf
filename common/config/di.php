<?php

\Yii::$container->set('account_service', 'common\\service\\AccountService');
\Yii::$container->set('paginator', 'Zii\\Paginator\\Paginator');
\Yii::$container->set('PayGate\\Ump\\LoggerInterface', 'common\\models\\Logger');
\Yii::$container->set('ump', 'PayGate\\Ump\\Client', [
    \Yii::$app->params['ump']['apiUrl'],
    \Yii::$app->params['ump']['merchant_id'],
    \Yii::$app->params['ump']['wdjf_key'],
    \Yii::$app->params['ump']['ump_cert'],
    isset(Yii::$app->request->hostInfo) ? Yii::$app->request->hostInfo : "",
    \Yii::$app->params['clientOption'],
]);
\Yii::$container->set('txClient', 'Tx\\TxClient');
Yii::$container->set('db_queue', 'Queue\\DbQueue');
\Yii::$container->set('wxClient', 'WeiXin\\Client', [
    \Yii::$app->params['weixin']['appId'],
    \Yii::$app->params['weixin']['appSecret'],
]);
Yii::$container->set('alisms', \common\service\AliSmsService::class, [
    Yii::$app->params['sms.ali.accessKeyId'],
    Yii::$app->params['sms.ali.accessKeySecret'],
]);
Yii::$container->set('dingTalk', DingNotify\Clients::class, [
    [
        'wdjf' => [
            'corpid' => env('DING_TALK_CONFIG_WDJF_CROP_ID'),
            'corpsecret' => env('DING_TALK_CONFIG_WDJF_CROP_SECRET'),
            'agentid' => env('DING_TALK_CONFIG_WDJF_DEFAULT_AGENT_ID'),
        ],
    ],
]);
Yii::$container->set('weixin_wdjf', function(){
    $options = [
        'debug' => false,       //所有日志均不会记录
        'app_id' => Yii::$app->params['weixin']['appId'],
        'secret' => Yii::$app->params['weixin']['appSecret'],
        'guzzle' => [
            'timeout' => 15,
        ],
    ];
    $app = new \EasyWeChat\Foundation\Application($options);
    $cache = new \Doctrine\Common\Cache\RedisCache();
    // 创建 redis 实例
    $redis = new \Redis();
    $params = Yii::$app->params['redis_config'];
    $redis->connect($params['hostname'], $params['port']);
    if ($params['password']) {
        $redis->auth($params['password']);
    }
    $cache->setRedis($redis);
    $app->access_token->setCache($cache);
    return $app;
});
