<?php

return [
    'timeZone' => 'Asia/Shanghai',
    'vendorPath' => dirname(dirname(__DIR__)).'/vendor',
    //'runtimePath'=>'@common/runtime',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'db_cache' => [
            'class' => 'yii\caching\DbCache',
            'cacheTable' => 'cache_entry',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
        ],
        'functions' => [
            'class' => 'common\components\Functions',
        ],
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host='.env('DB_HOST').';dbname='.env('DB_DATABASE'),
            'username' => env('DB_USERNAME'),
            'password' => env('DB_PASSWORD'),
            'charset' => 'utf8',
        ],
        'db_fin' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host='.env('DB_FIN_HOST').';dbname='.env('DB_FIN_DATABASE'),
            'username' => env('DB_FIN_USERNAME'),
            'password' => env('DB_FIN_PASSWORD'),
            'charset' => 'utf8',
        ],
        'db_tx' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host='.env('DB_TX_HOST').';dbname='.env('DB_TX_DATABASE'),
            'username' => env('DB_TX_USERNAME'),
            'password' => env('DB_TX_PASSWORD'),
            'charset' => 'utf8',
        ],
        'db_mig' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=localhost;dbname=mig;port:3306',
            'username' => 'mig',
            'password' => 'mig123456',
            'charset' => 'utf8',
        ],
    ],
];
