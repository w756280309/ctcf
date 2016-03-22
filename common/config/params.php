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
    'pc_cat' => ['1'=>'温银通','2'=>'温政盈',],
    'refund_method' => ['1' => '到期本息', '2' => '按月付息,到期本息', '3' => '按季付息,到期本息', '4' => '按半年付息,到期本息', '5' => '按年付息,到期本息'],//
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
        'notify' => [
            'bind_notify_url' => "https://m.wenjf.com/user/qpay/notify/backend",
            'bind_ret_url' => "https://m.wenjf.com/user/qpay/notify/frontend",
            'rec_notify_url' => "https://m.wenjf.com/user/qpay/qpaynotify/backend",
            'rec_ret_url' => "https://m.wenjf.com/user/qpay/qpaynotify/frontend",
            'order_notify_url' => "https://m.wenjf.com/order/qpay/notify/backend",
            'order_ret_url' => "https://m.wenjf.com/order/qpay/notify/frontend",
            'mer_draw_notify_url' => 'https://mwjf.wenjf.com/order/drawnotify/notify',  //融资方提现回调地址
            'draw_ret_url' => "https://m.wenjf.com/user/qpay/drawnotify/frontend",
            'draw_notify_url' => "https://m.wenjf.com/user/qpay/drawnotify/backend",
            'draw_apply_notify_url' => "https://m.wenjf.com/user/qpay/drawnotify/apply",
            'rec_pc_ret_url' => 'http://www.wenjf.com/user/bpay/brecharge/frontend-notify',
            'rec_pc_notify_url' => 'http://www.wenjf.com/user/bpay/brecharge/backend-notify',
            'draw_pc_ret_url' => 'https://www.wenjf.com/user/draw/draw-notify',   //投资用户提现返回结果页
            'mer_recharge_ret_url' => 'https://mwjf.wenjf.com/user/bpay/brecharge/frontend-notify',  //融资用户充值回调接口地址
            'mer_recharge_notify_url' => 'https://mwjf.wenjf.com/user/bpay/brecharge/backend-notify',

            'mianmi_ret_url' => 'https://mwjf.wenjf.com/user/qpay/agreementnotify/frontend',  //免密支付前台通知地址
            'mianmi_url' => 'https://mwjf.wenjf.com/user/qpay/agreementnotify/backend',//免密支付后台通知地址
        ],
    ],
    'm_assets_base_uri' => "https://static.wenjf.com/m/",
    'enable_baidu_tongji' => false,
];
