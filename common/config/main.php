<?php

return [
    'timeZone' => 'Asia/Shanghai',
    'vendorPath' => dirname(dirname(__DIR__)).'/vendor',
    //'runtimePath'=>'@common/runtime',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
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
