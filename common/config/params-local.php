<?php

return [
    'cfca' => [
        'institutionId' => '000005', //机构号码 测试账号
        'apiUrl' => 'http://echo.wcg.cn/cfca/recv/QxypZk5zRY4UGsGPU1miIPXqMPD',
        'clientKeyPath' => dirname(__DIR__).'/cfca_test/wdjf.p12',
        'clientKeyExportPass' => 'fake',
        'cfcaCertPath' => dirname(__DIR__).'/cfca_test/cfca.crt',
    ],
];
