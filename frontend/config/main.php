<?php

$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'language' => 'zh-CN',
    'controllerNamespace' => 'frontend\controllers',
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
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'page404/index',
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'enableCookieValidation' => false,
        ],
        'assetManager' => [
            'bundles' => [
                'yii\web\JqueryAsset' => [
                    'basePath' => '@webroot',
                    'baseUrl' => $params['pc_assets_base_uri'],
                    'js' => [
                        'js/jquery-1.12.4.min.js',
                    ],
                ],
            ],
        ],
        'view' => [
            'class' => 'common\view\WapView',
            'theme' => [
                'pathMap' => [
                    '@app/views' => '@view/themes/'.$_ENV['BW_APP'].'/pc/views',
                    '@app/modules' => '@view/themes/'.$_ENV['BW_APP'].'/pc/modules',
                ],
            ]
        ],
    ],
    'modules' => [
        'user' => [
            'class' => 'frontend\modules\user\Module',
        ],
        'deal' => [
            'class' => 'frontend\modules\deal\Module',
        ],
        'mall' => [
            'class' => 'frontend\modules\mall\Module',
        ],
        'order' => [
            'class' => 'frontend\modules\order\Module',
        ],
        'credit' => [
            'class' => 'frontend\modules\credit\Module',
        ],
        'risk' => [
            'class' => 'Wcg\Xii\Risk\Module',
        ],
    ],
    'params' => $params,
    'as AjaxJsonFormat' => [
        'class' => \common\components\RequestBehavior::className(),
    ],
    'as userAccountAccess' => \common\filters\UserAccountAcesssControl::className(),
    'as superviseAccessFilter' => \common\filters\SuperviseAccessFilter::className(),//监管控制：未实名无法查看首页和列表页
    'as logFirstVisitTime' => \common\filters\LogFirstVisitTime::className(),//记录用户首次访问时间
];
