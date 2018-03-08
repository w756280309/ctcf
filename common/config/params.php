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
        'institutionId' => '',  //机构号码 测试账号
        'apiUrl' => null,
        'clientKeyPath' => '',
        'clientKeyExportPass' => '',
        'cfcaCertPath' => '',
    ],
    'drawFee' => 2, //单位元，提现手续费
    'draw_free_limit' => 5, //提现自然月免手续费限制，若超过50次，则收取手续费
    'bank' => require(__DIR__ . '/banks.php'),
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
        'transfer_all_success' => '',   //全部转让成功
    ],

    'platform_info.contact_tel' => '',             //客服电话
    'platform_info.customer_service_time' => '',   //客服时间
    'platform_info.company_address' => '',   //公司地址
    'platform_info.company_name' => '',   //公司全称
    'platform_info.company_seal_176' => '',   //公司签章图片-认购确认函
    'platform_info.company_seal_640' => '',   //公司签章图片-交易资产凭证
    'platform_info.order_cert_logo' => '',    //认购协议页面上部logo
    'pc_cat' => [],

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
        'beian' => '',
    ],
    'ump' => [
        'apiUrl' => '',
        'merchant_id' => '',//温都在联动的商户号
        'wdjf_key' => '',
        'ump_cert' => '',
        'draw' => [
            'min' => null,
            'max' => null,
        ],
    ],
    //工信部保权密钥
    'miit' => [
        'wdjf_private_key' => __DIR__ . env('WDJF_PRIVATE_KEY'),
        'wdjf_public_key' => __DIR__ . env('WDJF_PUBLIC_KEY'),
        'MiitPublicKey' => __DIR__ . env('MIITPUBLICKEY'),
        'MiitContractUrl' => env('MIITCONTRACTURL'),
        'MiitGetHetongUrl' => env('MIITGETHETONGURL'),
        'MiitGetTicketUrl' => env('MIITGETTICKETURL'),
        'idcode' => env('IDCODE'),
        'aesKey' => env('AESKEY'),
    ],
    'udesk_key' => env('UDESK_KEY'),
    //todo-待添加
    'weixin' => [
        'appId' => '',
        'appSecret' => '',
        'token' => 'dx1234',
    ],
    'm_assets_base_uri' => '',
    'pc_assets_base_uri' => '',
    'upload_base_uri' => '',
    'fe_base_uri' => '',
    'enable_dev_helpers' => false,
    'category_type' => [
        '1' => '文章分类',
        '9' => '其他分类',
    ],
    'clientOption' => [
        'host' => [
            'api' => '',  //温都金服API正式站地址
            'frontend' => '', //温都金服PC端正式站地址
            'wap' => '',    //温都金服WAP端正式站地址
            'app' => '',  //温都金服APP端正式站地址
            'tx' => '',   //交易系统正式站地址
            'tx_www' => '',   //交易系统正式站地址[外网地址]
        ],
    ],
    //易保全正式环境配置
    'bao_quan_config' => [
        'services_url' => '',
        'app_key' => '',
    ],
    'mock_sms' => false,   //发送短信开关(当为true的时候,除了白名单里面的手机号,其他手机号一律不实际发送短信;当为false的时候,所有手机号都可以发送短信)
    'enable_ebaoquan' => true,

    /*wap seo*/
    'wap_page_title' => '',
    'wap_page_keywords' => '',
    'wap_page_descritpion' => '',

    /*pc seo*/
    'pc_page_title' => '',
    'pc_page_keywords' => '',
    'pc_page_desc' => '',

    // Web统计
    'analytics_enabled' => false,
    'analytics_pk_wap_id' => '', // piwik ID
    'analytics_pk_app_id' => '', // piwik ID
    'analytics_pk_pc_id' => '', // piwik ID
    'analytics_ga_id' => '',  // GA跟踪ID
    'analytics_gio_id' => '', // growingio统计key

    /* 债券相关配置信息 */
    'credit' => [
        'hold_days' => null,   //持有天数
        'repeatedly_hold_days' => null,   //债权多次转让时候最低持有天数
        'transfer_period' => null, //转让周期
        'max_discount_rate' => null,    //最高折让率
        'trade_count_limit' => null,    //可转让次数
        'fee_rate' => null,    //手续费费率
        'loan_fenqi_limit' => null,    //分期项目的资产发起转让条件限制，单位：月
        'loan_daoqi_limit' => null,   //不分期（到期本息）项目的资产发起转让条件限制，单位：天
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
            'chat_id' => '',//新建群ID，群名称：温都系统通知群
            'user' => ''//默认发送消息用,姓名：莫荻
        ]
    ],
    'base_domain' => '',    //根域名，banner图处使用
    'feature_credit_note_on' => true,  //债权转让功能开关,当为false时,隐藏进入债权功能页面入口
    'feature_credit_note_whitelist_uids' => [], //债权转让白名单
    'xs_money_limit' => 10000,//新手专享标最大可投金额，0为不限制
    'xs_trade_limit' => 1,//新手专享标投资次数
    'mall_settings' => [
        'url' => 'http://www.duiba.com.cn',
        'app_key' => '',
        'app_secret' => '',
    ],
    'ding_notify_list' => [],   //钉钉通知名单 实例 [ '用户姓名' => '用户在钉钉的ID',],具体数值在 data/wdjf_ding_users.json 中查
    'wdjf_security_key' => '',  //用户信息加密使用的key保存文件名
    'backend_tmp_share_path' => '/home/wjf/nfs',//后台和工作机共享目录, 存放临时文件, 会配置文件清理机制
    'showCharityAmount' => false,  //首页是否显示慈善合计金额标志位
    'wx.msg_tpl.draw_success' => 'eMnJo8ZdS3UaADDJDoeyi_HqvgZft04_4mMTFw5hZsY',  //微信提现成功消息模板ID
    'wx.msg_tpl.order_success' => '96YFmA0x8p1hYpeGG3rkq6AVjPPnKokFYASLwxao0xQ', //微信交易成功消息模板ID(非正式)
    'wx.msg_tpl.add_points_for_connect_wx' => 'f4KfVJdqIA8Bqu480kY5m_X3GGf57MUZnNIfMMmVgxg',  //微信绑定成功送积分消息模板
    'wx.msg_tpl.repayment_success' => '6a-yjoNe-hUDILHU9bfFn5aGoU4-UukLWYXNaBwEtJU',//微信消息模板：回款消息
    'white_open_id' => [],
    'mock_wechat_msg' => false,
    'njfae_upload_dir' => 'upload/',   //对应南金交上传的文件夹相对路径，实际使用时添加日期"20161020/"
    'channelSn_in_njfae' => 23,   //温都金服在南金交的编号，转让文件名的使用及其内部的营业部会遇到
    'njfae_save_filePath' => '@tmp',    //对接南金交生成的文件保存的相对路径，即在根目录下创建njfae文件夹
    'promo_api_url' => 'https://api.wenjf.com/promo/reward/cash',
    'redis.host' => null,// redis host
    'redis.port' => null,//redis port
    'redis.password' => null,//redis password
    'redis.database' => null,//redis database
    'ding_notify.user_list.create_note' => [

    ],//发起满足条件转让钉钉提醒通知用户名单(在温都金服公司钉钉ID), 参考 WDJF/data/wdjf_ding_users.json 中用户配置
    //阿里云通讯
    'sms.ali.accessKeyId' => env('SMS_ALI_ACCESS_KEY_ID'),
    'sms.ali.accessKeySecret' => env('SMS_ALI_ACCESS_KEY_SECRET'),
    //阿里短信模板
    'sms.ali.template.register' => env('SMS_ALI_TEMPLATE_REGISTER'),
    'sms.ali.template.forget.password' => env('SMS_ALI_TEMPLATE_FIND_PASSWORD'),
    //沃动短信通道配置
    'WoDong' => [
        'userid' => env('WODONGUSERID'),
        'password' => env('WODONGPASSWORD'),
        'account' => env('WODONGACCOUNT'),
        'url' => env('WODONGURL'),
    ],
    //黑名单，不发送短信
    'NoSendSms' => env('NOSENDSMS'),
    'redis_config' => [
        'hostname' => env('QUEUE_REDIS_HOST'),
        'port' => env('QUEUE_REDIS_PORT'),
        'password' => env('QUEUE_REDIS_PASSWORD'),
    ],
    //立合接口配置
    'li_he' => [
        'url' => env('LIHE_URL'),
        'key' => env('LIHE_KEY')
    ],
    //u_desk在线客服功能配置
    'u_desk' => [
        'im_user_key' => env('UDESK_IM_USER_KEY'),
        'code' => env('UDESK_CODE'),
        'link' => env('UDESK_LINK'),
    ],
    //网银充值’黑名单‘，不再发送奖励，用户id组成的数组
    'online-bank-blacklist' => [],
    'piwik_auth_key' => env('PIWIK_TOKEN_AUTH'),
    //配置部分用户的资产（资产+余额+代金券金额），让个别客户不受标的可见逻辑控制，['mobile' => 18210261704, 'money' => 100000],
    'partial_user_assets' => [],
    //线下积分翻倍活动
    'offline_points' => [
        'start' => null,  //开始日期 例：2012-12-12
        'end' => null,    //结束日期 例：2012-12-18
        'number' => null, //积分倍数 例：2
    ],
    //微信公众号推送配置
    'wechat_push' => [
        'hello_message' => null,    //欢迎信息
        'click_message' => [],    //点击公众号菜单的提示信息
    ],
    'old_site_visible_user_id' => '',
];
