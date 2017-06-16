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
        '105' => '现金红包',
        '106' => '买入转让',
        '107' => '手续费',
        '108' => '卖出转让',
        '109' => '回款',
        '2' => '投资',
        '3' => '放款',
        '4' => '回款',
        '400' => '还款',
        '5' => '流标',
        '7' => '资金转入理财账户',
        '51' => '超投撤标',
        '6' => '手续费',
        '8' => '充值-线下pos',
        '110' => '撤销投资',
    ],
    'cfca' => [
        'institutionId' => '000005', //机构号码 测试账号
        'apiUrl' => null,
        'clientKeyPath' => dirname(__DIR__).'/cfca_test/wdjf.p12',
        'clientKeyExportPass' => 'fake',
        'cfcaCertPath' => dirname(__DIR__).'/cfca_test/cfca.crt',
    ],
    'drawFee' => 2, //单位元，提现手续费
    'draw_free_limit' => 5, //提现自然月免手续费限制，若超过50次，则收取手续费
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
    'sms_white_list' => [],   //用户白名单功能只在mock_sms为true的时候有效，即mock_sms为true时，除了白名单里面设置的手机号，其他手机号一律不发短信
    'sms' => [
        'config' => [
            'APP_ID' => '8a48b551525cdd3301526207479a0bcc',
        ],
        'manbiao' => 71622,
        'tixian_succ' => 60753,
        'tixian_err' => 60757,
        'tixian_apply' => 71400,
        'toubiao' => 70040,
        'yzm' => 70052,
        'forget' => 70036,
        'daoqibenxi' => 70049,
        'fenqihuikuan' => 70048,
        'lfenqihuikuan' => 70046,
        'invite_bonus' => 105818, //邀请好友奖励
        'coupon_expire' => 105820, //代金券3天后失效
        'register_coupon' => 113105, //注册用户送代金券
        'coupon_reminder' => 113109,//代金券过期提醒
        'intro_redpacket' => 138631, //大转盘抽奖，未投资用户抽中现金红包时发放说明
        'roundabout_redpacket' => 138632, //现金红包已发到指定账户上的通知短信
        'birthday_coupon' => 141156,//用户生日给用户发送代金券
        'debx_repay' => 177805,//等额本息还款
    ],
    'contact_tel' => '400-101-5151',
    'pc_cat' => ['1' => '温盈金', '2' => '温盈宝'],
    'refund_method' => [
        '1' => '到期本息',
        '2' => '按月付息，到期本息',
        '3' => '按季付息，到期本息',
        '4' => '按半年付息，到期本息',
        '5' => '按年付息，到期本息',
        '6' => '按自然月付息，到期本息',
        '7' => '按自然季度付息，到期本息',
        '8' => '按自然半年付息，到期本息',
        '9' => '按自然年付息，到期本息',
        '10' => '等额本息',
    ],
    'page_info' => [
        'beian' => '@版权归温都金服开发团队所有',
    ],
    'ump' => [
        'apiUrl' => 'https://pay.soopay.net/spay/pay/payservice.do',
        'merchant_id' => '7001209',//温都在联动的商户号
        'wdjf_key' => __DIR__.'/payment/ump/wdjf_prod.key',
        'ump_cert' => __DIR__.'/payment/ump/ump_prod.crt',
        'draw' => [
            'min' => 10,
            'max' => 1000000000,
        ],
    ],
    //todo-待添加
    'weixin' => [
        'appId' => '',
        'appSecret' => '',
    ],
    'm_assets_base_uri' => 'https://static.wenjf.com/m/',
    'pc_assets_base_uri' => 'https://static.wenjf.com/pc/',
    'upload_base_uri' => 'https://static.wenjf.com/',
    'fe_base_uri' => 'https://static.wenjf.com/v2/',
    'enable_dev_helpers' => false,
    'category_type' => [
        '1' => '文章分类',
        '9' => '其他分类',
    ],
    'clientOption' => [
        'host' => [
            'api' => 'https://api.wenjf.com/',  //温都金服API正式站地址
            'frontend' => 'https://www.wenjf.com/', //温都金服PC端正式站地址
            'wap' => 'https://m.wenjf.com/',    //温都金服WAP端正式站地址
            'app' => 'https://app.wenjf.com/',  //温都金服APP端正式站地址
            'tx' => 'https://tx.wenjf.com/',   //交易系统正式站地址
            'tx_www' => 'https://tx-www.wenjf.com/',   //交易系统正式站地址[外网地址]
        ],
    ],
    //易保全正式环境配置
    'bao_quan_config' => [
        'services_url' => 'http://api.ebaoquan.org/services',
        'app_key' => '8f5ca3eb5cbac210',
    ],
    'mock_sms' => false,   //发送短信开关(当为true的时候,除了白名单里面的手机号,其他手机号一律不实际发送短信;当为false的时候,所有手机号都可以发送短信)
    'enable_ebaoquan' => true,

    /*wap seo*/
    'wap_page_title' => "温都金服-温州报业传媒旗下理财平台",
    'wap_page_keywords' => "温都金服,温金服,温州报业传媒理财,报纸理财,温州理财,温都理财",
    'wap_page_descritpion' => "温都金服(m.wenjf.com)为温州报业传媒旗下理财平台手机版，定位于市民身边的财富管家。联合卓越各类金融机构，资金采用第三方独立托管，为个人提供稳健收益的理财产品",

    /*pc seo*/
    'pc_page_title' => "温都金服【官网】—温州报业传媒旗下理财平台",
    'pc_page_keywords' => "温都金服,温金服,温州报业传媒理财,报纸理财,温州理财",
    'pc_page_desc' => "温都金服(wenjf.com)为温州报业传媒旗下理财平台，定位于市民身边的财富管家。联合卓越各类金融机构，资金采用第三方独立托管，为个人提供稳健收益的理财产品",

    // Web统计
    'analytics_enabled' => false,
    'analytics_pk_wap_id' => '', // piwik ID
    'analytics_pk_app_id' => '', // piwik ID
    'analytics_pk_pc_id' => '', // piwik ID
    'analytics_ga_id' => '',  // GA跟踪ID
    'analytics_gio_id' => '', // growingio统计key

    /* 债券相关配置信息 */
    'credit_trade' => [
        'hold_days' => 30,   //持有天数
        'repeatedly_hold_days' => 30,   //债权多次转让时候最低持有天数
        'max_discount_rate' => 3,    //最高折让率
        'trade_count_limit' => 1,    //可转让次数
        'fee_rate' => 0.003,    //手续费费率
        'listing_duration' => 3,   //转让周期
        'loan_fenqi_limit' => 6,    //分期项目的资产发起转让条件限制，单位：月
        'loan_daoqi_limit' => 180,   //不分期（到期本息）项目的资产发起转让条件限制，单位：天
    ],
    //钉钉账号相关配置
    'ding_config' => [
        'nj' => [
            'corp_id' => '',//默认南京易投贷corpid
            'corp_secret' => '',//默认南京易投贷secret
            'agent_id' => '',//默认技术部
            'chat_id' => '',//新建群ID，群名称：温都金服系统消息自动通知群
            'user' => ''//默认发送消息用户id，姓名：史阳
        ],
        'wdjf' => [
            'corp_id' => '',//默认温都corpid
            'corp_secret' => '',//默认温都secret
            'agent_id' => '',//默认运营群
            'chat_id' => "",//新建群ID，群名称：温都系统通知群
            'user' => ''//默认发送消息用,姓名：莫荻
        ]
    ],
    'base_domain' => 'wenjf.com',//根域名，banner图处使用
    'feature_credit_note_on' => true,  //债权转让功能开关,当为false时,隐藏进入债权功能页面入口
    'feature_credit_note_whitelist_uids' => [], //债权转让白名单
    'xs_money_limit' => 10000,//新手专享标最大可投金额，0为不限制
    'xs_trade_limit' => 1,//新手专享标投资次数
    'mall_settings' => [
        'url' => 'http://www.duiba.com.cn',
        'app_key' => '',
        'app_secret' => '',
    ],
    'ding_notify_list' => [],//钉钉通知名单 实例 [ '用户姓名' => '用户在钉钉的ID',],具体数值在 data/wdjf_ding_users.json 中查
    'wdjf_security_key' => __DIR__ . '/payment/security/wdjf_prod.key',//温都金服用户信息加密使用的key保存文件名
    'backend_tmp_share_path' => '/home/wjf/nfs',//后台和工作机共享目录, 存放临时文件, 会配置文件清理机制
    'showCharityAmount' => false,  //首页是否显示慈善合计金额标志位
    'draw_message_template_id' => 'eMnJo8ZdS3UaADDJDoeyi_HqvgZft04_4mMTFw5hZsY',  //微信提现成功消息模板ID
    'order_message_template_id' => '96YFmA0x8p1hYpeGG3rkq6AVjPPnKokFYASLwxao0xQ', //微信交易成功消息模板ID(非正式)
    'wx.msg_tpl.draw_success' => 'f4KfVJdqIA8Bqu480kY5m_X3GGf57MUZnNIfMMmVgxg',  //微信绑定成功送积分消息模板
    'white_open_id' => [],
];
