<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__.'/params-local.php')
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
                    'enableRotation' => false,
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info'],
                    'categories' => ['recharge_log'],
                    'logFile' => '@app/runtime/logs/borrower/recharge'. date('Ymd').'.log',
                    'enableRotation' => false,
                    'logVars' => [],
                    'prefix' => function ($message) {
                        return "";//去掉消息返回的[IP address][User ID][Session ID][Severity Level]
                    }
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['trace'],
                    'categories' => ['sms'],
                    'logFile' => '@app/runtime/logs/sms/sms'. date('Ymd').'.log',
                    'enableRotation' => false,
                    'logVars' => [],
                    'prefix' => function ($message) {
                        return "";//去掉消息返回的[IP address][User ID][Session ID][Severity Level]
                    }
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['trace'],
                    'categories' => ['loan_order'],
                    'logFile' => '@app/runtime/logs/order/loan_order' . date('Ym') . '.log',
                    'enableRotation' => false,
                    'logVars' => [],
                    'prefix' => function ($message) {
                        return "";//去掉消息返回的[IP address][User ID][Session ID][Severity Level]
                    }
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['trace'],
                    'categories' => ['bao_quan'],
                    'logFile' => '@app/runtime/logs/bao_quan/bao_quan' . date('Ym') . '.log',
                    'enableRotation' => false,
                    'logVars' => [],
                    'prefix' => function ($message) {
                        return "";//去掉消息返回的[IP address][User ID][Session ID][Severity Level]
                    }
                ],
                //联动日志记录
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info'],
                    'categories' => ['umplog'],
                    'logFile' => '@app/runtime/logs/ump/ump'.date('Ymd').'.log',
                    'enableRotation' => false,
                    'logVars' => [],
                    'prefix' => function ($message) {
                        return '';//去掉消息返回的[IP address][User ID][Session ID][Severity Level]
                    },
                ],
                //用户信息变更日志
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info'],
                    'categories' => ['user_log'],
                    'logFile' => '@app/runtime/logs/user/user_status'.date('Ymd').'.log',
                    'enableRotation' => false,
                    'logVars' => [],
                    'prefix' => function ($message) {
                        return '';//去掉消息返回的[IP address][User ID][Session ID][Severity Level]
                    },
                ],
                //队列日志
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info'],
                    'categories' => ['queue'],
                    'logFile' => '@app/runtime/logs/queue/queue_'.date('Ymd').'.log',
                    'enableRotation' => false,
                    'logVars' => [],
                    'prefix' => function ($message) {
                        return '';//去掉消息返回的[IP address][User ID][Session ID][Severity Level]
                    },
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['trace'],
                    'categories' => ['credit_order'],
                    'logFile' => '@app/runtime/credit/credit_order'.date('Ymd').'.log',
                    'enableRotation' => false,
                    'logVars' => [],
                    'prefix' => function ($message) {
                        return '';//去掉消息返回的[IP address][User ID][Session ID][Severity Level]
                    },
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info'],
                    'categories' => ['command'],
                    'logFile' => '@app/runtime/command_'.date('Ymd').'.log',
                    'enableRotation' => false,
                    'logVars' => [],
                    'prefix' => function ($message) {
                        return '';//去掉消息返回的[IP address][User ID][Session ID][Severity Level]
                    },
                ],
                //立合同步错误日志
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info'],
                    'categories' => ['lihe'],
                    'logFile' => '@app/runtime/logs/exchange/lihe_'.date('Ymd').'.log',
                    'enableRotation' => false,
                    'logVars' => [],
                    'prefix' => function ($message) {
                        return '';//去掉消息返回的[IP address][User ID][Session ID][Severity Level]
                    },
                ],
            ],
        ],
        'njfaeFtp' => [
            'class' => '\gftp\FtpComponent',
            'driverOptions' =>  [
                'class' => \gftp\FtpProtocol::valueOf('sftp')->driver,
                'user' => 'wdjf',
                'publicKeyFile' => __DIR__.'/njfae/keys/id_rsa.pub',  //ssh公钥路径
                'privateKeyFile' => __DIR__.'/njfae/keys/id_rsa', //ssh私钥路径
                'host' => '123.57.157.2',
                'port' => 22,
                'timeout' => 120,
            ]
        ],
        'beanstalk'=>[
            'class' => 'udokmeci\yii2beanstalk\Beanstalk',
            'host'=> "127.0.0.1", // default host
            'port'=>11300, //default port
            'connectTimeout'=> 1,
            'sleep' => false, // or int for usleep after every job
        ],
        'mutex' => [
            'class' => 'yii\mutex\FileMutex'
        ],
    ],
    'modules' => [
        'crm' => 'Wcg\Xii\Crm\Module',
        'njfae' => 'console\modules\njfae\Module',
        'tx' => 'console\modules\tx\Module',
        'ctcf' => 'console\modules\ctcf\Module',
    ],
    'params' => $params,
];
