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
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'page404/index',
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'enableCookieValidation' => false,
        ],
        'view' => [
            'class' => 'common\view\WapView',
        ],
    ],
    'modules' => [
        'user' => [
            'class' => 'frontend\modules\user\Module',
        ],
        'deal' => [
            'class' => 'frontend\modules\deal\Module',
        ],
        'order' => [
            'class' => 'frontend\modules\order\Module',
        ],
    ],
    'params' => $params,
    'as AjaxJsonFormat' => [
        'class' => \common\components\RequestBehavior::className(),
    ],
    'as userAccountAccess' => \common\filters\UserAccountAcesssControl::className(),
];
