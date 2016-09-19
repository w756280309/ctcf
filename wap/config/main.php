<?php

$params = array_merge(
    require(__DIR__.'/../../common/config/params.php'),
    require(__DIR__.'/../../common/config/params-local.php'),
    require(__DIR__.'/params.php'),
    require(__DIR__.'/params-local.php')
);

return [
    'id' => 'app-wap',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'language' => 'zh-CN',
    'controllerNamespace' => 'app\controllers',
    'components' => [
        'user' => [
            'identityClass' => 'common\models\user\User',
            'enableAutoLogin' => true,
            'loginUrl' => ['/site/login'],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
                //联动日志记录
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['trace'],
                    'categories' => ['umplog'],
                    'logFile' => '@app/runtime/logs/ump/ump'.date('Ymd').'.log',
                    'maxFileSize' => 1024 * 2,
                    'logVars' => ['trace'],
                    'prefix' => function ($message) {
                        return '';//去掉消息返回的[IP address][User ID][Session ID][Severity Level]
                    },
               ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'view' => [
            'class' => 'common\view\WapView',
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'enableCookieValidation' => false,
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
    ],
    'modules' => [
        'deal' => [
            'class' => 'app\modules\deal\Module',
        ],
        'order' => [
            'class' => 'app\modules\order\Module',
        ],
        'user' => [
            'class' => 'app\modules\user\Module',
        ],
        'system' => [
            'class' => 'app\modules\system\Module',
        ],
        'promotion' => [
            'class' => 'wap\modules\promotion\Module',
        ],
        'credit' => [
            'class' => 'wap\modules\credit\Module',
        ],
    ],
    'params' => $params,
    'as requestBehavior' => [
        'class' => \common\components\RequestBehavior::className(),
    ],
    'as userAccountAccessControl'=> \common\filters\UserAccountAcesssControl::className(),
];
