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
        '6' => '手续费'
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
        'manbiao' => 60752,
        'tixian_succ' => 60753,
        'tixian_err' => 60757,
        'toubiao' => 60760,
        'recharge' => 60764,
        'huikuan' => 60766,
        'yzm' => 12552
    ],
    'contact_tel' => '025-8570-8888',
    'white_list' => ['15810036547','18518154492'],
    'pc_cat' => ['1'=>'短期产品','2'=>'政府平台',],
    'refund_method' => ['1' => '到期本息', '2' => '按月付息', '3' => '按季付息', '4' => '按半年付息'],
    'page_info' => [
        'beian' => '@版权归温都金服开发团队所有',
    ],
    'ump_mer_recharge_ret_url' => 'http://b.wdjf.njfae.com.cn/user/bpay/brecharge/frontend-notify',
    'ump_mer_recharge_notify_url' => 'http://b.wdjf.njfae.com.cn/user/bpay/brecharge/backend-notify',
    'ump' => [
        'notify' => [
            'bind_notify_url' => "http://1.202.48.153:8001/user/qpay/notify/backend",
            'bind_ret_url' => "http://1.202.48.153:8001/user/qpay/notify/frontend",
            'rec_notify_url' => "http://1.202.48.153:8001/user/qpay/qpaynotify/backend",
            'rec_ret_url' => "http://1.202.48.153:8001/user/qpay/qpaynotify/frontend",
            'order_notify_url' => "http://1.202.48.153:8001/order/qpay/notify/backend",
            'order_ret_url' => "http://1.202.48.153:8001/order/qpay/notify/frontend",
            'mer_draw_notify_url' => 'http://1.202.98.24:8002/order/drawnotify/notify',  //融资方提现回调地址
            'draw_ret_url' => "http://1.202.102.12:8001/user/qpay/drawnotify/frontend",
            'draw_notify_url' => "http://1.202.102.12:8001/user/qpay/drawnotify/backend",
            'draw_apply_notify_url' => "http://1.202.102.12:8001/user/qpay/drawnotify/apply", 
        ],
    ],
];
