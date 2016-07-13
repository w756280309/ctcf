<?php

return [
    'adminEmail' => 'zhanghongyu@wangcaigu.com',
    'supportEmail' => 'zhanghongyu@wangcaigu.com',
    'user.passwordResetTokenExpire' => 3600,
    'deal_status' => [
        1 => '预告期',
        2 => '募集中',
        3 => '已售罄',
        4 => '流标',
        5 => '收益中',
        6 => '已还清',
        7 => '募集结束',
    ],
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
    'drawFee' => 2, //单位元，提现手续费
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
        'config' => [
            'APP_ID' => '8a48b551525cdd3301526207479a0bcc',
        ],
    ],
    'contact_tel' => '400-101-5151',
    'white_list' => ['15810036547', '18518154492'],
    'pc_cat' => ['1' => '温盈金', '2' => '温盈宝'],
    'refund_method' => ['1' => '到期本息', '2' => '按月付息，到期本息', '3' => '按季付息，到期本息', '4' => '按半年付息，到期本息', '5' => '按年付息，到期本息', '6' => '按自然月付息，到期本息', '7' => '按自然季度付息，到期本息', '8' => '按自然半年付息，到期本息', '9' => '按自然年付息，到期本息'], //
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
            'max' => 1000000000,
        ],
    ],
    'm_assets_base_uri' => 'https://static.wenjf.com/m/',
    'pc_assets_base_uri' => 'https://static.wenjf.com/pc/',
    'upload_base_uri' => 'https://static.wenjf.com/',
    'enable_analytics' => false,
    'enable_dev_helpers' => false,
    'category_type' => [
        '1' => '文章分类',
        '9' => '其他分类',
    ],
    'clientOption' => [
        'host' => [
            'api' => 'https://api.wenjf.com/',
            'frontend' => 'https://www.wenjf.com/',
            'wap' => 'https://m.wenjf.com/',
            'app' => 'https://app.wenjf.com/',
        ],
    ],
    //易保全正式环境配置
    'bao_quan_config' => [
        'services_url' => 'http://api.ebaoquan.org/services',
        'app_key' => '8f5ca3eb5cbac210',
        'app_secret' => 'cc7c180b7e016f9802b1bcb9e493450e',
    ],
    'mock_sms' => false,
    'enable_ebaoquan' => true,

    /*wap seo*/
    'wap_page_title' => "温都金服-温州报业传媒旗下理财平台",
    'wap_page_keywords' => "温都金服,温金服,温州报业传媒理财,报纸理财,温州理财,温都理财",
    'wap_page_descritpion' => "温都金服(m.wenjf.com)为温州报业传媒旗下理财平台手机版，定位于市民身边的财富管家。联合卓越各类金融机构，资金采用第三方独立托管，为个人提供稳健收益的理财产品",

    /*pc seo*/
    'pc_page_title' => "温都金服【官网】—温州报业传媒旗下理财平台",
    'pc_page_keywords' => "温都金服,温金服,温州报业传媒理财,报纸理财,温州理财",
    'pc_page_desc' => "温都金服(wenjf.com)为温州报业传媒旗下理财平台，定位于市民身边的财富管家。联合卓越各类金融机构，资金采用第三方独立托管，为个人提供稳健收益的理财产品",

    'baidu_tongji_key' => '',   //温都金服百度统计key
    'ga_tracking_id' => '',  //GA跟踪ID
];
