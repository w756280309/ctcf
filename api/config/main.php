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
        'user' => [
            'identityClass' => 'common\\models\\user\\User',
        ],
        'urlManager' => [
            'enableStrictParsing' => true,
            'rules' => [
                'rest/recharges' => 'v1/rest/recharge/list',
                'rest/loans' => 'v1/rest/loan/list',
                'rest/loans/<id:\d+>' => 'v1/rest/loan/get',
                'rest/loans/<id:\d+>/ump' => 'v1/rest/loan/ump',
                'rest/accounts' => 'v1/rest/account/list',
                'rest/borrowers' => 'v1/rest/borrower/list',
                'rest/borrowers/<id:\d+>' => 'v1/rest/borrower/get',
                'rest/borrowers/<id:\d+>/ump' => 'v1/rest/borrower/ump',
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

                'notify/mall/init' => 'v1/notify/mall/init',//积分商场扣除积分接口
                'notify/mall/result' => 'v1/notify/mall/result',//积分商城通知回调接口

                'app/splashes' => 'v1/app/splash/show',//app闪屏页查询接口

                'go/<key>' => 'source/referral-link/go',//分销商链接地址转跳
            ],
        ],
    ],
    'modules' => [
        'v1' => [
            'class' => 'api\\modules\\v1\\Module',
        ],
        'source' => [
            'class' => 'Wcg\\Growth\\Integration\\Yii2Module\\Module',
        ]
    ],
    'params' => $params,
    'language' => 'zh-CN',
];
