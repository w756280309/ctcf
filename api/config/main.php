<?php

$params = array_merge(
    require(__DIR__.'/../../common/config/params.php'),
    require(__DIR__.'/../../common/config/params-local.php'),
    require(__DIR__.'/params.php'),
    require(__DIR__.'/params-local.php')
);

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                    'logVars' => [],
                    'enableRotation' => false,
                    'prefix' => function ($message) {
                        return "";//去掉消息返回的[IP address][User ID][Session ID][Severity Level]
                    }
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['info'],
                    'categories' => ['notify'],
                    'logFile' => '@app/runtime/logs/notify/notify'. date('Ym').'.log',
                    'maxFileSize' => 1024*2,
                    'logVars' => [],
                    'enableRotation' => false,
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
                    'maxFileSize' => 1024 * 2,
                    'logVars' => [],
                    'enableRotation' => false,
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
                    'logVars' => [],
                    'enableRotation' => false,
                    'prefix' => function ($message) {
                        return '';//去掉消息返回的[IP address][User ID][Session ID][Severity Level]
                    },
                ],
            ],
        ],
        'user' => [
            'identityClass' => 'common\\models\\user\\User',
        ],
        'request' => [
            'class' => 'Zii\Http\Request',
            'enableCsrfValidation' => false, // 禁用CSRF
            'enableCookieValidation' => false, // 禁用Cookie值的加密
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'urlManager' => [
            'enableStrictParsing' => false,
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'rest/recharges' => 'v1/rest/recharge/list',
                'rest/loans' => 'v1/rest/loan/list',
                'rest/loans/<id:\d+>' => 'v1/rest/loan/get',
                'rest/loans/<id:\d+>/ump' => 'v1/rest/loan/ump',
                'rest/accounts' => 'v1/rest/account/list',
                'rest/borrowers' => 'v1/rest/borrower/list',
                'rest/borrowers/<id:\d+>' => 'v1/rest/borrower/get',
                'rest/borrowers/ump/' => 'v1/rest/borrower/ump',//查看商户在联动账户信息　可以使用　$userId 也可以使用　$umpId
                'rest/lenders/<id:\d+>' => 'v1/rest/lender/get',
                'rest/lenders/<id:\d+>/ump' => 'v1/rest/lender/ump',
                'rest/ordtx/<type:\d+>/<id:\d+>/ump' => 'v1/rest/ordertx/ump',
                'rest/lenderstats' => 'v1/rest/lender-stats/export',
                'rest/lenderstats_export' => 'v1/rest/lender-stats/new-export',
                'rest/settle/<type:\d+>/<date:\d+>' => 'v1/rest/settle/show',

                'pos/notify' => 'v1/pos/notify',

                'app/serverts' => 'v1/app/server/timestamp',
                'app/logout' => 'v1/app/auth/logout',
                'app/user' => 'v1/app/user/info',
                'app/appver' => 'v1/app/deploy/appver',
                'app/token' => 'v1/app/server/tokeninfo',

                'notify/draw/frontend' => 'v1/notify/draw/frontend',
                'notify/draw/backend' => 'v1/notify/draw/backend',

                'notify/updatecard/frontend' => 'v1/notify/updatecard/frontend',
                'notify/updatecard/backend' => 'v1/notify/updatecard/backend',

                'app/share/template' => 'v1/app/share/template',

                'promo/reward/cash' => 'v1/promo/reward/cash',

                'app/splashes' => 'v1/app/splash/show',//app闪屏页查询接口
                'app/tabs' => 'v1/app/splash/tab-image',//app底部tab图片接口
                'app/demotion' => 'v1/app/server/demotion',//app降级(完全使用H5)接口

                'go/<key>' => 'source/referral-link/go',//分销商链接地址转跳
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'v2/site/error',
        ],
    ],
    'modules' => [
        'v1' => [
            'class' => 'api\\modules\\v1\\Module',
        ],
        'v2' => [
            'class' => 'api\\modules\\v2\\Module',
        ],
        'tx' => [
            'class' => 'api\\modules\\tx\\Module',
        ],
        'source' => [
            'class' => 'Wcg\\Growth\\Integration\\Yii2Module\\Module',
        ],
        'njq' => [
            'class' => 'api\\modules\\njq\\Module',
        ],
        'asset' => [
            'class' => 'api\\modules\\asset\\Module',
        ]
    ],
    'params' => $params,
    'language' => 'zh-CN',
];
