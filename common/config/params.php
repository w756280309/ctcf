<?php

return [
    'adminEmail' => 'zhanghongyu@wangcaigu.com',
    'supportEmail' => 'zhanghongyu@wangcaigu.com',
    'user.passwordResetTokenExpire' => 3600,
    'deal_status' => [
        1 => '预告期',
        2 => '进行中',
        3 => '满标',
        4 => '流标',
        5 => '还款中',
        6 => '已还清',
        7 => '募集结束',
    ],
    'productonline' => ['1'=>'预告期','2'=>'募集中','3'=>'满标','4'=>'流标','5'=>'还款中','6'=>'已还清',7=>'募集结束'],
    'mingxi' => [
        '0' => '充值',
        '1' => '提现申请',
        '100' => '提现申请失败',
        '101' => '提现成功',
        '102' => '提现失败',
        '103' => '手续费',
        '104' => '手续费',
        '2' => '投资',
        '3' => '放款',
        '4' => '回款',
        '400' => '还款',
        '5' => '流标',
        '7' => '资金转入理财账户',
        '51' => '超投撤标',
        '6' => '手续费',
        '8' => '充值-线下pos',
    ],
    'cfca' => [
        'institutionId' => '000005', //机构号码 测试账号
        'apiUrl' => null,
        'clientKeyPath' => dirname(__DIR__).'/cfca_test/wdjf.p12',
        'clientKeyExportPass' => 'fake',
        'cfcaCertPath' => dirname(__DIR__).'/cfca_test/cfca.crt',
    ],
    'drawFee' => 2,//单位元，提现手续费
    'settlement' => [
        /*结算账号设置 start */
        'bank_id' => '424', //南京银行编号
        'accountname' => '温都金服',
        'branchname' => '南京银行鸿信大厦支行',
        'accountnumber' => '017601205400022',
        'province' => '江苏省',
        'city' => '南京市',
        /*结算账号设置 end*/
    ],
    'bank' => require(__DIR__.'/banks.php'),
    'sms' => [
        'manbiao' => 71622,
        'tixian_succ' => 60753,
        'tixian_err' => 60757,
        'tixian_apply' => 71400,
        'toubiao' => 70040,
        'recharge' => 60764,
        'yzm' => 70052,
        'forget' => 70036,
        'daoqibenxi' => 70049,
        'fenqihuikuan' => 70048,
        'lfenqihuikuan' => 70046,
    ],
    'contact_tel' => '400-101-5151',
    'white_list' => ['15810036547','18518154492'],
    'pc_cat' => ['1'=>'温盈金','2'=>'温盈宝',],
    'refund_method' => ['1' => '到期本息', '2' => '按月付息，到期本息', '3' => '按季付息，到期本息', '4' => '按半年付息，到期本息', '5' => '按年付息，到期本息'],//
    'page_info' => [
        'beian' => '@版权归温都金服开发团队所有',
    ],
    'ump' => [
        'apiUrl' => 'http://pay.soopay.net/spay/pay/payservice.do',
        'merchant_id' => '7001209',
        'wdjf_key' => __DIR__.'/payment/ump/wdjf_prod.key',
        'ump_cert' => __DIR__.'/payment/ump/ump_prod.crt',
        'draw' => [
            'min' => 10,
            'max' => 1000000000
        ],
    ],
    'm_assets_base_uri' => "https://static.wenjf.com/m/",
    'pc_assets_base_uri' => "https://static.wenjf.com/pc/",
    'enable_baidu_tongji' => false,
    'enable_dev_helpers' => false,
    'category_type'=>[
        '1'=>'文章分类',
        '9'=>'其他分类'
    ]
];
