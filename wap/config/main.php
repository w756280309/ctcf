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
                    'levels' => ['info'],
                    'categories' => ['umplog'],
                    'logFile' => '@app/runtime/logs/ump/ump'.date('Ymd').'.log',
                    'maxFileSize' => 1024 * 2,
                    'logVars' => ['trace'],
                    'prefix' => function ($message) {
                        return '';//去掉消息返回的[IP address][User ID][Session ID][Severity Level]
                    },
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info'],
                    'categories' => ['notify'],
                    'logFile' => '@app/runtime/logs/notify/mall/mall_notify'. date('Ym').'.log',
                    'maxFileSize' => 1024*2,
                    'logVars' => ['info'],
                    'prefix' => function ($message) {
                        return "";//去掉消息返回的[IP address][User ID][Session ID][Severity Level]
                    }
                ],
                //用户信息变更日志
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info'],
                    'categories' => ['user_log'],
                    'logFile' => '@app/runtime/logs/user/user_status'.date('Ymd').'.log',
                    'maxFileSize' => 1024 * 2,
                    'logVars' => ['trace'],
                    'prefix' => function ($message) {
                        return '';//去掉消息返回的[IP address][User ID][Session ID][Severity Level]
                    },
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info'],
                    'categories' => ['promo_log'],
                    'logFile' => '@app/runtime/logs/promo/user_join_'.date('Ymd').'.log',
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
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation

            'class' => \common\components\WebRequest::className(),
            'enableCookieValidation' => false,
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'db_cache' => [
            'class' => 'yii\caching\DbCache',
            'cacheTable' => 'cache_entry',
        ],
        'urlManager' => [
            'rules' => [
                'promotion/p1701/luodiye' => 'promotion/p1701/luodiye',
                'promotion/p170126/luodiye' => 'promotion/p170126/luodiye',
                'promotion/smashegg' => 'promotion/smashegg',
                'promotion/<key>' => 'promotion/promo',
                'promotion/<key>/luodiye' => 'promotion/promo/luodiye',
            ],
        ],
        'assetManager' => [
            'bundles' => [
                'yii\web\JqueryAsset' => [
                    'basePath' => '@webroot',
                    'baseUrl' => $params['m_assets_base_uri'],
                    'js' => [
                        'js/jquery.min.js',
                    ],
                ],
            ],
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
        'mall' => [
            'class' => 'wap\modules\mall\Module',
        ],
        'risk' => [
            'class' => 'Wcg\Xii\Risk\Module',
        ],
    ],
    'params' => $params,
    'as requestBehavior' => [
        'class' => \common\components\RequestBehavior::className(),
    ],
    'as userAccountAccessControl'=> \common\filters\UserAccountAcesssControl::className(),
    'as getOpenIdBehavior' => [
        'class' => \common\components\GetOpenIdBehavior::className(),
    ],
    //'as superviseAccessFilter' => \common\filters\SuperviseAccessFilter::className(),//监管控制：未实名无法查看首页和列表页
    'as logFirstVisitTime' => \common\filters\logFirstVisitTime::className(),//记录用户首次访问时间
];
