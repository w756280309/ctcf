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
Yii::$container->set('laraq', function () {
    $queue = new \Illuminate\Queue\Capsule\Manager();
    $queue->getContainer()->singleton('redis', function () {
        $config = [
            'client' => 'phpredis',
            'default' => [
                'host' => Yii::$app->params['redis.host'],
                'port' => Yii::$app->params['redis.port'],
                'password' => Yii::$app->params['redis.password'],
                'database' => Yii::$app->params['redis.database'],
            ],
        ];
        return new \Illuminate\Redis\RedisManager('phpredis', $config);
    });

    $queue->addConnection([
        'driver' => 'redis',
        'connection' => 'default',
        'queue' => 'default',
        'retry_after' => 90,
    ]);

    return $queue;
});
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
