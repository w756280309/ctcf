<?php

$params = array_merge(
    require(__DIR__.'/../../common/config/params.php'),
    require(__DIR__.'/../../common/config/params-local.php'),
    require(__DIR__.'/params.php'),
    require(__DIR__.'/params-local.php')
);

return [
    'id' => 'app-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'modules' => [],
    'language' => 'zh-CN',
    'homeurl' => '/site/index/',
    'components' => [
        'user' => [
            'identityClass' => 'common\models\adminuser\Admin',
            'enableAutoLogin' => true,
            'loginUrl' => ['/login'],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'info'], //,'trace'
                    'logVars' => ['_SERVER'], //_SERVER  _GET 等
                    //'logFile' => __DIR__.'/../../data/log/'.date('ymdhi').'.log',
                    //'messages'=>['test', 4, 'application', time()]
                ],
            ],
            'flushInterval' => 100,   // default is 1000
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
//        'as beforeRequest' => [
//            'class' => 'yii\filters\AccessControl',
//            'rules' => [
//                [
//                    'actions' => ['login', 'error'],
//                    'allow' => true,
//                ],
//                [
//
//                    'allow' => true,
//                    'roles' => ['@'],
//                ],
//            ],
//        ],
    ],
    'params' => $params,
    'defaultRoute' => '/frame/index',
    'modules' => [
        'news' => [
            'class' => 'backend\modules\news\Module',
        ],
        'product' => [
            'class' => 'backend\modules\product\Module',
        ],
        'order' => [
            'class' => 'backend\modules\order\Module',
        ],
        'user' => [
            'class' => 'backend\modules\user\Module',
        ],
        'adv' => [
            'class' => 'backend\modules\adv\Module',
        ],
        'adminuser' => [
            'class' => 'app\modules\adminuser\Module',
        ],
        'channel' => [
            'class' => 'backend\modules\channel\Module',
        ],
        'system' => [
            'class' => 'backend\modules\system\Module',
        ],
        'repayment' => [
            'class' => 'backend\modules\repayment\Module',
        ],
        'datatj' => [
            'class' => 'backend\modules\datatj\Module',
        ],
        'fenxiao' => [
            'class' => 'backend\modules\fenxiao\Module',
        ],
    ],
];
