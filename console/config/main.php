<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'console\controllers',
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['trace'],
                    'categories' => ['sms'],
                    'logFile' => '@app/runtime/logs/sms/sms'. date('Ymd').'.log',
                    'maxFileSize' => 1024*2,
                    'logVars' => ['trace'],
                    'prefix' => function ($message) {
                        return "";//去掉消息返回的[IP address][User ID][Session ID][Severity Level]
                    }
               ],
            ],
        ],
        'beanstalk'=>[
            'class' => 'udokmeci\yii2beanstalk\Beanstalk',
            'host'=> "127.0.0.1", // default host
            'port'=>11300, //default port
            'connectTimeout'=> 1,
            'sleep' => false, // or int for usleep after every job 
        ],
    ],
    'params' => $params,
    'controllerMap' => [
        'worker'=>[
            'class' => 'app\commands\WorkerController',
        ]
 
    ],
];
