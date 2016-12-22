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
    'components' => [
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
            ],
        ],
    ],
    'modules' => [
        'v1' => [
            'class' => 'api\\modules\\v1\\Module',
        ],
    ],
    'params' => $params,
    'language' => 'zh-CN',
];
