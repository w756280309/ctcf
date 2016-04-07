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
                'recharges' => 'v1/recharge/list',
                'loans' => 'v1/loan/list',
                'loans/<id:\d+>' => 'v1/loan/get',
                'loans/<id:\d+>/ump' => 'v1/loan/ump',
                'accounts' => 'v1/account/list',
                'borrowers' => 'v1/borrower/list',
                'borrowers/<id:\d+>' => 'v1/borrower/get',
                'borrowers/<id:\d+>/ump' => 'v1/borrower/ump',
                'lenders/<id:\d+>' => 'v1/lender/get',
                'lenders/<id:\d+>/ump' => 'v1/lender/ump',
                'ordtx/<type:\d+>/<id:\d+>/ump' => 'v1/ordertx/ump',
                'pos/notify' => 'v1/pos/notify',
                'lenderstats' => 'v1/lender-stats/export',
                'serverts' => 'v1/app/server/timestamp',
                'logout' => 'v1/app/auth/logout',
                'user' => 'v1/app/user/info',
                'appver' => 'v1/app/deploy/appver',
                'token' => 'v1/app/server/tokeninfo',
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
