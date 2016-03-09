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
                'users/<id:\d+>' => 'v1/user/get',
                'users/<id:\d+>/ump' => 'v1/user/ump',
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
