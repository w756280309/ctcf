<?php

return [
    'timeZone' => 'Asia/Shanghai',
    'class' => 'yii\\web\\Application',
    'id' => 'test',
    'basePath' => __DIR__,
    'components' => [
        'db' => [
            'class' => 'yii\\db\\Connection',
            'dsn' => 'mysql:host=localhost;dbname=wdjf',
            'username' => 'wdjf',
            'password' => 'wdjf',
            'charset' => 'utf8',
        ],
    ],
];
