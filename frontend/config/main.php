<?php

$params = array_merge(
    require(__DIR__.'/../../common/config/params.php'),
    require(__DIR__.'/../../common/config/params-local.php'),
    require(__DIR__.'/params.php'),
    require(__DIR__.'/params-local.php')
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
            'errorAction' => 'site/error',
        ],
        'assetManager' => [
            'bundles' => [
                'yii\bootstrap\BootstrapAsset' => [
                    'css' => [],
                ],
                'yii\bootstrap\BootstrapPluginAsset' => [
                    'js' => [],
                ],
                'yii\web\JqueryAsset' => [
                    'sourcePath' => '@app/assets',
                    'js' => [
                        'vendor/jquery/dist/jquery.min.js',
                    ],
                ],
            ],

        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'enableCookieValidation' => false,
        ],
    ],
    'modules' => [
        'news' => [
            'class' => 'app\modules\news\Module',
        ],
        'user' => [
            'class' => 'app\modules\user\Module',
        ],
        'product' => [
            'class' => 'app\modules\product\Module',
        ],
    ],
    'params' => $params,
    'as AjaxJsonFormat' => [
        'class' => 'common\components\RequestBehavior',
    ],
];
