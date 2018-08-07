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
    'language' => 'zh-CN',
    'homeurl' => '/site/index/',
    'components' => [
        'request' => [
            'enableCookieValidation' => false,
        ],
        'view' => [
            'class' => 'common\view\BackendView',
        ],
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
                    'logVars' => [], //_SERVER  _GET 等
                    //'logFile' => __DIR__.'/../../data/log/'.date('ymdhi').'.log',
                    //'messages'=>['test', 4, 'application', time()]
                ],
                [
                    'class' => 'common\log\JsonTarget',
                    'levels' => ['info', 'error', 'warning'],
                    'logVars' => [],
                    'enableRotation' => false,
                    'categories' => ['xiaowei'],
                    'logFile' => '@app/runtime/logs/xiaowei/xiaowei'.date('Ymd').'.log',
                ],
            ],
            'flushInterval' => 100,   // default is 1000
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'assetManager'=>[
            'bundles' => [
                'yii\web\JqueryAsset' => [
                    'jsOptions' => [
                        'position' => \yii\web\View::POS_HEAD,
                    ]
                ]
            ]
        ],
    ],
    'as requestBehavior' => [
        'class' => \common\components\RequestBehavior::className(),
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
        'coupon' => [
            'class' => 'backend\modules\coupon\Module',
        ],
        'adminupload' => [
            'class' => 'backend\modules\adminupload\Module',
        ],
        'offline' => [
            'class' => 'backend\modules\offline\Module',
        ],
        'growth' => [
            'class' => 'backend\modules\growth\Module',
        ],
        'o2o' => [
            'class' => 'backend\modules\o2o\Module',
        ],
        'crm' => [
            'class' => 'Wcg\Xii\Crm\Module',
        ],
        'source' => [
            'class' => 'Wcg\Growth\Integration\Yii2Module\Module',
        ],
        'wechat' => [
            'class' => 'backend\modules\wechat\Module',
        ],
    ],
    'as LoginStatusFilter' => [
        'class' => \common\filters\LoginStatusFilter::className(),   //用户登录状态，保持各端只有一个有效的会话
        'except' => [
            'login/logout',
        ],
    ],
];
