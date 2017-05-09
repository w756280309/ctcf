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
    ],
];
