<?php

return [
    'components' => [
        'redis_session' => [
            'class' => 'yii\redis\Connection',
            'hostname' => 'r-bp1cc58258c8bdf4.redis.rds.aliyuncs.com',
            'port' => 6379,
            'database' => 1, // 设为1避免误写，默认是0
            'password' => null, // 不允许提交密码到代码仓库
        ],
        // 如果要知道session数据是不是写到了redis，用`KEYS wdjf*`命令
        'session' => [
            'class' => 'yii\redis\Session',
            'redis' => 'redis_session', // redis连接
            'keyPrefix' => 'wdjf_ss_',
        ],
    ],
];
