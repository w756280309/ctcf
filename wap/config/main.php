<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-wap',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'language'=>'zh-CN',
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
                    'js'=>[]
                ],
//                'yii\web\JqueryAsset' => [
//                    'js'=>[]
//                ],
            ],

        ],
    ],

    'modules' => [
        'news' => [
            'class' => 'app\modules\news\Module',
        ],
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
    ],
    'params' => $params,
];
