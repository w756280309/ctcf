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
                'v1/Recharges' => 'v1/recharge/list',
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
