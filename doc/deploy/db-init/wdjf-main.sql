-- --------------------------------------------------------
-- Host:                         rr-bp1z450fa58j6w0tv.mysql.rds.aliyuncs.com
-- Server version:               5.6.16-log - Source distribution
-- Server OS:                    Linux
-- HeidiSQL Version:             9.5.0.5196
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping structure for table wjf.accesstoken
CREATE TABLE IF NOT EXISTS `accesstoken` (
  `uid` int(10) NOT NULL COMMENT '用户id',
  `expireTime` int(10) NOT NULL COMMENT '过期时间【暂定一个月】',
  `token` varchar(50) NOT NULL COMMENT '用户登录token',
  `clientType` char(10) NOT NULL COMMENT '客户端类型',
  `deviceName` varchar(50) NOT NULL COMMENT '设备名称',
  `clientInfo` varchar(100) NOT NULL COMMENT '客户端信息 ',
  `create_time` int(10) NOT NULL,
  `updateTime` datetime DEFAULT NULL,
  PRIMARY KEY (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='APP用户登录Token表';

-- Data exporting was unselected.
-- Dumping structure for table wjf.admin
CREATE TABLE IF NOT EXISTS `admin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `role_sn` char(10) NOT NULL COMMENT '角色sn',
  `username` char(32) NOT NULL COMMENT '管理员用户名',
  `real_name` varchar(20) DEFAULT NULL COMMENT '管理员姓名',
  `email` varchar(50) NOT NULL COMMENT '管理员Email',
  `password_hash` char(128) NOT NULL COMMENT '用户密码hash',
  `auth_key` char(128) DEFAULT NULL COMMENT 'cookie权限认证key',
  `last_login_ip` char(15) DEFAULT '' COMMENT '最后一次登录ip',
  `last_login_time` int(11) DEFAULT '0' COMMENT '最后一次登录时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 0-锁定 1-正常',
  `updated_at` int(10) unsigned NOT NULL COMMENT '创建时间',
  `created_at` int(10) unsigned NOT NULL COMMENT '更新时间',
  `udesk_email` varchar(32) DEFAULT NULL,
  `affiliator_id` int(11) DEFAULT NULL COMMENT '门店id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=90 DEFAULT CHARSET=utf8 COMMENT='管理员用户表';

-- Data exporting was unselected.
-- Dumping structure for table wjf.admin_auth
CREATE TABLE IF NOT EXISTS `admin_auth` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `admin_id` int(4) NOT NULL COMMENT '管理者id',
  `role_sn` char(24) DEFAULT '' COMMENT '角色sn',
  `auth_sn` char(24) DEFAULT '' COMMENT '权限sn',
  `auth_name` varchar(30) NOT NULL COMMENT '权限名称',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态0冻结，1激活',
  `updated_at` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=80392 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.admin_log
CREATE TABLE IF NOT EXISTS `admin_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `ip` varchar(30) DEFAULT NULL,
  `tableName` varchar(30) DEFAULT NULL,
  `primaryKey` varchar(32) DEFAULT NULL,
  `allAttributes` text,
  `changeSet` text,
  PRIMARY KEY (`id`),
  KEY `admin_log_table_name` (`tableName`),
  KEY `admin_log_admin_id` (`admin_id`),
  KEY `admin_log_primary_key` (`primaryKey`)
) ENGINE=InnoDB AUTO_INCREMENT=1644638 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.admin_op_log
CREATE TABLE IF NOT EXISTS `admin_op_log` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `op_name` varchar(50) NOT NULL COMMENT '操作名称',
  `admin_id` int(4) NOT NULL COMMENT '管理者id',
  `role_id` int(4) NOT NULL COMMENT '角色id',
  `role` int(4) NOT NULL COMMENT '角色',
  `auth` varchar(20) NOT NULL COMMENT '权限编号',
  `auth_name` varchar(50) NOT NULL COMMENT '权限',
  `method` char(10) DEFAULT 'get' COMMENT '请求方式',
  `url` varchar(50) DEFAULT NULL COMMENT '地址',
  `get_params` text COMMENT 'get参数',
  `post_params` text COMMENT 'post参数',
  `ip` char(20) DEFAULT NULL COMMENT 'ip地址',
  `updated_at` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.admin_upload
CREATE TABLE IF NOT EXISTS `admin_upload` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(30) NOT NULL,
  `link` varchar(100) NOT NULL,
  `allowHtml` smallint(1) DEFAULT '0',
  `isDeleted` smallint(1) DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=120 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.adv
CREATE TABLE IF NOT EXISTS `adv` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `sn` varchar(10) DEFAULT NULL COMMENT 'sn',
  `title` varchar(50) DEFAULT NULL COMMENT '标题',
  `status` tinyint(1) DEFAULT '1' COMMENT '0显示，1隐藏',
  `link` varchar(255) DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL COMMENT '图片说明',
  `show_order` int(4) DEFAULT NULL COMMENT '排序值',
  `del_status` tinyint(1) DEFAULT '0' COMMENT '0正常，1删除',
  `isDisabledInApp` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否在手机APP端显示',
  `creator_id` int(4) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `showOnPc` tinyint(1) DEFAULT NULL,
  `share_id` int(11) DEFAULT NULL,
  `type` smallint(6) DEFAULT '0',
  `media_id` int(11) NOT NULL,
  `start_date` datetime DEFAULT NULL,
  `timing` tinyint(1) DEFAULT '0',
  `investLeast` decimal(14,2) NOT NULL DEFAULT '0.00' COMMENT '最低投资可见',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=367 DEFAULT CHARSET=utf8 COMMENT='广告表';

-- Data exporting was unselected.
-- Dumping structure for table wjf.affiliate_campaign
CREATE TABLE IF NOT EXISTS `affiliate_campaign` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `trackCode` varchar(255) DEFAULT NULL,
  `affiliator_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `trackCode` (`trackCode`)
) ENGINE=InnoDB AUTO_INCREMENT=137 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.affiliator
CREATE TABLE IF NOT EXISTS `affiliator` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `picPath` varchar(255) DEFAULT NULL,
  `isRecommend` tinyint(1) DEFAULT '0',
  `isO2O` tinyint(1) DEFAULT '0',
  `parentId` int(11) DEFAULT NULL COMMENT '父级分销商ID',
  `isDel` tinyint(1) DEFAULT '0' COMMENT '是否删除',
  `isBranch` tinyint(1) DEFAULT '0' COMMENT '是否是门店(网点)',
  `hideSensitiveinfo` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否隐藏敏感信息',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=128 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.annual_report
CREATE TABLE IF NOT EXISTS `annual_report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `totalProfit` decimal(14,2) DEFAULT '0.00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11210 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.appliament
CREATE TABLE IF NOT EXISTS `appliament` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userID` int(11) unsigned NOT NULL COMMENT '用户ID',
  `appointmentTime` int(11) unsigned NOT NULL COMMENT '预约时间',
  `appointmentAward` decimal(14,2) unsigned NOT NULL COMMENT '预约金额',
  `appointmentObjectId` smallint(6) unsigned NOT NULL COMMENT '预约类型',
  `appointmentAwardType` smallint(6) unsigned NOT NULL COMMENT '获奖类型，1：喜卡，2：加息券',
  PRIMARY KEY (`id`),
  KEY `userId` (`userID`)
) ENGINE=InnoDB AUTO_INCREMENT=1654 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.app_meta
CREATE TABLE IF NOT EXISTS `app_meta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `key` varchar(64) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.asset
CREATE TABLE IF NOT EXISTS `asset` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `source` varchar(255) NOT NULL COMMENT '渠道信息',
  `createTime` datetime NOT NULL COMMENT '创建时间',
  `borrowerName` varchar(255) NOT NULL COMMENT '融资方',
  `sn` varchar(255) NOT NULL COMMENT '小微系统资产编号',
  `amount` decimal(14,2) NOT NULL COMMENT '资产金额',
  `repaymentType` int(11) NOT NULL COMMENT '还款方式',
  `rate` decimal(10,4) NOT NULL COMMENT '资产打包利率',
  `expires` int(11) NOT NULL COMMENT '产品期限',
  `expiresType` smallint(6) NOT NULL COMMENT '期限单位,1-天 2-月',
  `borrowerIdCardNumber` varchar(255) NOT NULL COMMENT '融资方身份证',
  `borrowerType` int(11) NOT NULL COMMENT '融资方身份,0-个人,1-企业',
  `extendInfo` text NOT NULL COMMENT '扩展信息',
  `status` smallint(6) NOT NULL COMMENT '状态',
  `issue` tinyint(1) NOT NULL COMMENT '是否可发标 1-不可发 0-可发',
  `itemInfo` text COMMENT '拆分资产信息',
  PRIMARY KEY (`id`),
  UNIQUE KEY `sn` (`sn`),
  KEY `borrowerName` (`borrowerName`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.auth
CREATE TABLE IF NOT EXISTS `auth` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `sn` char(24) DEFAULT '' COMMENT '编号',
  `psn` char(24) DEFAULT '' COMMENT '父级sn',
  `level` tinyint(1) DEFAULT '1' COMMENT '等级',
  `auth_name` varchar(50) NOT NULL COMMENT '权限',
  `path` varchar(100) DEFAULT '' COMMENT '地址（有权限的话部署path）',
  `type` tinyint(1) DEFAULT '1' COMMENT '状态1菜单，2功能',
  `auth_description` varchar(100) NOT NULL COMMENT '权限说明',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态0冻结，1激活',
  `order_code` tinyint(1) DEFAULT '0' COMMENT '排序字段',
  `updated_at` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=270 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.award
CREATE TABLE IF NOT EXISTS `award` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `createTime` datetime DEFAULT NULL,
  `promo_id` int(11) DEFAULT NULL,
  `ticket_id` int(11) DEFAULT NULL,
  `amount` decimal(14,2) DEFAULT NULL COMMENT '奖励面值',
  `ref_type` varchar(255) DEFAULT NULL,
  `ref_id` int(11) DEFAULT NULL,
  `reward_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=140744 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.bank
CREATE TABLE IF NOT EXISTS `bank` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `bankName` varchar(100) NOT NULL,
  `gateId` varchar(50) NOT NULL COMMENT '银行英文简称',
  PRIMARY KEY (`id`),
  UNIQUE KEY `gateId` (`gateId`)
) ENGINE=InnoDB AUTO_INCREMENT=441 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.bankcardbin
CREATE TABLE IF NOT EXISTS `bankcardbin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cardBin` varchar(50) DEFAULT NULL COMMENT '卡号唯一判断数字',
  `cardType` varchar(20) NOT NULL COMMENT '卡类型',
  `bankId` int(11) DEFAULT NULL COMMENT '银行id',
  `binDigits` int(11) DEFAULT NULL COMMENT '判断长度',
  `cardDigits` int(11) NOT NULL COMMENT '卡长度',
  PRIMARY KEY (`id`),
  UNIQUE KEY `card_bin` (`cardBin`)
) ENGINE=InnoDB AUTO_INCREMENT=1451 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.bank_card_update
CREATE TABLE IF NOT EXISTS `bank_card_update` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `sn` varchar(32) NOT NULL,
  `oldSn` varchar(32) NOT NULL,
  `uid` int(10) NOT NULL,
  `epayUserId` varchar(60) NOT NULL,
  `bankId` varchar(30) NOT NULL,
  `bankName` varchar(255) NOT NULL,
  `cardHolder` varchar(30) NOT NULL,
  `cardNo` varchar(50) NOT NULL,
  `status` int(1) NOT NULL DEFAULT '0',
  `created_at` int(10) DEFAULT NULL,
  `updated_at` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sn` (`sn`),
  KEY `idx_uid` (`uid`),
  KEY `idx_oldSn` (`oldSn`)
) ENGINE=InnoDB AUTO_INCREMENT=3119 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.bao_quan_queue
CREATE TABLE IF NOT EXISTS `bao_quan_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `itemId` int(11) DEFAULT NULL,
  `status` int(1) DEFAULT '1',
  `created_at` int(10) DEFAULT NULL,
  `updated_at` int(10) DEFAULT NULL,
  `itemType` varchar(20) DEFAULT 'loan',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_item` (`itemId`,`itemType`)
) ENGINE=InnoDB AUTO_INCREMENT=135139 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.booking_log
CREATE TABLE IF NOT EXISTS `booking_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uid` int(10) NOT NULL COMMENT '用户ID',
  `pid` int(10) NOT NULL COMMENT '项目ID',
  `name` varchar(50) NOT NULL COMMENT '姓名',
  `mobile` char(11) NOT NULL COMMENT '手机号',
  `fund` int(10) NOT NULL COMMENT '预约金额',
  `created_at` int(10) NOT NULL COMMENT '创建时间',
  `updated_at` int(10) NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COMMENT='预约记录表';

-- Data exporting was unselected.
-- Dumping structure for table wjf.booking_product
CREATE TABLE IF NOT EXISTS `booking_product` (
  `id` int(10) NOT NULL COMMENT 'ID',
  `name` varchar(128) NOT NULL COMMENT '项目名称',
  `is_disabled` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否禁用',
  `start_time` int(10) NOT NULL COMMENT '开始时间',
  `end_time` int(10) NOT NULL COMMENT '结束时间',
  `min_fund` int(10) NOT NULL DEFAULT '0' COMMENT '起投金额',
  `total_fund` int(10) NOT NULL DEFAULT '0' COMMENT '总额',
  `created_at` int(10) NOT NULL COMMENT '创建时间',
  `updated_at` int(10) NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='预约项目表';

-- Data exporting was unselected.
-- Dumping structure for table wjf.borrower
CREATE TABLE IF NOT EXISTS `borrower` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL DEFAULT '0' COMMENT '融资会员ID',
  `allowDisbursement` tinyint(1) NOT NULL DEFAULT '0' COMMENT '能否作为放款方',
  `type` smallint(6) NOT NULL DEFAULT '1' COMMENT '会员类型 1企业融资方 2个人融资方 3用款方 4代偿方 5担保方',
  `created_at` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=836 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.cache_entry
CREATE TABLE IF NOT EXISTS `cache_entry` (
  `id` char(128) NOT NULL DEFAULT '',
  `expire` int(11) DEFAULT NULL,
  `data` blob,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.callout
CREATE TABLE IF NOT EXISTS `callout` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '召集人ID',
  `endTime` datetime DEFAULT NULL COMMENT '召集截止时间',
  `responderCount` int(11) DEFAULT '0' COMMENT '响应人数',
  `promo_id` int(11) DEFAULT NULL COMMENT '参与活动ID',
  `createTime` datetime DEFAULT NULL COMMENT '创建时间',
  `callerOpenId` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_key` (`promo_id`,`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=727 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.callout_responder
CREATE TABLE IF NOT EXISTS `callout_responder` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `openid` varchar(64) DEFAULT NULL COMMENT '用户开放身份标识',
  `callout_id` int(11) NOT NULL COMMENT '召集ID',
  `ip` varchar(15) DEFAULT NULL COMMENT '响应人IP',
  `createTime` datetime DEFAULT NULL COMMENT '创建时间',
  `promo_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=278 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.cancelorder
CREATE TABLE IF NOT EXISTS `cancelorder` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `orderSn` varchar(30) NOT NULL,
  `txSn` varchar(30) NOT NULL,
  `money` decimal(14,2) NOT NULL COMMENT '返还金额。负数含义',
  `txStatus` tinyint(1) NOT NULL COMMENT '0初始,1处理中,2成功,3失败',
  `created_at` int(10) NOT NULL,
  `updated_at` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='取消订单表';

-- Data exporting was unselected.
-- Dumping structure for table wjf.captcha
CREATE TABLE IF NOT EXISTS `captcha` (
  `id` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `createTime` datetime NOT NULL,
  `expireTime` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.category
CREATE TABLE IF NOT EXISTS `category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL COMMENT '分类名称',
  `key` varchar(20) NOT NULL COMMENT '分类KEY',
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '上级分类id',
  `level` tinyint(1) NOT NULL DEFAULT '1' COMMENT '分类层级 ',
  `description` char(128) NOT NULL DEFAULT '' COMMENT '描述',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '分类序号',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态 0-锁定 1-正常 ',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '分类类型 1-资讯分类 ',
  `updated_at` int(10) NOT NULL COMMENT '更新时间',
  `created_at` int(10) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='公共分类表';

-- Data exporting was unselected.
-- Dumping structure for table wjf.channel
CREATE TABLE IF NOT EXISTS `channel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `thirdPartyUser_id` varchar(255) NOT NULL,
  `createTime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `userId` (`userId`),
  UNIQUE KEY `thirdPartyUser_id` (`thirdPartyUser_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6442 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.check_in
CREATE TABLE IF NOT EXISTS `check_in` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `checkDate` date NOT NULL,
  `lastCheckDate` date DEFAULT NULL,
  `streak` int(11) DEFAULT NULL,
  `createTime` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_date` (`user_id`,`checkDate`)
) ENGINE=InnoDB AUTO_INCREMENT=1546016 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.code
CREATE TABLE IF NOT EXISTS `code` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(16) NOT NULL,
  `user_id` int(10) DEFAULT NULL,
  `isUsed` int(1) NOT NULL,
  `usedAt` datetime DEFAULT NULL,
  `createdAt` datetime NOT NULL,
  `expiresAt` datetime NOT NULL,
  `goodsType_sn` varchar(255) DEFAULT NULL,
  `goodsType` smallint(6) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `goodsType_sn` (`goodsType_sn`)
) ENGINE=InnoDB AUTO_INCREMENT=347610 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.coins_record
CREATE TABLE IF NOT EXISTS `coins_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `incrCoins` int(11) NOT NULL,
  `finalCoins` int(11) NOT NULL,
  `createTime` datetime NOT NULL,
  `isOffline` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=73583 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.contract
CREATE TABLE IF NOT EXISTS `contract` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `type` int(10) NOT NULL DEFAULT '0' COMMENT '类型。0对应channel_order 1对应online_order',
  `order_id` int(4) DEFAULT '0' COMMENT '订单id',
  `contract_name` varchar(50) DEFAULT NULL,
  `contract_number` varchar(30) DEFAULT NULL COMMENT '合同号',
  `contract_template_id` int(4) DEFAULT NULL COMMENT '合同模板id',
  `contract_content` longtext COMMENT '合同内容',
  `path` varchar(100) DEFAULT NULL COMMENT '路径',
  `uid` int(4) DEFAULT NULL COMMENT '金交用户uid',
  `order_sn` varchar(30) DEFAULT NULL COMMENT '金交订单sn',
  `channel_user_sn` varchar(30) DEFAULT '' COMMENT '渠道中的sn',
  `channel_order_sn` varchar(30) DEFAULT '' COMMENT '渠道订单sn',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.contract_template
CREATE TABLE IF NOT EXISTS `contract_template` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `type` int(1) DEFAULT '0' COMMENT '0固定1特殊模板',
  `pid` int(10) DEFAULT NULL COMMENT '产品id',
  `name` char(30) DEFAULT NULL,
  `content` longtext,
  `path` varchar(100) DEFAULT NULL COMMENT '模板路径',
  `status` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`)
) ENGINE=InnoDB AUTO_INCREMENT=40028 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.coupon_type
CREATE TABLE IF NOT EXISTS `coupon_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sn` varchar(20) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `amount` decimal(6,2) DEFAULT '0.00',
  `minInvest` decimal(14,2) NOT NULL,
  `useStartDate` date DEFAULT NULL,
  `useEndDate` date DEFAULT NULL,
  `issueStartDate` date NOT NULL,
  `issueEndDate` date NOT NULL,
  `isDisabled` tinyint(1) NOT NULL,
  `created_at` int(10) DEFAULT NULL,
  `updated_at` int(10) DEFAULT NULL,
  `expiresInDays` int(3) DEFAULT NULL,
  `customerType` int(1) DEFAULT NULL,
  `loanCategories` varchar(30) DEFAULT NULL,
  `allowCollect` tinyint(1) DEFAULT NULL,
  `isAudited` tinyint(1) NOT NULL,
  `isAppOnly` tinyint(1) NOT NULL,
  `loanExpires` int(11) DEFAULT NULL,
  `type` tinyint(1) DEFAULT '0' COMMENT '0:代金券;1:加息券',
  `bonusRate` decimal(6,3) DEFAULT NULL,
  `bonusDays` int(11) DEFAULT '0' COMMENT '加息券的加息天数',
  PRIMARY KEY (`id`),
  UNIQUE KEY `sn` (`sn`),
  UNIQUE KEY `sn_2` (`sn`)
) ENGINE=InnoDB AUTO_INCREMENT=177 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.crm_account
CREATE TABLE IF NOT EXISTS `crm_account` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `creator_id` int(11) DEFAULT NULL,
  `createTime` datetime DEFAULT NULL,
  `updateTime` datetime DEFAULT NULL,
  `primaryContact_id` int(11) DEFAULT NULL,
  `type` varchar(10) DEFAULT NULL,
  `isConverted` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pc_id` (`primaryContact_id`)
) ENGINE=InnoDB AUTO_INCREMENT=84661 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.crm_account_contact
CREATE TABLE IF NOT EXISTS `crm_account_contact` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(11) DEFAULT NULL,
  `contact_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_account_contact` (`account_id`,`contact_id`)
) ENGINE=InnoDB AUTO_INCREMENT=85612 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.crm_activity
CREATE TABLE IF NOT EXISTS `crm_activity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(11) DEFAULT NULL,
  `creator_id` int(11) DEFAULT NULL,
  `createTime` datetime DEFAULT NULL,
  `updateTime` datetime DEFAULT NULL,
  `ref_type` varchar(255) DEFAULT NULL,
  `ref_id` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=93635 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.crm_branch_visit
CREATE TABLE IF NOT EXISTS `crm_branch_visit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(11) DEFAULT NULL,
  `creator_id` int(11) DEFAULT NULL,
  `createTime` datetime DEFAULT NULL,
  `updateTime` datetime DEFAULT NULL,
  `visitDate` date DEFAULT NULL,
  `recp_name` varchar(255) DEFAULT NULL,
  `content` text,
  `comment` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4698 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.crm_contact
CREATE TABLE IF NOT EXISTS `crm_contact` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(11) DEFAULT NULL,
  `creator_id` int(11) DEFAULT NULL,
  `createTime` datetime DEFAULT NULL,
  `updateTime` datetime DEFAULT NULL,
  `type` varchar(10) DEFAULT NULL,
  `obfsNumber` varchar(255) DEFAULT NULL,
  `encryptedNumber` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=79489 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.crm_gift
CREATE TABLE IF NOT EXISTS `crm_gift` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(11) NOT NULL,
  `goodsType_id` int(11) NOT NULL,
  `paymentMethod` varchar(255) NOT NULL,
  `isDelivered` tinyint(1) DEFAULT '0',
  `deliveryTime` datetime DEFAULT NULL,
  `deliveryMethod` varchar(255) DEFAULT NULL,
  `createTime` datetime DEFAULT NULL,
  `updateTime` datetime DEFAULT NULL,
  `creator_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8703 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.crm_identity
CREATE TABLE IF NOT EXISTS `crm_identity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(11) DEFAULT NULL,
  `creator_id` int(11) DEFAULT NULL,
  `createTime` datetime DEFAULT NULL,
  `updateTime` datetime DEFAULT NULL,
  `birthDate` date DEFAULT NULL,
  `birthYear` varchar(4) DEFAULT NULL,
  `gender` varchar(1) DEFAULT NULL,
  `obfsName` varchar(20) DEFAULT NULL,
  `encryptedName` varchar(255) DEFAULT NULL,
  `obfsIdNo` varchar(255) DEFAULT NULL,
  `encryptedIdNo` varchar(255) DEFAULT NULL,
  `tag` varchar(255) DEFAULT NULL COMMENT '标签',
  `inviter` int(11) DEFAULT NULL COMMENT '邀请人',
  `affiliator_id` int(11) DEFAULT NULL COMMENT '分销商',
  `emergencyContact` varchar(255) DEFAULT NULL COMMENT '紧急联系人',
  `address` varchar(255) DEFAULT NULL COMMENT '地址',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=84611 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.crm_note
CREATE TABLE IF NOT EXISTS `crm_note` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(11) DEFAULT NULL,
  `creator_id` int(11) DEFAULT NULL,
  `createTime` datetime DEFAULT NULL,
  `updateTime` datetime DEFAULT NULL,
  `content` text,
  `isSolved` tinyint(1) DEFAULT '0' COMMENT '是否解决',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=187 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.crm_order
CREATE TABLE IF NOT EXISTS `crm_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(11) NOT NULL,
  `mobile` varchar(255) DEFAULT NULL COMMENT '手机号',
  `idCard` varchar(255) NOT NULL COMMENT '身份证号',
  `bankCard` varchar(255) NOT NULL COMMENT '银行卡号',
  `bankName` varchar(255) NOT NULL COMMENT '开户行',
  `orderDate` date NOT NULL COMMENT '认购日期',
  `money` decimal(10,0) NOT NULL COMMENT '金额',
  `apr` float NOT NULL COMMENT '利率',
  `loanId` int(11) NOT NULL COMMENT '产品id',
  `channel` int(11) DEFAULT NULL COMMENT '渠道',
  `voucher` varchar(255) DEFAULT NULL COMMENT '凭证',
  `remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `createdAt` int(11) NOT NULL COMMENT '创建时间',
  `createdUser` int(11) NOT NULL COMMENT '操作人',
  `isAudited` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否审核通过',
  `auditor` int(11) DEFAULT NULL COMMENT '审核人',
  `offline_order_sn` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=911 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.crm_phone_call
CREATE TABLE IF NOT EXISTS `crm_phone_call` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(11) DEFAULT NULL,
  `creator_id` int(11) DEFAULT NULL,
  `createTime` datetime DEFAULT NULL,
  `updateTime` datetime DEFAULT NULL,
  `callTime` datetime DEFAULT NULL COMMENT '通话开始时间',
  `recp_id` int(11) DEFAULT NULL COMMENT '客服ID',
  `contact_id` int(11) DEFAULT NULL,
  `direction` varchar(20) DEFAULT NULL,
  `durationSeconds` int(11) DEFAULT NULL,
  `callerName` varchar(20) DEFAULT NULL COMMENT '客户称呼',
  `content` text,
  `gender` varchar(1) DEFAULT NULL,
  `comment` text,
  `reception` varchar(255) DEFAULT NULL COMMENT '门店接待',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=87816 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.crm_solve_detail
CREATE TABLE IF NOT EXISTS `crm_solve_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL COMMENT '需求类型',
  `ref_id` int(11) NOT NULL COMMENT '需求id',
  `content` varchar(255) NOT NULL COMMENT '内容',
  `isSolved` tinyint(1) NOT NULL DEFAULT '0' COMMENT '需求是否解决',
  `auditor` int(11) NOT NULL COMMENT '操作人',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=95 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.crm_test
CREATE TABLE IF NOT EXISTS `crm_test` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `a` varchar(255) DEFAULT NULL,
  `b` varchar(255) DEFAULT NULL,
  `c` varchar(255) DEFAULT NULL,
  `d` varchar(255) DEFAULT NULL,
  `e` varchar(255) DEFAULT NULL,
  `f` varchar(255) DEFAULT NULL,
  `g` varchar(255) DEFAULT NULL,
  `h` varchar(255) DEFAULT NULL,
  `i` varchar(255) DEFAULT NULL,
  `j` varchar(255) DEFAULT NULL,
  `k` varchar(255) DEFAULT NULL,
  `content` text,
  `summary` text,
  `status` int(11) DEFAULT '0',
  `type` varchar(255) DEFAULT NULL,
  `error` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8094 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.draw_record
CREATE TABLE IF NOT EXISTS `draw_record` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pay_id` int(1) unsigned NOT NULL COMMENT '支付公司id【若线下划款，此字段没有意义】',
  `account_id` int(10) unsigned NOT NULL COMMENT '对应资金账户id',
  `sn` varchar(30) DEFAULT NULL COMMENT '流水号',
  `orderSn` varchar(30) DEFAULT NULL,
  `uid` int(10) unsigned NOT NULL,
  `identification_type` tinyint(1) NOT NULL COMMENT '证件类型0=身份证 1=户口簿 2=护照 3=军官证 4=士兵证 5=港澳居民来往内地通行证 6=台湾同胞来往内地通行证 7=临时身份证 8=外国人居留证 9=警官证',
  `identification_number` varchar(32) NOT NULL COMMENT '证件号',
  `user_bank_id` int(11) NOT NULL COMMENT 'userbank的id',
  `sub_bank_name` varchar(255) DEFAULT NULL,
  `province` varchar(30) DEFAULT NULL,
  `city` varchar(30) DEFAULT NULL,
  `money` decimal(14,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '提现金额',
  `fee` decimal(4,2) NOT NULL COMMENT '提现手续费',
  `pay_bank_id` varchar(30) NOT NULL COMMENT '取现银行代号【不同支付公司银行id可能不等。保存时按照统一的保存bank_id】',
  `bank_id` varchar(30) NOT NULL COMMENT '本平台银行id',
  `bank_name` varchar(30) NOT NULL COMMENT '取现银行账户',
  `bank_account` varchar(30) NOT NULL COMMENT '取现银行账号',
  `status` tinyint(2) unsigned DEFAULT '0' COMMENT '状态 0-未处理 1-已审核 21-提现不成功  2-提现成功 11-提现驳回',
  `created_at` int(10) unsigned DEFAULT NULL,
  `updated_at` int(10) unsigned DEFAULT NULL,
  `lastCronCheckTime` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=118120 DEFAULT CHARSET=utf8 COMMENT='提现记录表';

-- Data exporting was unselected.
-- Dumping structure for table wjf.ebankconfig
CREATE TABLE IF NOT EXISTS `ebankconfig` (
  `bankId` int(4) NOT NULL,
  `typePersonal` tinyint(1) NOT NULL DEFAULT '0' COMMENT '支持个人为1',
  `typeBusiness` tinyint(1) NOT NULL DEFAULT '0' COMMENT '支持企业为1',
  `isDisabled` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否禁用0否',
  `singleLimit` decimal(14,2) NOT NULL COMMENT '单次限额',
  `dailyLimit` decimal(14,2) NOT NULL COMMENT '单日限额',
  PRIMARY KEY (`bankId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.ebao_quan
CREATE TABLE IF NOT EXISTS `ebao_quan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(1) DEFAULT NULL,
  `title` varchar(200) DEFAULT NULL,
  `itemId` int(10) NOT NULL,
  `uid` int(10) NOT NULL,
  `baoId` varchar(32) DEFAULT NULL,
  `docHash` varchar(200) DEFAULT NULL,
  `preservationTime` varchar(13) DEFAULT NULL,
  `success` tinyint(1) DEFAULT NULL,
  `errMessage` varchar(200) DEFAULT NULL,
  `created_at` int(10) DEFAULT NULL,
  `updated_at` int(10) DEFAULT NULL,
  `itemType` varchar(20) DEFAULT 'loan_order',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=259835 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.epayuser
CREATE TABLE IF NOT EXISTS `epayuser` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `appUserId` varchar(60) NOT NULL COMMENT '应用方用户ID（兼容非数字的用户标识）',
  `epayId` smallint(5) unsigned NOT NULL COMMENT '托管方ID',
  `epayUserId` varchar(60) NOT NULL COMMENT '托管用户ID',
  `accountNo` varchar(60) DEFAULT NULL COMMENT '托管账户号',
  `regDate` date NOT NULL COMMENT '开户日期',
  `clientIp` int(10) unsigned NOT NULL COMMENT 'IP',
  `createTime` datetime NOT NULL COMMENT '记录时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `epayId_2` (`epayId`,`epayUserId`),
  KEY `appUserId` (`appUserId`),
  KEY `epayId` (`epayId`)
) ENGINE=InnoDB AUTO_INCREMENT=59916 DEFAULT CHARSET=utf8 COMMENT='托管方用户表';

-- Data exporting was unselected.
-- Dumping structure for table wjf.goods_type
CREATE TABLE IF NOT EXISTS `goods_type` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sn` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `type` smallint(6) DEFAULT NULL,
  `createdAt` datetime DEFAULT NULL,
  `effectDays` smallint(6) DEFAULT NULL,
  `affiliator_id` int(11) DEFAULT NULL,
  `isSkuEnabled` tinyint(1) DEFAULT '0',
  `stock` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sn` (`sn`)
) ENGINE=InnoDB AUTO_INCREMENT=270 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.inviterrelation
CREATE TABLE IF NOT EXISTS `inviterrelation` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `inviterUid` int(10) NOT NULL COMMENT '邀请人uid',
  `inviteeUid` int(10) NOT NULL COMMENT '被邀请人uid',
  `code` varchar(50) NOT NULL COMMENT '邀请码',
  `created_at` int(10) NOT NULL,
  `updated_at` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `inviterUid` (`inviterUid`,`inviteeUid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='邀请关系表';

-- Data exporting was unselected.
-- Dumping structure for table wjf.invite_record
CREATE TABLE IF NOT EXISTS `invite_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `invitee_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6076 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.issuer
CREATE TABLE IF NOT EXISTS `issuer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `mediaTitle` varchar(255) DEFAULT NULL,
  `video_id` int(11) DEFAULT NULL,
  `videoCover_id` int(11) DEFAULT NULL,
  `big_pic` varchar(255) DEFAULT NULL,
  `mid_pic` varchar(255) DEFAULT NULL,
  `small_pic` varchar(255) DEFAULT NULL,
  `isShow` tinyint(1) DEFAULT '0',
  `sort` int(11) DEFAULT NULL,
  `path` varchar(255) DEFAULT NULL,
  `allowShowOnPc` tinyint(1) NOT NULL DEFAULT '0',
  `pcTitle` varchar(255) DEFAULT NULL,
  `pcDescription` varchar(255) DEFAULT NULL,
  `pcLink` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.item_category
CREATE TABLE IF NOT EXISTS `item_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(10) unsigned NOT NULL COMMENT '项目ID',
  `category_id` int(10) unsigned NOT NULL COMMENT '分类ID',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '分类类型 1-资讯分类 ',
  `updated_at` int(10) NOT NULL COMMENT '更新时间',
  `created_at` int(10) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2862 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='项目、分类对照表';

-- Data exporting was unselected.
-- Dumping structure for table wjf.item_message
CREATE TABLE IF NOT EXISTS `item_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticketId` int(11) DEFAULT NULL COMMENT '抽奖机会id',
  `content` varchar(255) DEFAULT NULL COMMENT '描述内容',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=179 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.jx_page
CREATE TABLE IF NOT EXISTS `jx_page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `issuerId` int(11) unsigned NOT NULL,
  `title` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `createTime` datetime DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `issuerId` (`issuerId`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.lenderstats
CREATE TABLE IF NOT EXISTS `lenderstats` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL,
  `userRegTime` int(10) NOT NULL,
  `created_at` int(10) NOT NULL,
  `updated_at` int(10) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `mobile` char(11) DEFAULT NULL,
  `idcard` varchar(50) DEFAULT NULL,
  `idcardStatus` tinyint(1) DEFAULT NULL,
  `mianmiStatus` tinyint(1) DEFAULT NULL,
  `bid` tinyint(1) DEFAULT NULL,
  `accountBalance` decimal(14,2) DEFAULT NULL,
  `rtotalFund` decimal(14,2) DEFAULT NULL,
  `rtotalNum` int(10) DEFAULT NULL,
  `dtotalFund` decimal(14,2) DEFAULT NULL,
  `dtotalNum` int(10) DEFAULT NULL,
  `ototalFund` decimal(14,2) DEFAULT NULL,
  `ototalNum` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=111420 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.login_log
CREATE TABLE IF NOT EXISTS `login_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `ip` varchar(30) NOT NULL COMMENT 'IP地址',
  `type` tinyint(1) NOT NULL COMMENT '渠道类型：1代表前台wap;2代表前台pc端;3代表后端控制台',
  `user_name` varchar(30) NOT NULL COMMENT '用户登陆名',
  `updated_at` int(11) DEFAULT NULL COMMENT '记录更新时间',
  `created_at` int(11) NOT NULL COMMENT '记录创建时间',
  `status` tinyint(1) DEFAULT '0' COMMENT '状态：0-失败，1-成功',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=329025 DEFAULT CHARSET=utf8 COMMENT='登陆错误日志表';

-- Data exporting was unselected.
-- Dumping structure for table wjf.media
CREATE TABLE IF NOT EXISTS `media` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL,
  `uri` varchar(255) NOT NULL,
  `createTime` datetime NOT NULL,
  `updateTime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=701 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.migration
CREATE TABLE IF NOT EXISTS `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.money_record
CREATE TABLE IF NOT EXISTS `money_record` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sn` varchar(30) DEFAULT NULL COMMENT '流水号',
  `type` int(1) NOT NULL DEFAULT '0' COMMENT '类型。',
  `osn` varchar(30) DEFAULT NULL COMMENT '对应的流水号',
  `account_id` int(10) unsigned NOT NULL COMMENT '对应资金账户id',
  `uid` int(10) unsigned DEFAULT NULL,
  `balance` decimal(14,2) DEFAULT '0.00' COMMENT '每次流水记录当时余额',
  `in_money` decimal(14,2) unsigned DEFAULT '0.00' COMMENT '入账金额',
  `out_money` decimal(14,2) unsigned DEFAULT '0.00' COMMENT '出账金额',
  `remark` varchar(500) DEFAULT NULL COMMENT '备注',
  `created_at` int(10) unsigned DEFAULT NULL,
  `updated_at` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=12147003 DEFAULT CHARSET=utf8 COMMENT='用户资金记录表';

-- Data exporting was unselected.
-- Dumping structure for table wjf.news
CREATE TABLE IF NOT EXISTS `news` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL COMMENT '新闻标题',
  `summary` varchar(200) DEFAULT NULL COMMENT '新闻概括',
  `child_title` varchar(100) DEFAULT NULL COMMENT '新闻副标题',
  `image` varchar(250) DEFAULT NULL COMMENT '内容图片',
  `source` varchar(100) NOT NULL COMMENT '内容来源',
  `creator_id` int(10) unsigned NOT NULL COMMENT '创建者管理员id',
  `status` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '状态 0-草稿 1-正常 3-删除',
  `home_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否在首页显示 0-不显示 1-显示',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '序号',
  `body` text NOT NULL COMMENT '新闻内容',
  `news_time` int(10) NOT NULL COMMENT '新闻发布时间',
  `updated_at` int(10) NOT NULL,
  `created_at` int(10) NOT NULL,
  `pc_thumb` varchar(255) NOT NULL,
  `allowShowInList` tinyint(1) NOT NULL DEFAULT '1',
  `investLeast` decimal(14,2) NOT NULL DEFAULT '0.00' COMMENT '最低投资可见',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=884 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='新闻表';

-- Data exporting was unselected.
-- Dumping structure for table wjf.notifylog
CREATE TABLE IF NOT EXISTS `notifylog` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `query` text NOT NULL,
  `created_at` int(10) NOT NULL,
  `updated_at` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.offline_loan
CREATE TABLE IF NOT EXISTS `offline_loan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `expires` smallint(6) NOT NULL,
  `unit` varchar(20) DEFAULT NULL,
  `sn` char(32) DEFAULT NULL,
  `yield_rate` varchar(255) DEFAULT NULL,
  `jixi_time` datetime DEFAULT NULL,
  `finish_date` datetime DEFAULT NULL,
  `repaymentMethod` smallint(6) DEFAULT NULL,
  `is_jixi` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否计息',
  `paymentDay` int(11) DEFAULT NULL COMMENT '固定还款日',
  `isCustomRepayment` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否自定义还款',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=286 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.offline_order
CREATE TABLE IF NOT EXISTS `offline_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sn` varchar(255) DEFAULT NULL,
  `affiliator_id` int(10) NOT NULL,
  `loan_id` int(10) NOT NULL,
  `mobile` varchar(255) DEFAULT NULL COMMENT '手机号',
  `money` decimal(14,2) NOT NULL,
  `orderDate` date NOT NULL,
  `created_at` int(10) NOT NULL,
  `isDeleted` tinyint(1) NOT NULL,
  `user_id` int(11) NOT NULL,
  `idCard` varchar(30) NOT NULL,
  `accBankName` varchar(255) NOT NULL,
  `bankCardNo` varchar(30) NOT NULL,
  `valueDate` date DEFAULT NULL,
  `apr` decimal(14,6) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7266 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.offline_repayment
CREATE TABLE IF NOT EXISTS `offline_repayment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `loan_id` int(11) DEFAULT NULL,
  `term` int(2) DEFAULT NULL,
  `dueDate` date DEFAULT NULL,
  `amount` decimal(14,2) DEFAULT NULL,
  `principal` decimal(14,2) DEFAULT NULL,
  `interest` decimal(14,2) DEFAULT NULL,
  `isRepaid` int(1) DEFAULT '0',
  `repaidAt` datetime DEFAULT NULL,
  `isRefunded` int(1) DEFAULT '0',
  `refundedAt` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1904 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.offline_repayment_plan
CREATE TABLE IF NOT EXISTS `offline_repayment_plan` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `loan_id` int(11) NOT NULL COMMENT '线下标的id',
  `sn` varchar(255) NOT NULL COMMENT '计划编号',
  `order_id` int(11) NOT NULL COMMENT '线下订单id',
  `qishu` int(11) NOT NULL COMMENT '还款期数',
  `uid` int(11) NOT NULL COMMENT '用户id',
  `benxi` decimal(14,2) NOT NULL COMMENT '本息',
  `benjin` decimal(14,2) NOT NULL DEFAULT '0.00' COMMENT '应还本金',
  `lixi` decimal(14,2) NOT NULL COMMENT '应还利息',
  `refund_time` date NOT NULL COMMENT '计划还款时间',
  `actualRefundTime` date DEFAULT NULL COMMENT '实际还款时间',
  `status` int(11) NOT NULL DEFAULT '0' COMMENT '0、未还 1、已还 2、提前还款 3，无效',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `operator` int(11) NOT NULL COMMENT '操作人',
  `yuqi_day` int(11) NOT NULL DEFAULT '0' COMMENT '逾期天数',
  `tiexi` decimal(14,2) NOT NULL DEFAULT '0.00' COMMENT '贴息(逾期费用)',
  `isSendSms` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否发送短信',
  PRIMARY KEY (`id`),
  UNIQUE KEY `sn` (`sn`)
) ENGINE=InnoDB AUTO_INCREMENT=41765 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.offline_stats
CREATE TABLE IF NOT EXISTS `offline_stats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tradedAmount` decimal(14,2) NOT NULL,
  `refundedPrincipal` decimal(14,2) NOT NULL,
  `refundedInterest` decimal(14,2) NOT NULL,
  `createTime` datetime NOT NULL,
  `updateTime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.offline_user
CREATE TABLE IF NOT EXISTS `offline_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `realName` varchar(50) NOT NULL,
  `mobile` varchar(20) NOT NULL,
  `idCard` varchar(30) NOT NULL,
  `points` int(11) DEFAULT '0',
  `annualInvestment` decimal(14,2) NOT NULL DEFAULT '0.00',
  `created_at` int(10) DEFAULT NULL,
  `crmAccount_id` int(11) DEFAULT NULL,
  `isReg` tinyint(1) DEFAULT '0',
  `onlineUserId` int(11) DEFAULT NULL COMMENT '线上用户id',
  PRIMARY KEY (`id`),
  KEY `crmAccount_id` (`crmAccount_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2964 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.online_fangkuan
CREATE TABLE IF NOT EXISTS `online_fangkuan` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '放款ID',
  `sn` varchar(30) NOT NULL COMMENT '批次序号',
  `order_money` decimal(14,2) DEFAULT NULL COMMENT '放款金额',
  `fee` decimal(14,2) DEFAULT NULL COMMENT '手续费金额',
  `uid` int(11) unsigned DEFAULT NULL COMMENT '借款人ID',
  `online_product_id` int(11) unsigned DEFAULT NULL COMMENT '线上产品id',
  `status` tinyint(2) unsigned NOT NULL DEFAULT '1' COMMENT '0---1-审核通过 2审核拒绝',
  `remark` varchar(10) NOT NULL COMMENT '备注',
  `admin_id` int(10) unsigned NOT NULL COMMENT '放款操作人id',
  `created_at` int(10) unsigned DEFAULT NULL COMMENT '创建时间',
  `updated_at` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9750 DEFAULT CHARSET=utf8 COMMENT='在线标的放款订单';

-- Data exporting was unselected.
-- Dumping structure for table wjf.online_fangkuan_detail
CREATE TABLE IF NOT EXISTS `online_fangkuan_detail` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '放款ID',
  `fangkuan_order_id` int(10) NOT NULL COMMENT '放款订单序号',
  `product_order_id` int(10) NOT NULL COMMENT '投标订单序号',
  `order_money` decimal(14,2) DEFAULT NULL COMMENT '放款金额',
  `online_product_id` int(11) NOT NULL COMMENT '标的ID',
  `order_time` varchar(20) NOT NULL COMMENT '投标的时间',
  `status` tinyint(2) unsigned NOT NULL DEFAULT '1' COMMENT '0---1-审核通过 2审核拒绝',
  `admin_id` int(10) unsigned NOT NULL COMMENT '放款操作人id',
  `created_at` int(10) unsigned DEFAULT NULL COMMENT '创建时间',
  `updated_at` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=172078 DEFAULT CHARSET=utf8 COMMENT='在线标的放款订单明细';

-- Data exporting was unselected.
-- Dumping structure for table wjf.online_order
CREATE TABLE IF NOT EXISTS `online_order` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '投标ID',
  `sn` varchar(30) NOT NULL COMMENT '订单序号',
  `online_pid` int(10) unsigned DEFAULT NULL COMMENT '项目标的ID',
  `refund_method` tinyint(1) DEFAULT '1' COMMENT '还款方式：1.按天到期本息 2.按月付息还本',
  `yield_rate` decimal(14,6) NOT NULL COMMENT '年利率',
  `expires` smallint(6) unsigned DEFAULT NULL COMMENT '借款期限 (以天为单位) 如 15  表示15天',
  `order_money` decimal(14,2) DEFAULT NULL COMMENT '投标金额',
  `order_time` int(10) NOT NULL COMMENT '成功时间(支付之后)',
  `uid` int(10) DEFAULT NULL COMMENT '投资者uid',
  `username` varchar(50) DEFAULT NULL COMMENT '投资者用户名',
  `status` tinyint(2) NOT NULL COMMENT '0--投标失败---1-投标成功 2.撤标 3，无效',
  `created_at` int(10) unsigned DEFAULT NULL,
  `updated_at` int(10) unsigned DEFAULT NULL,
  `campaign_source` varchar(50) DEFAULT NULL COMMENT '百度统计来源标志',
  `couponAmount` decimal(6,2) NOT NULL,
  `paymentAmount` decimal(14,2) NOT NULL,
  `investFrom` int(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `sn` (`sn`),
  KEY `online_pid` (`online_pid`),
  KEY `uid` (`uid`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=173767 DEFAULT CHARSET=utf8 COMMENT='标的订单';

-- Data exporting was unselected.
-- Dumping structure for table wjf.online_product
CREATE TABLE IF NOT EXISTS `online_product` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `epayLoanAccountId` varchar(15) NOT NULL COMMENT '标的在托管平台的账户号',
  `title` varchar(128) NOT NULL COMMENT '标的项目名称',
  `sn` char(32) NOT NULL COMMENT '标的项目编号',
  `cid` int(10) unsigned NOT NULL COMMENT '分类id',
  `is_xs` tinyint(1) NOT NULL COMMENT '是否新手标1是',
  `recommendTime` int(10) NOT NULL COMMENT '推荐时间',
  `borrow_uid` int(10) NOT NULL COMMENT '融资用户ID',
  `yield_rate` decimal(6,4) NOT NULL COMMENT '年利率',
  `jiaxi` decimal(3,1) DEFAULT NULL COMMENT '加息利率（%）',
  `fee` decimal(14,6) NOT NULL COMMENT '平台手续费（放款时候收取，最小精度百万分之1）',
  `expires_show` varchar(50) NOT NULL DEFAULT '' COMMENT '还款期限文字显示',
  `refund_method` int(1) unsigned DEFAULT '1' COMMENT '还款方式：1.按天到期本息 2.按月付息还本',
  `expires` smallint(6) unsigned DEFAULT NULL COMMENT '借款期限 (以天为单位) 如 15  表示15天',
  `kuanxianqi` smallint(4) NOT NULL COMMENT '宽限期',
  `money` decimal(14,2) NOT NULL DEFAULT '0.00' COMMENT '项目融资总额',
  `funded_money` decimal(14,2) NOT NULL COMMENT '实际募集金额',
  `start_money` decimal(14,2) NOT NULL COMMENT '起投金额',
  `dizeng_money` decimal(14,2) NOT NULL COMMENT '递增金额',
  `finish_date` int(10) DEFAULT NULL COMMENT '项目截止日',
  `start_date` int(10) NOT NULL COMMENT '融资开始日期',
  `end_date` int(10) NOT NULL COMMENT '融资结束日期',
  `channel` tinyint(1) DEFAULT '0' COMMENT '分销渠道',
  `description` text NOT NULL COMMENT '项目介绍',
  `full_time` int(10) NOT NULL COMMENT '满标时间',
  `jixi_time` int(10) DEFAULT NULL COMMENT '计息开始时间',
  `fk_examin_time` int(10) DEFAULT '0' COMMENT '放款审核时间',
  `account_name` varchar(50) DEFAULT '' COMMENT '账户名称',
  `account` varchar(50) DEFAULT '' COMMENT '账户',
  `bank` varchar(100) DEFAULT '' COMMENT '开户行',
  `del_status` int(10) DEFAULT '0' COMMENT '状态1-无效 0-有效',
  `online_status` tinyint(1) DEFAULT '0' COMMENT '上线状态：1上线0未上线',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '标的进展： 1预告期、 2进行中,3满标,4流标,5还款中,6已还清 ',
  `yuqi_faxi` decimal(14,6) NOT NULL COMMENT '逾期罚息',
  `order_limit` int(4) DEFAULT '200' COMMENT '限制投标uid，默认200次',
  `isPrivate` tinyint(1) DEFAULT '0' COMMENT '是否是定向标，0否1是',
  `allowedUids` varchar(200) DEFAULT NULL COMMENT '定向标用户id。以,分隔',
  `finish_rate` decimal(6,4) DEFAULT '0.0000' COMMENT '募集完成比例',
  `is_jixi` tinyint(1) DEFAULT '0' COMMENT '是否已经计息0否1是',
  `sort` tinyint(1) DEFAULT '0' COMMENT '排序',
  `contract_type` tinyint(1) DEFAULT '0' COMMENT '0固定1特殊模板',
  `creator_id` int(10) unsigned NOT NULL COMMENT '创建者管理员id',
  `created_at` int(10) unsigned DEFAULT NULL,
  `updated_at` int(10) unsigned DEFAULT NULL,
  `isFlexRate` tinyint(1) DEFAULT '0' COMMENT '是否启用浮动利率',
  `rateSteps` varchar(500) DEFAULT NULL COMMENT '浮动利率',
  `issuer` int(11) DEFAULT NULL,
  `issuerSn` varchar(30) DEFAULT NULL,
  `paymentDay` int(5) DEFAULT NULL,
  `isTest` tinyint(1) DEFAULT '0',
  `filingAmount` decimal(14,2) DEFAULT NULL,
  `allowUseCoupon` tinyint(1) NOT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `isLicai` tinyint(1) DEFAULT NULL,
  `pointsMultiple` smallint(6) DEFAULT '1',
  `allowTransfer` tinyint(1) DEFAULT '1',
  `isCustomRepayment` tinyint(1) DEFAULT NULL COMMENT '是否自定义还款',
  `isJixiExamined` tinyint(1) DEFAULT '1' COMMENT '计息审核',
  `internalTitle` varchar(255) DEFAULT NULL,
  `publishTime` datetime DEFAULT NULL,
  `balance_limit` decimal(14,2) DEFAULT '0.00',
  `allowRateCoupon` tinyint(1) DEFAULT '0' COMMENT '加息券使用0:禁止;1:允许',
  `originalBorrower` varchar(20) DEFAULT NULL COMMENT '底层融资方',
  `pkg_sn` varchar(30) DEFAULT NULL COMMENT '资产包sn',
  `isRedeemable` tinyint(1) DEFAULT '0',
  `redemptionPeriods` varchar(255) DEFAULT NULL,
  `redemptionPaymentDates` varchar(255) DEFAULT NULL,
  `isDailyAccrual` tinyint(1) DEFAULT '0',
  `flexRepay` tinyint(1) DEFAULT '0' COMMENT '灵活还款1是0否',
  `alternativeRepayer` int(11) DEFAULT NULL COMMENT '代偿方',
  `borrowerRate` decimal(6,4) DEFAULT NULL COMMENT '融资方利率',
  `fundReceiver` int(11) DEFAULT NULL COMMENT '用款方',
  `guarantee` int(11) DEFAULT NULL COMMENT '担保方',
  `asset_id` int(11) DEFAULT NULL COMMENT '资产包id',
  PRIMARY KEY (`id`),
  UNIQUE KEY `sn` (`sn`),
  UNIQUE KEY `asset_id` (`asset_id`)
) ENGINE=InnoDB AUTO_INCREMENT=10134 DEFAULT CHARSET=utf8 COMMENT='线上标的产品表';

-- Data exporting was unselected.
-- Dumping structure for view wjf.online_product_v1
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `online_product_v1` (
	`id` INT(10) UNSIGNED NOT NULL,
	`epayLoanAccountId` VARCHAR(15) NOT NULL COMMENT '标的在托管平台的账户号' COLLATE 'utf8_general_ci',
	`title` VARCHAR(128) NOT NULL COMMENT '标的项目名称' COLLATE 'utf8_general_ci',
	`sn` CHAR(32) NOT NULL COMMENT '标的项目编号' COLLATE 'utf8_general_ci',
	`cid` INT(10) UNSIGNED NOT NULL COMMENT '分类id',
	`is_xs` TINYINT(1) NOT NULL COMMENT '是否新手标1是',
	`recommendTime` INT(10) NOT NULL COMMENT '推荐时间',
	`borrow_uid` INT(10) NOT NULL COMMENT '融资用户ID',
	`yield_rate` DECIMAL(6,4) NOT NULL COMMENT '年利率',
	`jiaxi` DECIMAL(3,1) NULL COMMENT '加息利率（%）',
	`fee` DECIMAL(14,6) NOT NULL COMMENT '平台手续费（放款时候收取，最小精度百万分之1）',
	`expires_show` VARCHAR(50) NOT NULL COMMENT '还款期限文字显示' COLLATE 'utf8_general_ci',
	`refund_method` INT(1) UNSIGNED NULL COMMENT '还款方式：1.按天到期本息 2.按月付息还本',
	`expires` SMALLINT(6) UNSIGNED NULL COMMENT '借款期限 (以天为单位) 如 15  表示15天',
	`kuanxianqi` SMALLINT(4) NOT NULL COMMENT '宽限期',
	`money` DECIMAL(14,2) NOT NULL COMMENT '项目融资总额',
	`funded_money` DECIMAL(14,2) NOT NULL COMMENT '实际募集金额',
	`start_money` DECIMAL(14,2) NOT NULL COMMENT '起投金额',
	`dizeng_money` DECIMAL(14,2) NOT NULL COMMENT '递增金额',
	`finish_date` INT(10) NULL COMMENT '项目截止日',
	`start_date` INT(10) NOT NULL COMMENT '融资开始日期',
	`end_date` INT(10) NOT NULL COMMENT '融资结束日期',
	`channel` TINYINT(1) NULL COMMENT '分销渠道',
	`description` TEXT NOT NULL COMMENT '项目介绍' COLLATE 'utf8_general_ci',
	`full_time` INT(10) NOT NULL COMMENT '满标时间',
	`jixi_time` INT(10) NULL COMMENT '计息开始时间',
	`fk_examin_time` INT(10) NULL COMMENT '放款审核时间',
	`account_name` VARCHAR(50) NULL COMMENT '账户名称' COLLATE 'utf8_general_ci',
	`account` VARCHAR(50) NULL COMMENT '账户' COLLATE 'utf8_general_ci',
	`bank` VARCHAR(100) NULL COMMENT '开户行' COLLATE 'utf8_general_ci',
	`del_status` INT(10) NULL COMMENT '状态1-无效 0-有效',
	`online_status` TINYINT(1) NULL COMMENT '上线状态：1上线0未上线',
	`status` INT(4) NOT NULL,
	`yuqi_faxi` DECIMAL(14,6) NOT NULL COMMENT '逾期罚息',
	`order_limit` INT(4) NULL COMMENT '限制投标uid，默认200次',
	`isPrivate` TINYINT(1) NULL COMMENT '是否是定向标，0否1是',
	`allowedUids` VARCHAR(200) NULL COMMENT '定向标用户id。以,分隔' COLLATE 'utf8_general_ci',
	`finish_rate` DECIMAL(6,4) NULL COMMENT '募集完成比例',
	`is_jixi` TINYINT(1) NULL COMMENT '是否已经计息0否1是',
	`sort` TINYINT(1) NULL COMMENT '排序',
	`contract_type` TINYINT(1) NULL COMMENT '0固定1特殊模板',
	`creator_id` INT(10) UNSIGNED NOT NULL COMMENT '创建者管理员id',
	`created_at` INT(10) UNSIGNED NULL,
	`updated_at` INT(10) UNSIGNED NULL,
	`isFlexRate` TINYINT(1) NULL COMMENT '是否启用浮动利率',
	`rateSteps` VARCHAR(500) NULL COMMENT '浮动利率' COLLATE 'utf8_general_ci',
	`issuer` INT(11) NULL,
	`issuerSn` VARCHAR(30) NULL COLLATE 'utf8_general_ci',
	`paymentDay` INT(5) NULL,
	`isTest` TINYINT(1) NULL,
	`filingAmount` DECIMAL(14,2) NULL,
	`allowUseCoupon` TINYINT(1) NOT NULL,
	`tags` VARCHAR(255) NULL COLLATE 'utf8_general_ci',
	`isLicai` TINYINT(1) NULL,
	`pointsMultiple` SMALLINT(6) NULL,
	`allowTransfer` TINYINT(1) NULL,
	`isCustomRepayment` TINYINT(1) NULL COMMENT '是否自定义还款',
	`isJixiExamined` TINYINT(1) NULL COMMENT '计息审核',
	`internalTitle` VARCHAR(255) NULL COLLATE 'utf8_general_ci',
	`publishTime` DATETIME NULL,
	`balance_limit` DECIMAL(14,2) NULL,
	`allowRateCoupon` TINYINT(1) NULL COMMENT '加息券使用0:禁止;1:允许',
	`originalBorrower` VARCHAR(20) NULL COMMENT '底层融资方' COLLATE 'utf8_general_ci',
	`pkg_sn` VARCHAR(30) NULL COMMENT '资产包sn' COLLATE 'utf8_general_ci',
	`isRedeemable` TINYINT(1) NULL,
	`redemptionPeriods` VARCHAR(255) NULL COLLATE 'utf8_general_ci',
	`redemptionPaymentDates` VARCHAR(255) NULL COLLATE 'utf8_general_ci',
	`isDailyAccrual` TINYINT(1) NULL,
	`flexRepay` TINYINT(1) NULL COMMENT '灵活还款1是0否',
	`alternativeRepayer` INT(11) NULL COMMENT '代偿方',
	`borrowerRate` DECIMAL(6,4) NULL COMMENT '融资方利率',
	`fundReceiver` INT(11) NULL COMMENT '用款方',
	`guarantee` INT(11) NULL COMMENT '担保方'
) ENGINE=MyISAM;

-- Dumping structure for view wjf.online_product_v2
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `online_product_v2` (
	`id` INT(10) UNSIGNED NOT NULL,
	`epayLoanAccountId` VARCHAR(15) NOT NULL COMMENT '标的在托管平台的账户号' COLLATE 'utf8_general_ci',
	`title` VARCHAR(128) NOT NULL COMMENT '标的项目名称' COLLATE 'utf8_general_ci',
	`sn` CHAR(32) NOT NULL COMMENT '标的项目编号' COLLATE 'utf8_general_ci',
	`cid` INT(10) UNSIGNED NOT NULL COMMENT '分类id',
	`is_xs` TINYINT(1) NOT NULL COMMENT '是否新手标1是',
	`recommendTime` INT(10) NOT NULL COMMENT '推荐时间',
	`borrow_uid` INT(10) NOT NULL COMMENT '融资用户ID',
	`yield_rate` DECIMAL(6,4) NOT NULL COMMENT '年利率',
	`jiaxi` DECIMAL(3,1) NULL COMMENT '加息利率（%）',
	`fee` DECIMAL(14,6) NOT NULL COMMENT '平台手续费（放款时候收取，最小精度百万分之1）',
	`expires_show` VARCHAR(50) NOT NULL COMMENT '还款期限文字显示' COLLATE 'utf8_general_ci',
	`refund_method` INT(1) UNSIGNED NULL COMMENT '还款方式：1.按天到期本息 2.按月付息还本',
	`expires` SMALLINT(6) UNSIGNED NULL COMMENT '借款期限 (以天为单位) 如 15  表示15天',
	`kuanxianqi` SMALLINT(4) NOT NULL COMMENT '宽限期',
	`money` DECIMAL(14,2) NOT NULL COMMENT '项目融资总额',
	`funded_money` DECIMAL(14,2) NOT NULL COMMENT '实际募集金额',
	`start_money` DECIMAL(14,2) NOT NULL COMMENT '起投金额',
	`dizeng_money` DECIMAL(14,2) NOT NULL COMMENT '递增金额',
	`finish_date` INT(10) NULL COMMENT '项目截止日',
	`start_date` INT(10) NOT NULL COMMENT '融资开始日期',
	`end_date` INT(10) NOT NULL COMMENT '融资结束日期',
	`channel` TINYINT(1) NULL COMMENT '分销渠道',
	`description` TEXT NOT NULL COMMENT '项目介绍' COLLATE 'utf8_general_ci',
	`full_time` INT(10) NOT NULL COMMENT '满标时间',
	`jixi_time` INT(10) NULL COMMENT '计息开始时间',
	`fk_examin_time` INT(10) NULL COMMENT '放款审核时间',
	`account_name` VARCHAR(50) NULL COMMENT '账户名称' COLLATE 'utf8_general_ci',
	`account` VARCHAR(50) NULL COMMENT '账户' COLLATE 'utf8_general_ci',
	`bank` VARCHAR(100) NULL COMMENT '开户行' COLLATE 'utf8_general_ci',
	`del_status` BIGINT(11) NULL,
	`online_status` TINYINT(1) NULL COMMENT '上线状态：1上线0未上线',
	`status` INT(4) NULL,
	`yuqi_faxi` DECIMAL(14,6) NOT NULL COMMENT '逾期罚息',
	`order_limit` INT(4) NULL COMMENT '限制投标uid，默认200次',
	`isPrivate` TINYINT(1) NULL COMMENT '是否是定向标，0否1是',
	`allowedUids` VARCHAR(200) NULL COMMENT '定向标用户id。以,分隔' COLLATE 'utf8_general_ci',
	`finish_rate` DECIMAL(6,4) NULL COMMENT '募集完成比例',
	`is_jixi` TINYINT(1) NULL COMMENT '是否已经计息0否1是',
	`sort` TINYINT(1) NULL COMMENT '排序',
	`contract_type` TINYINT(1) NULL COMMENT '0固定1特殊模板',
	`creator_id` INT(10) UNSIGNED NOT NULL COMMENT '创建者管理员id',
	`created_at` INT(10) UNSIGNED NULL,
	`updated_at` INT(10) UNSIGNED NULL,
	`isFlexRate` TINYINT(1) NULL COMMENT '是否启用浮动利率',
	`rateSteps` VARCHAR(500) NULL COMMENT '浮动利率' COLLATE 'utf8_general_ci',
	`issuer` INT(11) NULL,
	`issuerSn` VARCHAR(30) NULL COLLATE 'utf8_general_ci',
	`paymentDay` INT(5) NULL,
	`isTest` TINYINT(1) NULL,
	`filingAmount` DECIMAL(14,2) NULL,
	`allowUseCoupon` TINYINT(1) NOT NULL,
	`tags` VARCHAR(255) NULL COLLATE 'utf8_general_ci',
	`isLicai` TINYINT(1) NULL,
	`pointsMultiple` SMALLINT(6) NULL,
	`allowTransfer` TINYINT(1) NULL,
	`isCustomRepayment` TINYINT(1) NULL COMMENT '是否自定义还款',
	`isJixiExamined` TINYINT(1) NULL COMMENT '计息审核',
	`internalTitle` VARCHAR(255) NULL COLLATE 'utf8_general_ci',
	`publishTime` DATETIME NULL,
	`balance_limit` DECIMAL(14,2) NULL,
	`allowRateCoupon` TINYINT(1) NULL COMMENT '加息券使用0:禁止;1:允许',
	`originalBorrower` VARCHAR(20) NULL COMMENT '底层融资方' COLLATE 'utf8_general_ci',
	`pkg_sn` VARCHAR(30) NULL COMMENT '资产包sn' COLLATE 'utf8_general_ci',
	`isRedeemable` TINYINT(1) NULL,
	`redemptionPeriods` VARCHAR(255) NULL COLLATE 'utf8_general_ci',
	`redemptionPaymentDates` VARCHAR(255) NULL COLLATE 'utf8_general_ci',
	`isDailyAccrual` TINYINT(1) NULL,
	`flexRepay` TINYINT(1) NULL COMMENT '灵活还款1是0否',
	`alternativeRepayer` INT(11) NULL COMMENT '代偿方',
	`borrowerRate` DECIMAL(6,4) NULL COMMENT '融资方利率',
	`fundReceiver` INT(11) NULL COMMENT '用款方',
	`guarantee` INT(11) NULL COMMENT '担保方'
) ENGINE=MyISAM;

-- Dumping structure for table wjf.online_product_vv
CREATE TABLE IF NOT EXISTS `online_product_vv` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `epayLoanAccountId` varchar(15) NOT NULL COMMENT '标的在托管平台的账户号',
  `title` varchar(128) NOT NULL COMMENT '标的项目名称',
  `sn` char(32) NOT NULL COMMENT '标的项目编号',
  `cid` int(10) unsigned NOT NULL COMMENT '分类id',
  `is_xs` tinyint(1) NOT NULL COMMENT '是否新手标1是',
  `recommendTime` int(10) NOT NULL COMMENT '推荐时间',
  `borrow_uid` int(10) NOT NULL COMMENT '融资用户ID',
  `yield_rate` decimal(6,4) NOT NULL COMMENT '年利率',
  `jiaxi` decimal(3,1) DEFAULT NULL COMMENT '加息利率（%）',
  `fee` decimal(14,6) NOT NULL COMMENT '平台手续费（放款时候收取，最小精度百万分之1）',
  `expires_show` varchar(50) NOT NULL DEFAULT '' COMMENT '还款期限文字显示',
  `refund_method` int(1) unsigned DEFAULT '1' COMMENT '还款方式：1.按天到期本息 2.按月付息还本',
  `expires` smallint(6) unsigned DEFAULT NULL COMMENT '借款期限 (以天为单位) 如 15  表示15天',
  `kuanxianqi` smallint(4) NOT NULL COMMENT '宽限期',
  `money` decimal(14,2) NOT NULL DEFAULT '0.00' COMMENT '项目融资总额',
  `funded_money` decimal(14,2) NOT NULL COMMENT '实际募集金额',
  `start_money` decimal(14,2) NOT NULL COMMENT '起投金额',
  `dizeng_money` decimal(14,2) NOT NULL COMMENT '递增金额',
  `finish_date` int(10) DEFAULT NULL COMMENT '项目截止日',
  `start_date` int(10) NOT NULL COMMENT '融资开始日期',
  `end_date` int(10) NOT NULL COMMENT '融资结束日期',
  `channel` tinyint(1) DEFAULT '0' COMMENT '分销渠道',
  `description` text NOT NULL COMMENT '项目介绍',
  `full_time` int(10) NOT NULL COMMENT '满标时间',
  `jixi_time` int(10) DEFAULT NULL COMMENT '计息开始时间',
  `fk_examin_time` int(10) DEFAULT '0' COMMENT '放款审核时间',
  `account_name` varchar(50) DEFAULT '' COMMENT '账户名称',
  `account` varchar(50) DEFAULT '' COMMENT '账户',
  `bank` varchar(100) DEFAULT '' COMMENT '开户行',
  `del_status` int(10) DEFAULT '0' COMMENT '状态1-无效 0-有效',
  `online_status` tinyint(1) DEFAULT '0' COMMENT '上线状态：1上线0未上线',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '标的进展： 1预告期、 2进行中,3满标,4流标,5还款中,6已还清 ',
  `yuqi_faxi` decimal(14,6) NOT NULL COMMENT '逾期罚息',
  `order_limit` int(4) DEFAULT '200' COMMENT '限制投标uid，默认200次',
  `isPrivate` tinyint(1) DEFAULT '0' COMMENT '是否是定向标，0否1是',
  `allowedUids` varchar(200) DEFAULT NULL COMMENT '定向标用户id。以,分隔',
  `finish_rate` decimal(6,4) DEFAULT '0.0000' COMMENT '募集完成比例',
  `is_jixi` tinyint(1) DEFAULT '0' COMMENT '是否已经计息0否1是',
  `sort` tinyint(1) DEFAULT '0' COMMENT '排序',
  `contract_type` tinyint(1) DEFAULT '0' COMMENT '0固定1特殊模板',
  `creator_id` int(10) unsigned NOT NULL COMMENT '创建者管理员id',
  `created_at` int(10) unsigned DEFAULT NULL,
  `updated_at` int(10) unsigned DEFAULT NULL,
  `isFlexRate` tinyint(1) DEFAULT '0' COMMENT '是否启用浮动利率',
  `rateSteps` varchar(500) DEFAULT NULL COMMENT '浮动利率',
  `issuer` int(11) DEFAULT NULL,
  `issuerSn` varchar(30) DEFAULT NULL,
  `paymentDay` int(5) DEFAULT NULL,
  `isTest` tinyint(1) DEFAULT '0',
  `filingAmount` decimal(14,2) DEFAULT NULL,
  `allowUseCoupon` tinyint(1) NOT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `isLicai` tinyint(1) DEFAULT NULL,
  `pointsMultiple` smallint(6) DEFAULT '1',
  `allowTransfer` tinyint(1) DEFAULT '1',
  `isCustomRepayment` tinyint(1) DEFAULT NULL COMMENT '是否自定义还款',
  `isJixiExamined` tinyint(1) DEFAULT '1' COMMENT '计息审核',
  `internalTitle` varchar(255) DEFAULT NULL,
  `publishTime` datetime DEFAULT NULL,
  `balance_limit` decimal(14,2) DEFAULT '0.00',
  `allowRateCoupon` tinyint(1) DEFAULT '0' COMMENT '加息券使用0:禁止;1:允许',
  `originalBorrower` varchar(20) DEFAULT NULL COMMENT '底层融资方',
  `pkg_sn` varchar(30) DEFAULT NULL COMMENT '资产包sn',
  `isRedeemable` tinyint(1) DEFAULT '0',
  `redemptionPeriods` varchar(255) DEFAULT NULL,
  `redemptionPaymentDates` varchar(255) DEFAULT NULL,
  `isDailyAccrual` tinyint(1) DEFAULT '0',
  `flexRepay` tinyint(1) DEFAULT '0' COMMENT '灵活还款1是0否',
  `alternativeRepayer` int(11) DEFAULT NULL COMMENT '代偿方',
  `borrowerRate` decimal(6,4) DEFAULT NULL COMMENT '融资方利率',
  `fundReceiver` int(11) DEFAULT NULL COMMENT '用款方',
  `guarantee` int(11) DEFAULT NULL COMMENT '担保方',
  PRIMARY KEY (`id`),
  UNIQUE KEY `sn` (`sn`)
) ENGINE=InnoDB AUTO_INCREMENT=9963 DEFAULT CHARSET=utf8 COMMENT='线上标的产品表';

-- Data exporting was unselected.
-- Dumping structure for table wjf.online_repayment_plan
CREATE TABLE IF NOT EXISTS `online_repayment_plan` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `online_pid` int(10) unsigned NOT NULL COMMENT '标的id',
  `sn` varchar(30) NOT NULL COMMENT '计划编号',
  `order_id` int(10) NOT NULL COMMENT '投标订单序号',
  `qishu` int(10) NOT NULL COMMENT '还款期数',
  `uid` int(11) unsigned DEFAULT NULL COMMENT '借款人ID',
  `benxi` decimal(14,2) NOT NULL COMMENT '应还本息',
  `benjin` decimal(14,2) NOT NULL COMMENT '应还本金',
  `lixi` decimal(14,2) NOT NULL COMMENT '应还利息',
  `overdue` decimal(14,6) DEFAULT NULL COMMENT '逾期费用',
  `yuqi_day` varchar(50) NOT NULL COMMENT '逾期天数',
  `benxi_yue` decimal(14,2) NOT NULL COMMENT '本息余额',
  `refund_time` int(10) unsigned DEFAULT NULL COMMENT '还款时间',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '0、未还 1、已还 2、提前还款 3，无效',
  `created_at` int(10) unsigned DEFAULT NULL,
  `updated_at` int(10) unsigned DEFAULT NULL,
  `asset_id` int(10) DEFAULT NULL,
  `actualRefundTime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `online_pid` (`online_pid`),
  KEY `sn` (`sn`),
  KEY `order_id` (`order_id`),
  KEY `status` (`status`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=247750 DEFAULT CHARSET=utf8 COMMENT='标的还款计划表';

-- Data exporting was unselected.
-- Dumping structure for table wjf.online_repayment_record
CREATE TABLE IF NOT EXISTS `online_repayment_record` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `online_pid` int(10) unsigned DEFAULT NULL COMMENT '标的id',
  `order_id` int(10) NOT NULL COMMENT '投标订单id',
  `order_sn` varchar(30) NOT NULL COMMENT '订单sn',
  `qishu` int(10) NOT NULL COMMENT '还款期数',
  `uid` int(11) unsigned DEFAULT NULL COMMENT '借款人ID',
  `benxi` decimal(14,2) NOT NULL COMMENT '应还本息',
  `benjin` decimal(14,2) NOT NULL COMMENT '应还本金',
  `lixi` decimal(14,2) NOT NULL COMMENT '应还利息',
  `overdue` decimal(14,2) DEFAULT NULL COMMENT '逾期费用',
  `yuqi_day` varchar(50) NOT NULL COMMENT '逾期天数',
  `benxi_yue` decimal(14,2) NOT NULL COMMENT '本息余额',
  `refund_time` int(10) unsigned DEFAULT NULL COMMENT '还款时间',
  `status` tinyint(2) NOT NULL DEFAULT '0' COMMENT '0、未还 1、已还 2、提前还款 3，无效',
  `created_at` int(10) unsigned DEFAULT NULL,
  `updated_at` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=177328 DEFAULT CHARSET=utf8 COMMENT='还款记录表';

-- Data exporting was unselected.
-- Dumping structure for table wjf.open_account
CREATE TABLE IF NOT EXISTS `open_account` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `encryptedName` varchar(255) DEFAULT NULL,
  `encryptedIdCard` varchar(255) DEFAULT NULL,
  `status` varchar(10) DEFAULT NULL,
  `ip` varchar(255) DEFAULT NULL,
  `createTime` datetime DEFAULT NULL,
  `updateTime` datetime DEFAULT NULL,
  `message` varchar(255) DEFAULT NULL,
  `sn` varchar(30) DEFAULT NULL,
  `code` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=49141 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.option
CREATE TABLE IF NOT EXISTS `option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `questionId` int(11) NOT NULL COMMENT '题目ID',
  `content` text NOT NULL COMMENT '选项内容',
  `createTime` datetime DEFAULT NULL COMMENT '创建时间',
  `updateTime` datetime DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=481 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.orderqueue
CREATE TABLE IF NOT EXISTS `orderqueue` (
  `orderSn` varchar(30) NOT NULL COMMENT '订单sn',
  `status` tinyint(1) NOT NULL COMMENT '处理状态0未处理1处理',
  `updated_at` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  PRIMARY KEY (`orderSn`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.order_affiliation
CREATE TABLE IF NOT EXISTS `order_affiliation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL,
  `trackCode` varchar(255) DEFAULT NULL,
  `affiliator_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20636 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.page_meta
CREATE TABLE IF NOT EXISTS `page_meta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `href` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `keywords` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.payment
CREATE TABLE IF NOT EXISTS `payment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `payment_name` varchar(50) DEFAULT NULL COMMENT '支付公司',
  `institution_id` char(30) DEFAULT NULL COMMENT '在支付公司的标识【例如此数在中金的机构号id】',
  `receivenoticeurl` varchar(100) DEFAULT NULL COMMENT '前台通知地址',
  `receivenoticebackendurl` varchar(100) DEFAULT NULL COMMENT '后台通知地址',
  `path` varchar(100) DEFAULT NULL COMMENT '配置文件路径【可能包含银行、公私钥、日志等】',
  `status` tinyint(2) unsigned DEFAULT '0' COMMENT '状态 0-禁用 1-启用【标注支付公司是否可以使用-是否可以同时使用】',
  `created_at` int(10) unsigned DEFAULT NULL,
  `updated_at` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='支付公司';

-- Data exporting was unselected.
-- Dumping structure for table wjf.payment_log
CREATE TABLE IF NOT EXISTS `payment_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `txSn` varchar(32) NOT NULL,
  `amount` decimal(14,2) NOT NULL,
  `toParty_id` int(11) NOT NULL,
  `loan_id` int(11) NOT NULL,
  `createdAt` int(11) NOT NULL,
  `ref_id` int(11) DEFAULT NULL,
  `ref_type` smallint(6) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `txSn` (`txSn`)
) ENGINE=InnoDB AUTO_INCREMENT=8714 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.perf
CREATE TABLE IF NOT EXISTS `perf` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bizDate` date DEFAULT NULL,
  `uv` int(11) DEFAULT NULL,
  `pv` int(11) DEFAULT NULL,
  `bounceRate` float DEFAULT NULL,
  `reg` int(11) DEFAULT NULL,
  `regConv` float DEFAULT NULL,
  `idVerified` int(11) DEFAULT NULL,
  `qpayEnabled` int(11) DEFAULT NULL,
  `investor` int(11) DEFAULT NULL,
  `newInvestor` int(11) DEFAULT NULL,
  `chargeViaPos` decimal(14,2) DEFAULT NULL,
  `chargeViaEpay` decimal(14,2) DEFAULT NULL,
  `drawAmount` decimal(14,2) DEFAULT NULL,
  `investmentInWyj` decimal(14,2) DEFAULT NULL,
  `investmentInWyb` decimal(14,2) DEFAULT NULL,
  `totalInvestment` decimal(14,2) DEFAULT NULL,
  `successFound` decimal(14,2) DEFAULT NULL,
  `rechargeMoney` decimal(14,2) DEFAULT NULL,
  `rechargeCost` decimal(14,2) DEFAULT NULL,
  `draw` decimal(14,2) DEFAULT NULL,
  `created_at` int(10) DEFAULT NULL,
  `newRegisterAndInvestor` int(11) DEFAULT NULL,
  `investAndLogin` int(11) DEFAULT NULL,
  `notInvestAndLogin` int(11) DEFAULT NULL,
  `repayMoney` decimal(14,4) DEFAULT NULL,
  `repayLoanCount` int(11) DEFAULT NULL,
  `repayUserCount` int(11) DEFAULT NULL,
  `onlineInvestment` decimal(14,2) DEFAULT NULL,
  `offlineInvestment` decimal(14,2) DEFAULT NULL,
  `newRegAndNewInveAmount` decimal(14,2) DEFAULT NULL,
  `preRegAndNewInveAmount` decimal(14,2) DEFAULT NULL,
  `licaiNewInvCount` int(11) DEFAULT NULL,
  `licaiNewInvSum` decimal(14,2) DEFAULT NULL,
  `licaiInvCount` int(11) DEFAULT NULL,
  `licaiInvSum` decimal(14,2) DEFAULT NULL,
  `xsNewInvCount` int(11) DEFAULT NULL,
  `xsNewInvSum` decimal(14,2) DEFAULT NULL,
  `xsInvCount` int(11) DEFAULT NULL,
  `xsInvSum` decimal(14,2) DEFAULT NULL,
  `checkIn` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=985 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.points_batch
CREATE TABLE IF NOT EXISTS `points_batch` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `batchSn` varchar(32) NOT NULL,
  `createTime` datetime DEFAULT NULL,
  `isOnline` tinyint(1) DEFAULT NULL,
  `publicMobile` varchar(11) DEFAULT NULL,
  `safeMobile` varchar(255) DEFAULT NULL,
  `points` int(11) NOT NULL,
  `desc` varchar(255) DEFAULT NULL,
  `status` smallint(6) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `idCard` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4091 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.point_order
CREATE TABLE IF NOT EXISTS `point_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sn` varchar(32) DEFAULT NULL,
  `orderNum` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `points` int(11) DEFAULT NULL,
  `orderTime` datetime DEFAULT NULL,
  `isPaid` tinyint(1) DEFAULT NULL,
  `status` smallint(6) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `type` varchar(255) DEFAULT NULL,
  `isOffline` tinyint(1) DEFAULT '0',
  `offGoodsName` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=49058 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.point_record
CREATE TABLE IF NOT EXISTS `point_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sn` varchar(32) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ref_type` varchar(32) DEFAULT NULL,
  `ref_id` int(11) DEFAULT NULL,
  `incr_points` int(11) DEFAULT NULL,
  `decr_points` int(11) DEFAULT NULL,
  `final_points` int(11) DEFAULT NULL,
  `recordTime` datetime DEFAULT NULL,
  `userLevel` int(11) DEFAULT NULL,
  `isOffline` tinyint(1) DEFAULT '0',
  `offGoodsName` varchar(255) DEFAULT NULL,
  `remark` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1857129 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.poker
CREATE TABLE IF NOT EXISTS `poker` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `term` varchar(10) NOT NULL,
  `spade` int(11) NOT NULL,
  `heart` int(11) NOT NULL,
  `club` int(11) NOT NULL,
  `diamond` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `term` (`term`),
  UNIQUE KEY `unique_term` (`term`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.poker_user
CREATE TABLE IF NOT EXISTS `poker_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `term` varchar(10) NOT NULL COMMENT '期数',
  `spade` int(11) NOT NULL DEFAULT '0' COMMENT '黑桃',
  `heart` int(11) NOT NULL DEFAULT '0' COMMENT '红桃',
  `club` int(11) NOT NULL DEFAULT '0' COMMENT '梅花',
  `diamond` int(11) NOT NULL DEFAULT '0' COMMENT '方块',
  `order_id` int(11) DEFAULT NULL COMMENT '订单id',
  `firstVisitTime` datetime DEFAULT NULL COMMENT '本期首次访问时间',
  `checkInTime` datetime DEFAULT NULL COMMENT '本期签到时间',
  `createTime` datetime DEFAULT NULL,
  `updateTime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_unique` (`user_id`,`term`)
) ENGINE=InnoDB AUTO_INCREMENT=357416 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.promo
CREATE TABLE IF NOT EXISTS `promo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `key` varchar(50) DEFAULT NULL,
  `promoClass` varchar(255) DEFAULT NULL,
  `whiteList` varchar(255) DEFAULT NULL,
  `isOnline` tinyint(1) DEFAULT '0',
  `startTime` datetime NOT NULL,
  `endTime` datetime DEFAULT NULL,
  `config` text,
  `isO2O` tinyint(1) DEFAULT '0',
  `isHidden` tinyint(1) DEFAULT '0' COMMENT '是否隐藏',
  `sortValue` int(11) DEFAULT NULL COMMENT '排序值',
  `advSn` varchar(255) DEFAULT NULL COMMENT '首页轮播sn',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.promo0809_log
CREATE TABLE IF NOT EXISTS `promo0809_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `prize_id` int(1) NOT NULL,
  `user_address` varchar(255) NOT NULL,
  `createdAt` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.promo160520_log
CREATE TABLE IF NOT EXISTS `promo160520_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mobile` varchar(20) NOT NULL,
  `prizeId` smallint(1) NOT NULL,
  `isNewUser` tinyint(1) NOT NULL,
  `count` smallint(1) NOT NULL,
  `createdAt` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mobile` (`mobile`)
) ENGINE=InnoDB AUTO_INCREMENT=414 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.promo_lottery_ticket
CREATE TABLE IF NOT EXISTS `promo_lottery_ticket` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `isDrawn` int(1) DEFAULT '0',
  `isRewarded` int(1) DEFAULT '0',
  `reward_id` int(1) DEFAULT '0',
  `ip` varchar(30) DEFAULT NULL,
  `rewardedAt` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `source` varchar(30) DEFAULT NULL,
  `promo_id` int(11) DEFAULT NULL,
  `drawAt` int(11) DEFAULT NULL,
  `joinSequence` int(11) DEFAULT NULL,
  `duobaoCode` varchar(255) DEFAULT NULL,
  `expiryTime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=207526 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.promo_mobile
CREATE TABLE IF NOT EXISTS `promo_mobile` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `promo_id` int(11) DEFAULT NULL,
  `mobile` varchar(255) NOT NULL,
  `createTime` datetime NOT NULL,
  `ip` varchar(255) NOT NULL,
  `referralSource` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=25839 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.promo_sequence
CREATE TABLE IF NOT EXISTS `promo_sequence` (
  `id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.qpaybinding
CREATE TABLE IF NOT EXISTS `qpaybinding` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `binding_sn` varchar(32) NOT NULL COMMENT '绑卡流水号',
  `uid` int(10) unsigned DEFAULT NULL,
  `epayUserId` varchar(60) NOT NULL COMMENT '托管平台用户号',
  `bank_id` varchar(255) DEFAULT NULL COMMENT '银行id',
  `bank_name` varchar(255) DEFAULT NULL COMMENT '银行名称',
  `sub_bank_name` varchar(255) DEFAULT NULL COMMENT '开户支行名称',
  `province` varchar(30) DEFAULT NULL COMMENT '省',
  `city` varchar(30) DEFAULT NULL COMMENT '城市',
  `account` varchar(30) DEFAULT NULL COMMENT '持卡人姓名',
  `card_number` varchar(50) DEFAULT NULL COMMENT '银行卡号',
  `account_type` tinyint(2) unsigned DEFAULT '11' COMMENT '11=个人账户 12=企业账户',
  `status` tinyint(2) unsigned DEFAULT '0' COMMENT '状态 0-未绑定 1-已绑定 3-处理中',
  `created_at` int(10) unsigned DEFAULT NULL,
  `updated_at` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `binding_sn` (`binding_sn`),
  KEY `binding_sn_2` (`binding_sn`)
) ENGINE=InnoDB AUTO_INCREMENT=30572 DEFAULT CHARSET=utf8 COMMENT='用户绑卡申请表';

-- Data exporting was unselected.
-- Dumping structure for table wjf.qpayconfig
CREATE TABLE IF NOT EXISTS `qpayconfig` (
  `bankId` int(4) NOT NULL,
  `isDisabled` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否禁用0否1是',
  `singleLimit` decimal(14,2) NOT NULL COMMENT '单次限额',
  `dailyLimit` decimal(14,2) NOT NULL COMMENT '单日限额',
  `allowBind` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`bankId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.question
CREATE TABLE IF NOT EXISTS `question` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL COMMENT '问题',
  `batchSn` varchar(20) NOT NULL COMMENT '批次号',
  `promoId` int(11) DEFAULT NULL COMMENT '活动ID',
  `answer` varchar(255) DEFAULT NULL COMMENT '答案',
  `createTime` datetime DEFAULT NULL COMMENT '创建时间',
  `updateTime` datetime DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=465 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.queue_task
CREATE TABLE IF NOT EXISTS `queue_task` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `runnable` varchar(255) DEFAULT NULL,
  `params` text,
  `status` smallint(6) DEFAULT '0',
  `weight` smallint(6) DEFAULT '1',
  `runCount` int(11) DEFAULT '0',
  `lastRunTime` datetime DEFAULT NULL,
  `createTime` datetime DEFAULT NULL,
  `finishTime` datetime DEFAULT NULL,
  `runLimit` int(11) DEFAULT NULL,
  `nextRunTime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=364552 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.ranking_promo_offline_sale
CREATE TABLE IF NOT EXISTS `ranking_promo_offline_sale` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rankingPromoOfflineSale_id` int(11) NOT NULL,
  `mobile` varchar(11) DEFAULT NULL,
  `totalInvest` decimal(10,0) DEFAULT NULL,
  `investedAt` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.recharge_record
CREATE TABLE IF NOT EXISTS `recharge_record` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sn` varchar(30) DEFAULT NULL COMMENT '流水号',
  `pay_type` tinyint(1) NOT NULL COMMENT '1快捷充值,2网银充值',
  `pay_id` int(1) unsigned NOT NULL COMMENT '支付公司id',
  `account_id` int(10) unsigned NOT NULL COMMENT '对应资金账户id',
  `uid` int(10) unsigned DEFAULT NULL,
  `fund` decimal(14,2) unsigned DEFAULT '0.00' COMMENT '充值金额',
  `epayUserId` varchar(60) NOT NULL COMMENT '托管平台用户号',
  `clientIp` int(10) NOT NULL COMMENT 'ip地址',
  `pay_bank_id` varchar(30) NOT NULL COMMENT '取现银行代号【不同支付公司银行id可能不等。保存时按照统一的保存bank_id】',
  `bank_id` varchar(30) NOT NULL COMMENT '本平台银行id',
  `bankNotificationTime` datetime DEFAULT NULL COMMENT '支付平台收到银行通知时间，格式：YYYYMMDDhhmmss',
  `settlement` tinyint(1) DEFAULT '0' COMMENT '结算状态0未结算 10=已经受理 30=正在结算 40=已经执行(已发送转账指令) 50=转账退回',
  `remark` varchar(100) DEFAULT NULL COMMENT 'remark',
  `created_at` int(10) unsigned DEFAULT NULL,
  `updated_at` int(10) unsigned DEFAULT NULL,
  `status` tinyint(2) unsigned DEFAULT '0' COMMENT '状态 0-充值未处理   1-充值成功 2充值失败 ',
  `lastCronCheckTime` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sn` (`sn`),
  KEY `account_id` (`account_id`),
  KEY `uid` (`uid`),
  KEY `status` (`status`),
  KEY `status_2` (`status`),
  KEY `uid_2` (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=178758 DEFAULT CHARSET=utf8 COMMENT='充值记录表';

-- Data exporting was unselected.
-- Dumping structure for table wjf.referral
CREATE TABLE IF NOT EXISTS `referral` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `code` varchar(255) NOT NULL,
  `created_at` int(10) DEFAULT NULL,
  `updated_at` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.referral_source
CREATE TABLE IF NOT EXISTS `referral_source` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(15) NOT NULL,
  `target` varchar(255) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `isActive` tinyint(1) DEFAULT '1',
  `createTime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.region
CREATE TABLE IF NOT EXISTS `region` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '区域ID',
  `code` varchar(64) NOT NULL COMMENT '代码',
  `name` varchar(256) NOT NULL COMMENT '名称',
  `province_id` int(11) NOT NULL COMMENT '所属省ID（0不存在）',
  `city_id` int(11) NOT NULL COMMENT '所属市ID（0不存在）',
  `show_order` int(11) NOT NULL COMMENT '显示顺序',
  PRIMARY KEY (`id`),
  KEY `i_region_code` (`code`),
  KEY `i_region_province_city` (`province_id`,`city_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3508 DEFAULT CHARSET=utf8 COMMENT='区域表';

-- Data exporting was unselected.
-- Dumping structure for table wjf.repayment
CREATE TABLE IF NOT EXISTS `repayment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `loan_id` int(11) DEFAULT NULL,
  `term` int(2) DEFAULT NULL,
  `dueDate` date DEFAULT NULL,
  `amount` decimal(14,2) DEFAULT NULL,
  `principal` decimal(14,2) DEFAULT NULL,
  `interest` decimal(14,2) DEFAULT NULL,
  `isRepaid` int(1) DEFAULT '0',
  `repaidAt` datetime DEFAULT NULL,
  `isRefunded` int(1) DEFAULT '0',
  `refundedAt` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19401 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for view wjf.repayment_v1
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `repayment_v1` (
	`id` INT(11) NOT NULL,
	`loan_id` INT(11) NULL,
	`term` INT(2) NULL,
	`dueDate` DATE NULL,
	`amount` DECIMAL(14,2) NULL,
	`principal` DECIMAL(14,2) NULL,
	`interest` DECIMAL(14,2) NULL,
	`isRepaid` INT(1) NOT NULL,
	`repaidAt` VARCHAR(29) NULL COLLATE 'utf8mb4_general_ci',
	`isRefunded` INT(1) NOT NULL,
	`refundedAt` VARCHAR(29) NULL COLLATE 'utf8mb4_general_ci'
) ENGINE=MyISAM;

-- Dumping structure for view wjf.repayment_v2
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `repayment_v2` (
	`id` INT(11) NOT NULL,
	`loan_id` INT(11) NULL,
	`term` INT(2) NULL,
	`dueDate` DATE NULL,
	`amount` DECIMAL(14,2) NULL,
	`principal` DECIMAL(14,2) NULL,
	`interest` DECIMAL(14,2) NULL,
	`isRepaid` BIGINT(11) NULL,
	`repaidAt` DATETIME NULL,
	`isRefunded` BIGINT(11) NULL,
	`refundedAt` DATETIME NULL
) ENGINE=MyISAM;

-- Dumping structure for table wjf.repayment_vv
CREATE TABLE IF NOT EXISTS `repayment_vv` (
  `id` int(11) NOT NULL DEFAULT '0',
  `loan_id` int(11) DEFAULT NULL,
  `term` int(2) DEFAULT NULL,
  `dueDate` date DEFAULT NULL,
  `amount` decimal(14,2) DEFAULT NULL,
  `principal` decimal(14,2) DEFAULT NULL,
  `interest` decimal(14,2) DEFAULT NULL,
  `isRepaid` int(1) DEFAULT '0',
  `repaidAt` datetime DEFAULT NULL,
  `isRefunded` int(1) DEFAULT '0',
  `refundedAt` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.retention
CREATE TABLE IF NOT EXISTS `retention` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `seq` int(11) NOT NULL,
  `tactic_id` int(11) DEFAULT NULL,
  `status` varchar(20) NOT NULL,
  `startTime` datetime DEFAULT NULL,
  `createTime` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_tactic_user_seq` (`tactic_id`,`user_id`,`seq`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=9889 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.reward
CREATE TABLE IF NOT EXISTS `reward` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sn` varchar(50) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `limit` int(11) unsigned DEFAULT '0',
  `ref_type` varchar(255) NOT NULL,
  `ref_amount` decimal(14,2) DEFAULT NULL,
  `path` varchar(255) DEFAULT NULL,
  `promo_id` int(11) DEFAULT NULL,
  `createTime` datetime DEFAULT NULL,
  `ref_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sn` (`sn`)
) ENGINE=InnoDB AUTO_INCREMENT=271 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.risk_assessment
CREATE TABLE IF NOT EXISTS `risk_assessment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  `grade` int(11) NOT NULL,
  `content` varchar(255) NOT NULL,
  `createTime` datetime NOT NULL,
  `isDel` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15784 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.role
CREATE TABLE IF NOT EXISTS `role` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `sn` char(10) DEFAULT '' COMMENT '编号',
  `role_name` varchar(50) NOT NULL COMMENT '角色',
  `role_description` varchar(100) NOT NULL COMMENT '角色说明',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态0冻结，1激活',
  `updated_at` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sn_unique` (`sn`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.role_auth
CREATE TABLE IF NOT EXISTS `role_auth` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `role_sn` char(24) NOT NULL COMMENT '角色sn',
  `auth_sn` char(24) NOT NULL COMMENT '权限sn',
  `auth_name` varchar(30) NOT NULL COMMENT '权限名称',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态0冻结，1激活',
  `updated_at` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1770 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.second_kill
CREATE TABLE IF NOT EXISTS `second_kill` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(11) unsigned NOT NULL COMMENT '用户ID ',
  `createTime` int(11) unsigned NOT NULL COMMENT '获奖时间',
  `term` varchar(10) NOT NULL COMMENT '物品编号',
  PRIMARY KEY (`id`),
  UNIQUE KEY `term_2` (`userId`,`term`)
) ENGINE=InnoDB AUTO_INCREMENT=279 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.session
CREATE TABLE IF NOT EXISTS `session` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `batchSn` varchar(255) NOT NULL,
  `createTime` datetime DEFAULT NULL,
  `answers` text COMMENT '答题信息记录',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=101508 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.settle
CREATE TABLE IF NOT EXISTS `settle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `txSn` varchar(60) NOT NULL,
  `txDate` date NOT NULL,
  `money` decimal(14,2) NOT NULL,
  `fee` decimal(14,2) DEFAULT NULL,
  `serviceSn` varchar(60) NOT NULL,
  `txType` int(11) NOT NULL,
  `status` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1915 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.share
CREATE TABLE IF NOT EXISTS `share` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `shareKey` varchar(20) DEFAULT NULL,
  `title` varchar(200) DEFAULT NULL,
  `description` text,
  `imgUrl` varchar(100) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `shareKey` (`shareKey`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.share_log
CREATE TABLE IF NOT EXISTS `share_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL COMMENT '用户id',
  `scene` varchar(10) NOT NULL COMMENT '分享场景',
  `shareUrl` varchar(255) NOT NULL COMMENT '分享的url',
  `ipAddress` varchar(50) DEFAULT NULL COMMENT 'ip地址',
  `createdAt` date NOT NULL COMMENT '分享日期',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=34245 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.sms
CREATE TABLE IF NOT EXISTS `sms` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `code` char(10) DEFAULT NULL COMMENT '短信码',
  `time_len` int(4) DEFAULT NULL COMMENT '短信有效时长',
  `type` tinyint(1) DEFAULT NULL COMMENT '1注册验证码2密码找回验证码',
  `uid` int(4) DEFAULT NULL COMMENT 'uid',
  `username` varchar(50) DEFAULT NULL,
  `temp_uid` varchar(20) DEFAULT NULL COMMENT '临时用户uid',
  `status` tinyint(1) DEFAULT '0' COMMENT '状态0未用，1使用',
  `end_time` int(11) DEFAULT NULL COMMENT '截止日期',
  `created_at` int(11) DEFAULT NULL COMMENT '创建时间',
  `safeMobile` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=107563 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.sms_config
CREATE TABLE IF NOT EXISTS `sms_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template_id` varchar(255) NOT NULL,
  `config` text NOT NULL,
  `createTime` datetime NOT NULL,
  `updateTime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.sms_message
CREATE TABLE IF NOT EXISTS `sms_message` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `uid` int(4) NOT NULL COMMENT 'uid',
  `template_id` varchar(32) DEFAULT NULL,
  `message` varchar(1000) NOT NULL COMMENT '短信内容,json',
  `level` tinyint(1) NOT NULL COMMENT '1,2,3：1最高',
  `status` tinyint(1) DEFAULT '0' COMMENT '状态0未发送，1已发送',
  `created_at` int(11) DEFAULT NULL COMMENT '创建时间',
  `updated_at` int(11) DEFAULT NULL COMMENT '更新时间',
  `safeMobile` varchar(255) DEFAULT NULL,
  `serviceProvider` varchar(32) DEFAULT 'ytx' COMMENT '短信服务供应商',
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=1161410 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.social_connect
CREATE TABLE IF NOT EXISTS `social_connect` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `resourceOwner_id` varchar(128) NOT NULL,
  `provider_type` varchar(20) DEFAULT NULL,
  `createTime` datetime DEFAULT NULL,
  `isAutoLogin` tinyint(1) DEFAULT '1' COMMENT '是否自动登录',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_owner` (`resourceOwner_id`,`provider_type`)
) ENGINE=InnoDB AUTO_INCREMENT=43738 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.social_connect_log
CREATE TABLE IF NOT EXISTS `social_connect_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `resourceOwner_id` varchar(128) NOT NULL,
  `action` varchar(20) DEFAULT NULL,
  `provider_type` varchar(20) DEFAULT NULL,
  `data` text,
  `createTime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=45197 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.splash
CREATE TABLE IF NOT EXISTS `splash` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` char(60) NOT NULL COMMENT '标题',
  `sn` char(14) NOT NULL,
  `img640x960` int(11) unsigned DEFAULT NULL COMMENT '640x960对应的mediaID',
  `img640x1136` int(11) unsigned DEFAULT NULL COMMENT '640x1136对应的mediaID',
  `img750x1334` int(11) unsigned DEFAULT NULL COMMENT '750x1334对应的mediaID',
  `img1242x2208` int(11) unsigned DEFAULT NULL COMMENT '1242x2208对应的mediaID',
  `img1080x1920` int(11) unsigned DEFAULT NULL COMMENT '1080x1920对应的mediaID',
  `creator_id` int(11) DEFAULT NULL COMMENT '创建者ID',
  `publishTime` datetime DEFAULT NULL COMMENT '发布时间',
  `isPublished` smallint(6) unsigned DEFAULT NULL COMMENT '是否发布',
  `createTime` int(11) unsigned NOT NULL COMMENT '创建时间',
  `updateTime` int(11) unsigned DEFAULT NULL COMMENT '更新时间',
  `auto_publish` smallint(6) unsigned DEFAULT NULL COMMENT '是否自动发布',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.third_party_connect
CREATE TABLE IF NOT EXISTS `third_party_connect` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `publicId` varchar(255) NOT NULL,
  `visitor_id` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `thirdPartyUser_id` varchar(255) DEFAULT NULL,
  `createTime` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18985 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.ticket_token
CREATE TABLE IF NOT EXISTS `ticket_token` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=282514 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.tradelog
CREATE TABLE IF NOT EXISTS `tradelog` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `txType` varchar(50) NOT NULL COMMENT '交易标识',
  `direction` tinyint(1) NOT NULL COMMENT '1是请求，2是回调',
  `txSn` varchar(32) NOT NULL COMMENT '交易流水号',
  `uid` int(10) DEFAULT NULL,
  `requestData` text NOT NULL,
  `rawRequest` text COMMENT '请求内容',
  `responseCode` varchar(10) DEFAULT NULL COMMENT '响应码',
  `rawResponse` text COMMENT '响应内容',
  `responseMessage` varchar(100) NOT NULL COMMENT '响应消息内容',
  `duration` float NOT NULL COMMENT '同步请求花费时间',
  `txDate` date NOT NULL,
  `created_at` int(10) DEFAULT NULL,
  `updated_at` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4164566 DEFAULT CHARSET=utf8 COMMENT='交易日志表';

-- Data exporting was unselected.
-- Dumping structure for table wjf.transfer
CREATE TABLE IF NOT EXISTS `transfer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `createTime` datetime NOT NULL,
  `updateTime` datetime DEFAULT NULL,
  `amount` decimal(14,2) NOT NULL,
  `metadata` varchar(500) NOT NULL,
  `status` varchar(10) NOT NULL,
  `lastCronCheckTime` int(11) DEFAULT NULL,
  `sn` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_lastCronCheckTime` (`lastCronCheckTime`)
) ENGINE=InnoDB AUTO_INCREMENT=3411 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.transfer_tx
CREATE TABLE IF NOT EXISTS `transfer_tx` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sn` varchar(255) NOT NULL COMMENT '流水号',
  `userId` int(11) NOT NULL COMMENT '转账方用户ID',
  `money` decimal(10,2) NOT NULL COMMENT '转账金额',
  `status` smallint(6) NOT NULL DEFAULT '0' COMMENT '转账状态',
  `ref_sn` varchar(255) DEFAULT NULL COMMENT '关联业务流水号',
  `lastCronCheckTime` datetime DEFAULT NULL COMMENT '上次查询时间',
  `createTime` datetime DEFAULT NULL,
  `updateTime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sn` (`sn`)
) ENGINE=InnoDB AUTO_INCREMENT=3818 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.user
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `type` int(11) NOT NULL DEFAULT '2' COMMENT '会员类别 1-普通用户 2-机构用户',
  `username` char(32) NOT NULL COMMENT '会员账号',
  `usercode` char(32) NOT NULL COMMENT '会员编号',
  `email` varchar(50) DEFAULT NULL COMMENT 'Email',
  `real_name` varchar(50) DEFAULT NULL COMMENT '真实姓名',
  `tel` varchar(50) DEFAULT '' COMMENT '办公电话',
  `law_master` varchar(150) DEFAULT NULL COMMENT '法定代表人姓名',
  `law_master_idcard` varchar(90) DEFAULT NULL COMMENT '法定代表人身份证号',
  `law_mobile` char(11) NOT NULL,
  `business_licence` varchar(150) DEFAULT NULL COMMENT '营业执照号',
  `org_name` varchar(450) DEFAULT NULL COMMENT '企业名称',
  `org_code` varchar(90) DEFAULT NULL COMMENT '组织机构代码证号',
  `shui_code` varchar(150) DEFAULT NULL COMMENT '税务登记证号',
  `password_hash` char(128) NOT NULL COMMENT '用户密码hash',
  `trade_pwd` char(128) DEFAULT '' COMMENT '交易密码',
  `auth_key` char(128) DEFAULT NULL COMMENT 'cookie权限认证key',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态 0-锁定 1-正常',
  `idcard_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态 -1未通过 0-未验证 1-验证通过',
  `invest_status` tinyint(1) DEFAULT '1' COMMENT '投资状态0不可投，1可投，默认可投',
  `mianmiStatus` tinyint(1) DEFAULT '0' COMMENT '投资免密协议是否签署1签署0未签署',
  `last_login` int(10) unsigned DEFAULT NULL COMMENT '最后一次登录时间',
  `passwordLastUpdatedTime` datetime DEFAULT NULL COMMENT '最后修改密码时间',
  `regFrom` smallint(6) DEFAULT '0' COMMENT '注册来源',
  `updated_at` int(10) unsigned NOT NULL COMMENT '更新时间',
  `created_at` int(10) unsigned NOT NULL COMMENT '注册时间',
  `campaign_source` varchar(50) DEFAULT NULL COMMENT '百度统计来源标志',
  `is_soft_deleted` int(1) DEFAULT '0',
  `sort` int(3) DEFAULT '0',
  `regContext` varchar(255) NOT NULL,
  `registerIp` varchar(255) DEFAULT NULL,
  `points` int(11) DEFAULT '0',
  `annualInvestment` decimal(14,2) NOT NULL DEFAULT '0.00',
  `safeMobile` varchar(255) DEFAULT NULL,
  `safeIdCard` varchar(255) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `promoId` int(11) DEFAULT NULL,
  `crmAccount_id` int(11) DEFAULT NULL,
  `regLocation` varchar(255) DEFAULT NULL,
  `offlineUserId` int(11) DEFAULT NULL COMMENT '线下用户id',
  PRIMARY KEY (`id`),
  UNIQUE KEY `usercode` (`usercode`),
  KEY `crmAccount_id` (`crmAccount_id`)
) ENGINE=InnoDB AUTO_INCREMENT=75799 DEFAULT CHARSET=utf8 COMMENT='用户表';

-- Data exporting was unselected.
-- Dumping structure for table wjf.user_account
CREATE TABLE IF NOT EXISTS `user_account` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '类型 1,投资账户 2，融资账户',
  `uid` int(10) unsigned NOT NULL,
  `account_balance` decimal(14,2) DEFAULT '0.00' COMMENT '账户余额',
  `available_balance` decimal(14,2) NOT NULL DEFAULT '0.00' COMMENT '可用余额',
  `freeze_balance` decimal(14,2) DEFAULT '0.00' COMMENT '冻结余额',
  `profit_balance` decimal(14,2) DEFAULT '0.00' COMMENT '收益金额',
  `investment_balance` decimal(14,2) unsigned DEFAULT '0.00' COMMENT '理财金额',
  `drawable_balance` decimal(14,2) DEFAULT NULL,
  `in_sum` decimal(14,2) unsigned DEFAULT '0.00' COMMENT '账户入金总额',
  `out_sum` decimal(14,2) unsigned DEFAULT '0.00' COMMENT '账户出金总额',
  `created_at` int(10) unsigned DEFAULT NULL COMMENT '创建时间',
  `updated_at` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_uid` (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=75738 DEFAULT CHARSET=utf8 COMMENT='用户资金表';

-- Data exporting was unselected.
-- Dumping structure for table wjf.user_affiliation
CREATE TABLE IF NOT EXISTS `user_affiliation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `trackCode` varchar(255) DEFAULT NULL,
  `affiliator_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `affiliator_id` (`affiliator_id`)
) ENGINE=InnoDB AUTO_INCREMENT=52793 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.user_bank
CREATE TABLE IF NOT EXISTS `user_bank` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `binding_sn` varchar(32) DEFAULT NULL COMMENT '绑卡流水号',
  `uid` int(10) unsigned DEFAULT NULL,
  `epayUserId` varchar(60) NOT NULL COMMENT '托管平台用户号',
  `bank_id` varchar(255) DEFAULT NULL COMMENT '银行id',
  `bank_name` varchar(255) DEFAULT NULL COMMENT '银行名称',
  `sub_bank_name` varchar(255) DEFAULT NULL COMMENT '开户支行名称',
  `province` varchar(30) DEFAULT NULL COMMENT '省',
  `city` varchar(30) DEFAULT NULL COMMENT '城市',
  `account` varchar(30) DEFAULT NULL COMMENT '持卡人姓名',
  `card_number` varchar(50) DEFAULT NULL,
  `account_type` tinyint(2) unsigned DEFAULT '11' COMMENT '11=个人账户 12=企业账户',
  `mobile` varchar(11) DEFAULT NULL COMMENT '手机号码',
  `created_at` int(10) unsigned DEFAULT NULL,
  `updated_at` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `binding_sn` (`binding_sn`),
  UNIQUE KEY `uid` (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=23946 DEFAULT CHARSET=utf8 COMMENT='用户银行账号';

-- Data exporting was unselected.
-- Dumping structure for table wjf.user_coupon
CREATE TABLE IF NOT EXISTS `user_coupon` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `couponType_id` int(10) NOT NULL,
  `user_id` int(10) NOT NULL,
  `order_id` int(10) DEFAULT NULL,
  `isUsed` tinyint(1) NOT NULL,
  `created_at` int(10) NOT NULL,
  `expiryDate` date NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `ip` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=814498 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.user_info
CREATE TABLE IF NOT EXISTS `user_info` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `isInvested` int(1) DEFAULT '0',
  `investCount` int(5) DEFAULT '0',
  `investTotal` decimal(14,2) DEFAULT NULL,
  `firstInvestDate` date DEFAULT NULL,
  `lastInvestDate` date DEFAULT NULL,
  `firstInvestAmount` decimal(14,2) DEFAULT NULL,
  `lastInvestAmount` decimal(14,2) DEFAULT NULL,
  `averageInvestAmount` decimal(14,2) DEFAULT NULL,
  `isAffiliator` tinyint(1) DEFAULT '0',
  `creditInvestCount` int(11) DEFAULT '0',
  `creditInvestTotal` decimal(14,2) DEFAULT '0.00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=75168 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.user_promo
CREATE TABLE IF NOT EXISTS `user_promo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `promo_key` varchar(50) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_uid_key` (`user_id`,`promo_key`)
) ENGINE=InnoDB AUTO_INCREMENT=896 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.virtual_card
CREATE TABLE IF NOT EXISTS `virtual_card` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `serial` varchar(50) NOT NULL,
  `secret` varchar(255) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `isPull` tinyint(1) DEFAULT '0',
  `pullTime` datetime DEFAULT NULL,
  `isUsed` tinyint(1) DEFAULT '0',
  `usedTime` datetime DEFAULT NULL,
  `createTime` datetime DEFAULT NULL,
  `goodsType_id` int(11) NOT NULL,
  `expiredTime` datetime DEFAULT NULL,
  `affiliator_id` int(11) DEFAULT NULL,
  `usedMobile` varchar(20) DEFAULT NULL,
  `isReserved` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `serial` (`serial`)
) ENGINE=InnoDB AUTO_INCREMENT=664 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.voucher
CREATE TABLE IF NOT EXISTS `voucher` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ref_type` varchar(255) DEFAULT NULL,
  `ref_id` varchar(255) DEFAULT NULL,
  `goodsType_sn` varchar(255) DEFAULT NULL,
  `card_id` int(11) DEFAULT NULL,
  `promo_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `isRedeemed` tinyint(1) DEFAULT '0',
  `redeemTime` datetime DEFAULT NULL,
  `redeemIp` varchar(255) DEFAULT NULL,
  `createTime` datetime NOT NULL,
  `orderNum` varchar(255) DEFAULT NULL,
  `expireTime` datetime DEFAULT NULL,
  `isOp` tinyint(1) DEFAULT '0',
  `amount` decimal(14,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_ref_key` (`ref_type`,`ref_id`),
  UNIQUE KEY `orderNum` (`orderNum`)
) ENGINE=InnoDB AUTO_INCREMENT=41380 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.wechat_reply
CREATE TABLE IF NOT EXISTS `wechat_reply` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) DEFAULT NULL COMMENT '回复类型',
  `keyword` varchar(255) DEFAULT NULL COMMENT '关键字',
  `content` text COMMENT '内容',
  `isDel` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `createdAt` int(11) NOT NULL COMMENT '创建时间',
  `updatedAt` int(11) NOT NULL COMMENT '更新时间',
  `style` varchar(255) DEFAULT NULL COMMENT '消息类型',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.weixin_auth
CREATE TABLE IF NOT EXISTS `weixin_auth` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `appId` varchar(255) NOT NULL,
  `accessToken` varchar(255) DEFAULT NULL,
  `jsApiTicket` varchar(255) DEFAULT NULL,
  `expiresAt` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `appId` (`appId`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table wjf.weixin_url
CREATE TABLE IF NOT EXISTS `weixin_url` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `auth_id` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for view wjf.online_product_v1
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `online_product_v1`;
CREATE ALGORITHM=UNDEFINED DEFINER=`wjf2017`@`%` SQL SECURITY DEFINER VIEW `online_product_v1` AS select `online_product`.`id` AS `id`,`online_product`.`epayLoanAccountId` AS `epayLoanAccountId`,`online_product`.`title` AS `title`,`online_product`.`sn` AS `sn`,`online_product`.`cid` AS `cid`,`online_product`.`is_xs` AS `is_xs`,`online_product`.`recommendTime` AS `recommendTime`,`online_product`.`borrow_uid` AS `borrow_uid`,`online_product`.`yield_rate` AS `yield_rate`,`online_product`.`jiaxi` AS `jiaxi`,`online_product`.`fee` AS `fee`,`online_product`.`expires_show` AS `expires_show`,`online_product`.`refund_method` AS `refund_method`,`online_product`.`expires` AS `expires`,`online_product`.`kuanxianqi` AS `kuanxianqi`,`online_product`.`money` AS `money`,`online_product`.`funded_money` AS `funded_money`,`online_product`.`start_money` AS `start_money`,`online_product`.`dizeng_money` AS `dizeng_money`,`online_product`.`finish_date` AS `finish_date`,`online_product`.`start_date` AS `start_date`,`online_product`.`end_date` AS `end_date`,`online_product`.`channel` AS `channel`,`online_product`.`description` AS `description`,`online_product`.`full_time` AS `full_time`,`online_product`.`jixi_time` AS `jixi_time`,`online_product`.`fk_examin_time` AS `fk_examin_time`,`online_product`.`account_name` AS `account_name`,`online_product`.`account` AS `account`,`online_product`.`bank` AS `bank`,`online_product`.`del_status` AS `del_status`,`online_product`.`online_status` AS `online_status`,if((`online_product`.`id` in ('id',3967,3969,3970,3979,4009,4015,4049,4072,4084,4118,4477,4485,4516,4531,4547,4558,4567,4604,4640,4695,4732,4752,4765,4803,4821,4861,4892,4913,4986,5031,5040,5053,5080,5101,5119,5165,5172,5208,5209,5230,5262,5264,5267,5274,5317,5330,5366,5391,5403,5409,5416,5421,5438,5473,5508,5524,5651,5678,5684,5691,5693,5702,5709,5710,5717,5732,5737,5750,5751,5752,5758,5759,5760,5762,5769,5774,5778,5782,5783,5787,5793,5811,5818,5828,5830,5836,5844,5850,5858,5870,5876,5883,5886,5892,5893,6020,6033,6039,6069,6070,6077,6078,6091,6100,6105,6106,6113,6115,6124,6135,6138,6144,6153,6162,6167,6180,6188,6189,6196,6197,6212,6233,6238,6258,6260,6265,6268,6271,6275,6279,6285,6295,6304,6308,6312,6319,6332,6334,6341,6345,6346,6359,6367,6370,6379,6384,6397,6407,6419,6422,6426,6431,6435,6440,6441,6442,6452,6454,6464,6478,6484,6500,6512,6529,6534,6542,6559,6576,6577,6589,6606,6610,6622,6635,6657,6659,6662,6671,6676,6680,6691,6703,6711,6721,6734,6776,6778,6801,6810,6812,6818,6829,6839,6840,6852,6865,6872,6881,6890,6897,6914,6935,6950,6963,6984,6995,6999,7026,7042,7045,7067,7074,7076,7090,7093,7100,7102,7109,7123,7138,7141,7147,7153,7157,7162,7171,7172,7176,7181,7186,7189,7192,7201,7202,7203,7206,7207,7214,7220,7221,7224,7230,7231,7238,7243,7244,7245,7256,7257,7258,7259,7260,7263,7264,7267,7268,7279,7284,7287,7294,7302,7307,7309,7316,7321,7325,7326,7336,7338,7342,7347,7353,7356,7359,7367,7372,7375,7381,7389,7398,7399,7400,7401,7402,7403,7408,7420,7422,7426,7434,7435,7447,7453,7455,7464,7471,7473,7475,7482,7487,7491,7496,7500,7512,7513,7514,7515,7516,7517,7540,7541,7556,7559,7560,7563,7564,7565,7566,7567,7580,7585,7589,7597,7604,7606,7609,7614,7621,7631,7634,7641,7642,7645,7662,7665,7677,7679,7681,7688,7690,7693,7696,7697,7704,7709,7710,7713,7715,7722,7723,7724,7727,7728,7730,7733,7734,7739,7746,7748,7752,7757,7758,7763,7765,7766,7771,7777,7779,7780,7781,7784,7787,7790,7797,7798,7799,7801,7803,7804,7805,7809,7811,7818,7821,7825,7827,7833,7840,7841,7897,7949,7950,7954,7961,7968,7969,7970,7974,7976,7981,7989,7994,7996,7998,8004,8008,8009,8010,8012,8023,8025,8029,8030,8032,8036,8041,8049,8050,8051,8052,8055,8056,8057,8058,8082,8083,8084,8085,8086,8087,8088,8089,8090,8091,8092,8093,8105,8106,8107,8112,8121,8126,8127,8128,8129,8133,8141,8143,8145,8150,8153,8161,8164,8166,8177,8178,8184,8187,8189,8192,8193,8196,8201,8204,8208,8210,8211,8214,8215,8216,8221,8224,8229,8230,8231,8234,8235,8239,8241,8243,8245,8253,8255,8256,8257,8258,8263,8268,8269,8273,8277,8279,8281,8285,8291,8292,8297,8300,8301,8304,8319,8322,8325,8328,8331,8335,8337,8338,8342,8343,8344,8348,8350,8353,8355,8356,8358,8366,8367,8370,8372,8373,8381,8383,8386,8389,8394,8405,8410,8412,8415,8423,8429,8435,8436,8439,8440,8443,8456,8458,8469,8471,8472,8473,8485,8495,8497,8499,8505,8518,8528,8530,8532,8539,8548,8570,8578,8589,8595,8603,8604,8611,8618,8632,8634,8652,8660,8674,8675,8676,8694,8706,8710,8711,8712,8714,8717,8721,8722,8725,8726,8727,8729,8737,8738,8739,8741,8747,8751,8754,8757,8759,8760,8767,8768,8776,8778,8779,8787,8791,8793,8797,8799,8806,8808,8812,8814,8818,8825,8828,8831,8832,8847,8848,8850,8852,8854,8859,8863,8865,8873,8874,8877,8882,8886,8888,8891,8894,8895,8897,8898,8899,8908,8911,8913,8916,8917,8921,8922,8923,8924,8929,8930,8931,8932,8933,8935,8936,8940,8943,8944,8945,8946,8947,8948,8949,8951,8953,8955,8956,8958,8963,8964,8965,8966,8978,8979,8980,8982,8983,8984,8986,8988,8989,8990,8991,8992,8993,8994,8995,8996,8997,8998,8999,9000,9001,9003,9004,9005,9006,9007,9009,9010,9015,9016,9017,9019,9020,9022,9023,9024,9025,9027,9028,9030,9031,9032,9036,9037,9039,9040,9042,9043,9045,9046,9047,9049,9051,9053,9054,9057,9059,9060,9063,9064,9065,9066,9068,9069,9072,9073,9074,9078,9086,9088,9090,9092,9093,9094,9095,9096,9097,9098,9099,9102,9103,9104,9106,9107,9111,9112,9113,9119,9121,9122,9124,9125,9127,9128,9129,9130,9131,9132,9134,9141,9144,9151,9152,9154,9156,9161,9162,9165,9166,9168,9169,9171,9172,9175,9179,9181,9182,9183,9185,9186,9188,9190,9191,9195,9196,9199,9200,9201,9202,9203,9206,9209,9210,9214,9215,9217,9223,9224,9225,9226,9227,9229,9231,9232,9233,9234,9235,9236,9237,9239,9243,9244,9245,9246,9247,9249,9250,9252,9253,9254,9255,9258,9261,9262,9265,9266,9267,9268,9269,9270,9271,9272,9274,9275,9276,9277,9278,9280,9283,9284,9287,9288,9289,9290,9293,9294,9296,9297,9300,9301,9303,9304,9306,9307,9308,9309,9310,9311,9312,9313,9315,9317,9320,9322,9324,9331,9332,9333,9334,9336,9337,9338,9340,9342,9345,9346,9348,9350,9351,9356,9358,9387)),6,`online_product`.`status`) AS `status`,`online_product`.`yuqi_faxi` AS `yuqi_faxi`,`online_product`.`order_limit` AS `order_limit`,`online_product`.`isPrivate` AS `isPrivate`,`online_product`.`allowedUids` AS `allowedUids`,`online_product`.`finish_rate` AS `finish_rate`,`online_product`.`is_jixi` AS `is_jixi`,`online_product`.`sort` AS `sort`,`online_product`.`contract_type` AS `contract_type`,`online_product`.`creator_id` AS `creator_id`,`online_product`.`created_at` AS `created_at`,`online_product`.`updated_at` AS `updated_at`,`online_product`.`isFlexRate` AS `isFlexRate`,`online_product`.`rateSteps` AS `rateSteps`,`online_product`.`issuer` AS `issuer`,`online_product`.`issuerSn` AS `issuerSn`,`online_product`.`paymentDay` AS `paymentDay`,`online_product`.`isTest` AS `isTest`,`online_product`.`filingAmount` AS `filingAmount`,`online_product`.`allowUseCoupon` AS `allowUseCoupon`,`online_product`.`tags` AS `tags`,`online_product`.`isLicai` AS `isLicai`,`online_product`.`pointsMultiple` AS `pointsMultiple`,`online_product`.`allowTransfer` AS `allowTransfer`,`online_product`.`isCustomRepayment` AS `isCustomRepayment`,`online_product`.`isJixiExamined` AS `isJixiExamined`,`online_product`.`internalTitle` AS `internalTitle`,`online_product`.`publishTime` AS `publishTime`,`online_product`.`balance_limit` AS `balance_limit`,`online_product`.`allowRateCoupon` AS `allowRateCoupon`,`online_product`.`originalBorrower` AS `originalBorrower`,`online_product`.`pkg_sn` AS `pkg_sn`,`online_product`.`isRedeemable` AS `isRedeemable`,`online_product`.`redemptionPeriods` AS `redemptionPeriods`,`online_product`.`redemptionPaymentDates` AS `redemptionPaymentDates`,`online_product`.`isDailyAccrual` AS `isDailyAccrual`,`online_product`.`flexRepay` AS `flexRepay`,`online_product`.`alternativeRepayer` AS `alternativeRepayer`,`online_product`.`borrowerRate` AS `borrowerRate`,`online_product`.`fundReceiver` AS `fundReceiver`,`online_product`.`guarantee` AS `guarantee` from `online_product` where ((((`online_product`.`finish_date` > 0) and (`online_product`.`finish_date` < 1522512000)) or (`online_product`.`id` in (3915,3946,3952,3959,3967,3969,3970,3978,3979,4009,4015,4049,4072,4084,4118,4129,4165,4197,4198,4199,4200,4201,4477,4485,4516,4531,4547,4558,4567,4604,4640,4695,4732,4752,4765,4803,4821,4861,4892,4913,4986,5031,5040,5053,5080,5101,5119,5165,5172,5208,5209,5230,5262,5264,5267,5274,5317,5330,5366,5391,5403,5409,5416,5421,5438,5473,5508,5524,5651,5678,5684,5691,5693,5702,5709,5710,5717,5732,5737,5750,5751,5752,5758,5759,5760,5762,5769,5774,5778,5782,5783,5787,5793,5811,5818,5828,5830,5836,5844,5850,5858,5870,5876,5883,5886,5892,5893,5921,5947,5963,5977,5990,5995,6020,6027,6033,6036,6039,6060,6066,6069,6070,6077,6078,6085,6091,6100,6105,6106,6109,6113,6115,6118,6119,6124,6125,6135,6136,6138,6144,6145,6153,6157,6159,6162,6167,6172,6180,6188,6189,6193,6196,6197,6200,6202,6205,6212,6215,6227,6233,6238,6244,6245,6258,6260,6264,6265,6268,6271,6274,6275,6279,6285,6295,6300,6304,6308,6312,6319,6332,6333,6334,6341,6345,6346,6359,6366,6367,6370,6379,6380,6384,6385,6397,6407,6414,6419,6420,6422,6426,6431,6435,6440,6441,6442,6450,6452,6454,6457,6458,6464,6478,6484,6490,6497,6500,6505,6512,6521,6527,6529,6534,6542,6543,6547,6559,6563,6565,6576,6577,6581,6587,6589,6591,6599,6606,6607,6609,6610,6622,6630,6633,6635,6638,6649,6653,6657,6659,6662,6668,6671,6676,6680,6683,6684,6688,6691,6695,6703,6704,6708,6711,6713,6721,6729,6731,6734,6736,6758,6760,6776,6778,6784,6795,6801,6808,6810,6811,6812,6814,6815,6817,6818,6829,6830,6834,6837,6839,6840,6852,6855,6865,6872,6881,6890,6897,6899,6901,6907,6908,6914,6917,6924,6927,6932,6935,6950,6955,6963,6966,6972,6984,6995,6999,7014,7026,7031,7040,7042,7045,7046,7054,7064,7067,7074,7076,7086,7090,7093,7098,7100,7101,7102,7109,7123,7138,7141,7147,7151,7153,7157,7162,7171,7172,7174,7176,7181,7186,7189,7192,7195,7201,7202,7203,7206,7207,7214,7215,7220,7221,7224,7230,7231,7238,7243,7244,7245,7249,7253,7256,7257,7258,7259,7260,7263,7264,7267,7268,7279,7284,7286,7287,7294,7301,7302,7307,7309,7312,7316,7319,7321,7325,7326,7333,7336,7338,7342,7347,7351,7353,7356,7359,7367,7372,7375,7378,7381,7389,7398,7399,7400,7401,7402,7403,7408,7411,7412,7420,7422,7426,7429,7434,7435,7443,7445,7446,7447,7453,7455,7463,7464,7465,7470,7471,7473,7475,7482,7485,7487,7491,7496,7497,7500,7512,7513,7514,7515,7516,7517,7520,7526,7538,7540,7541,7543,7555,7556,7559,7560,7563,7564,7565,7566,7567,7571,7573,7575,7580,7585,7588,7589,7597,7604,7606,7609,7614,7621,7628,7631,7634,7641,7642,7645,7662,7665,7677,7679,7681,7685,7688,7690,7693,7696,7697,7700,7703,7704,7709,7710,7712,7713,7715,7722,7723,7724,7725,7727,7728,7729,7730,7733,7734,7735,7739,7746,7748,7752,7757,7758,7763,7764,7765,7766,7771,7773,7777,7779,7780,7781,7783,7784,7787,7788,7790,7797,7798,7799,7801,7803,7804,7805,7809,7811,7817,7818,7819,7821,7824,7825,7827,7833,7835,7840,7841,7848,7853,7865,7884,7887,7897,7903,7924,7940,7949,7950,7954,7959,7961,7968,7969,7970,7973,7974,7976,7981,7982,7989,7990,7994,7996,7997,7998,8004,8007,8008,8009,8010,8012,8016,8023,8025,8029,8030,8032,8034,8036,8041,8049,8050,8051,8052,8055,8056,8057,8058,8060,8064,8066,8068,8082,8083,8084,8085,8086,8087,8088,8089,8090,8091,8092,8093,8094,8097,8098,8099,8100,8101,8105,8106,8107,8112,8121,8126,8127,8128,8129,8133,8141,8143,8145,8146,8149,8150,8153,8156,8161,8164,8166,8169,8177,8178,8184,8187,8189,8192,8193,8196,8201,8204,8208,8210,8211,8214,8215,8216,8221,8224,8229,8230,8231,8234,8235,8239,8241,8243,8245,8246,8253,8255,8256,8257,8258,8259,8263,8268,8269,8273,8277,8279,8281,8285,8291,8292,8297,8300,8301,8304,8319,8322,8325,8328,8330,8331,8335,8337,8338,8342,8343,8344,8348,8350,8353,8355,8356,8357,8358,8366,8367,8369,8370,8372,8373,8381,8382,8383,8384,8385,8386,8387,8389,8390,8391,8392,8394,8396,8405,8410,8412,8415,8423,8426,8429,8435,8436,8439,8440,8442,8443,8455,8456,8458,8461,8465,8469,8471,8472,8473,8475,8480,8485,8491,8495,8497,8499,8504,8505,8510,8515,8518,8528,8529,8530,8532,8539,8540,8548,8551,8561,8570,8573,8578,8589,8595,8602,8603,8604,8611,8618,8620,8627,8632,8634,8648,8652,8660,8664,8674,8675,8676,8677,8694,8696,8706,8710,8711,8712,8714,8717,8721,8722,8725,8726,8727,8729,8737,8738,8739,8741,8747,8751,8754,8757,8759,8760,8767,8768,8776,8778,8779,8787,8791,8793,8797,8799,8802,8806,8808,8812,8814,8818,8825,8828,8831,8832,8845,8847,8848,8850,8852,8854,8856,8859,8863,8865,8866,8873,8874,8877,8878,8882,8886,8888,8891,8894,8895,8897,8898,8899,8908,8911,8913,8916,8917,8921,8922,8923,8924,8929,8930,8931,8932,8933,8935,8936,8940,8943,8944,8945,8946,8947,8948,8949,8951,8953,8955,8956,8958,8963,8964,8965,8966,8978,8979,8980,8982,8983,8984,8986,8988,8989,8990,8991,8992,8993,8994,8995,8996,8997,8998,8999,9000,9001,9003,9004,9005,9006,9007,9009,9010,9015,9016,9017,9019,9020,9021,9022,9023,9024,9025,9027,9028,9030,9031,9032,9036,9037,9038,9039,9040,9042,9043,9045,9046,9047,9049,9051,9053,9054,9057,9058,9059,9060,9063,9064,9065,9066,9067,9068,9069,9072,9073,9074,9078,9086,9088,9090,9092,9093,9094,9095,9096,9097,9098,9099,9102,9103,9104,9106,9107,9111,9112,9113,9119,9120,9121,9122,9124,9125,9127,9128,9129,9130,9131,9132,9134,9141,9144,9151,9152,9154,9156,9161,9162,9165,9166,9168,9169,9171,9172,9175,9179,9181,9182,9183,9185,9186,9188,9190,9191,9193,9195,9196,9199,9200,9201,9202,9203,9206,9209,9210,9214,9215,9217,9222,9223,9224,9225,9226,9227,9229,9231,9232,9233,9234,9235,9236,9237,9239,9243,9244,9245,9246,9247,9249,9250,9252,9253,9254,9255,9258,9261,9262,9265,9266,9267,9268,9269,9270,9271,9272,9274,9275,9276,9277,9278,9280,9283,9284,9287,9288,9289,9290,9291,9293,9294,9296,9297,9300,9301,9303,9304,9306,9307,9308,9309,9310,9311,9312,9313,9315,9317,9320,9322,9324,9331,9332,9333,9334,9336,9337,9338,9340,9342,9345,9346,9348,9350,9351,9356,9357,9358,9387)) or (`online_product`.`cid` = 3)) and (`online_product`.`id` not in (889,891,892,893,895,897,898,905,907,910,914,918,920,930,933,941,944,947,954,968,976,984,993,998,1001,1009,1016,1027,1036,1049,1054,1061,1065,1071,1086,1098,1111,1121,1128,1131,1140,1150,1154,1164,1173,1185,1211,1214,1225,1226,1239,1253,1262,1282,1288,1289,1295,1305,1315,1323,1330,1339,1354,1356,1358,1365,1375,1385,1402,1409,1423,1434,1449,1459,1464,1540,1562,1563,1564,1565,1566,1568,1569,1572,1574,1575,1580,1596,1605,1612,1631,1653,1654,1655,1656,1674,1696,1718,1734,1748,1750,1759,1765,1775,1786,1807,1822,1831,1832,1835,1851,1868,1886,1915,1917,1942,1943,1952,1966,1989,2007,2028,2035,2037,2055,2079,2096,2115,2143,2146,2147,2150,2156,2171,2180,2191,2194,2220,2228,2230,2233,2248,2264,2284,2295,2316,2326,2328,2331,2346,2365,2393,2409,2420,2439,2460,2474,2483,2508,2515,2525,2541,2559,2574,2598,2625,2637,2642,2657,2673,2687,2708,2743,2751,2757,2765,2778,2792,2806,2830,2838,2844,2857,2869,2893,2912,2936,2944,2951,2970,2992,3006,3018,3036,3064,3076,3094,3121,3141,3166,3182,3219,3225,3230,3231,3258,3285,3311,3322,3328,3342,3361,3381,3402,3425,3433,3439,3455,3473,3494,3510,3523,3529,3533,3565,3591,3610,3626,3644,3654,3662,3680,3702,3831,3841,3862,3885,3916,3928,3931,3965,4002,4022,4043,4075,4083,4088,4115,4133,4147,4183,4207,4209,4210,4230,4231,4245,4262,4275,4297,4320,4328,4334,4351,4370,4381,4402,4457,4458,4459,4463,4472,4488,4504,4523,4550,4557,4563,4580,4601,4615,4631,4651,4652,4665,4681,4703,4722,4742,4760,4770,4772,4777,4796,4799,4812,4832,4844,4868,4880,4885,4903,4921,4931,4949,4987,4994,4997,5019,5041,5055,5077,5105,5114,5121,5147,5175,5178,5202,5227,5266,5278,5287,5306,5333,5354,5374,5422,5455,5462,5467,5471,5480,5484,5493,5496,5501,5532,5561,5578,5604,5629,5638,5643,5663,5677,5681,5701,5724,5763,5764,5784,5812,5833,5856,5874,5904,5918,5925,5926,5937,5954,5976,6002,6016,6024,6040,6049,6055,6073,6083,6101,6381,6388,6396,6415,6433,6443,6453,6468,6488,6509,6536,6564,6582,6583,6595,6614,6629,6642,6666,6677,6679,6699,6726,6738,6749,6769,6793,6800,6803,6836,6864,6891,6918,6953,6957,6961,6983,7006,7032,7053,7085,7096,7112,7121,7144,7175,7191,7218,7229,7236,7254,7280,7293,7323,7354,7364,7374,7391,7414,7437,7459,7499,7522,7524,7534,7554,7579,7598,7623,7635,7646,7647,7669,7695,7714,7737,7760,7768,7775,7791,7814,7831,7851,7886,7892,7920,7963,7992,8015,8038,8065,8108,8109,8113,8114,8117,8119,8132,8151,8175,8199,8207,8227,8247,8282,8309,8347,8349,8351,8365,8374,8402,8451,8463,8487,8526,8536,8543,8562,8582,8608,8637,8661,8669,8682,8686,8700,8719,8732,8746,8764,8774,8777,8796,8816,8836,8855,8879,8885,8890,8910,8934,8952,8961,8967,8968,8981,9008,9029,9050,9080,9100,9108,9114,9138,9155,9170,9189,9208,9213,9216,9238,9377,9381,9411,9420,9438,9446,9459,9530,9532,9950,9951,9957)));

-- Dumping structure for view wjf.online_product_v2
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `online_product_v2`;
CREATE ALGORITHM=UNDEFINED DEFINER=`wjf2017`@`%` SQL SECURITY DEFINER VIEW `online_product_v2` AS select `a`.`id` AS `id`,`a`.`epayLoanAccountId` AS `epayLoanAccountId`,`a`.`title` AS `title`,`a`.`sn` AS `sn`,`a`.`cid` AS `cid`,`a`.`is_xs` AS `is_xs`,`a`.`recommendTime` AS `recommendTime`,`a`.`borrow_uid` AS `borrow_uid`,`a`.`yield_rate` AS `yield_rate`,`a`.`jiaxi` AS `jiaxi`,`a`.`fee` AS `fee`,`a`.`expires_show` AS `expires_show`,`a`.`refund_method` AS `refund_method`,`a`.`expires` AS `expires`,`a`.`kuanxianqi` AS `kuanxianqi`,`a`.`money` AS `money`,`a`.`funded_money` AS `funded_money`,`a`.`start_money` AS `start_money`,`a`.`dizeng_money` AS `dizeng_money`,`a`.`finish_date` AS `finish_date`,`a`.`start_date` AS `start_date`,`a`.`end_date` AS `end_date`,`a`.`channel` AS `channel`,`a`.`description` AS `description`,`a`.`full_time` AS `full_time`,`a`.`jixi_time` AS `jixi_time`,`a`.`fk_examin_time` AS `fk_examin_time`,`a`.`account_name` AS `account_name`,`a`.`account` AS `account`,`a`.`bank` AS `bank`,if((`a`.`id` in (1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,45,47,48,49,50,51,52,56,58,59,62,64,65,66,67,73,75,77,78,79,80,86,87,92,95,101,102,103,104,108,113,117,124,125,130,131,132,136,137,141,145,153,154,155,160,161,173,174,175,192,193,203,204,206,207,208,211,212,215,216,217,222,228,230,231,232,242,243,249,250,254,257,258,259,260,263,264,269,270,271,273,277,279,284,285,287,288,290,291,295,296,297,299,301,302,303,304,305,306,307,308,310,312,313,314,315,316,317,318,320,321,323,324,327,328,330,331,332,333,334,337,340,343,352,353,356,359,361,363,365,369,375,376,378,381,385,387,398,403,404,406,408,409,410,413,420,421,422,423,431,437,440,443,444,446,448,452,454,459,461,462,467,473,476,481,483,488,495,497,505,510,514,531,554,573,588,597,608,611,615,616,635,638,648,649,662,664,667,670,679,686,695,703,711,713,718,723,725,727,728,731,732,736,740,744,745,756,758,759,760,762,764,776,777,779,785,786,790,793,794,806,820,830,837,838,839,840,844,845,852,861,872,883,886,889,891,892,893,895,897,898,900,905,906,907,910,911,913,914,917,918,920,926,930,933,936,941,942,944,945,947,948,954,956,958,968,976,982,984,986,993,998,999,1001,1009,1016,1027,1036,1049,1054,1061,1065,1071,1086,1098,1111,1121,1128,1131,1140,1150,1154,1164,1172,1173,1185,1203,1205,1206,1211,1214,1225,1226,1227,1236,1239,1246,1249,1253,1262,1272,1274,1282,1288,1289,1291,1295,1305,1315,1323,1330,1339,1354,1355,1356,1358,1363,1365,1375,1385,1393,1402,1409,1423,1434,1447,1449,1459,1464,1470,1485,1517,1540,1551,1552,1559,1560,1561,1562,1563,1564,1565,1566,1568,1569,1572,1574,1575,1580,1586,1596,1605,1612,1631,1653,1654,1655,1656,1674,1685,1696,1704,1718,1734,1748,1750,1759,1765,1775,1780,1782,1786,1807,1822,1831,1832,1835,1851,1868,1885,1886,1899,1914,1915,1917,1942,1943,1952,1966,1989,1992,2007,2028,2035,2037,2055,2061,2074,2079,2087,2096,2106,2115,2121,2127,2143,2146,2147,2150,2156,2157,2168,2171,2175,2180,2188,2191,2194,2209,2220,2225,2228,2230,2233,2248,2262,2264,2277,2284,2295,2300,2316,2326,2328,2331,2341,2346,2347,2353,2365,2376,2393,2409,2420,2431,2439,2457,2460,2463,2467,2474,2477,2478,2483,2501,2508,2515,2525,2541,2556,2559,2574,2598,2625,2637,2642,2657,2673,2687,2708,2742,2743,2751,2756,2757,2765,2766,2771,2778,2781,2786,2792,2797,2799,2804,2806,2808,2810,2821,2822,2828,2830,2832,2838,2844,2853,2856,2857,2858,2869,2874,2880,2886,2892,2893,2903,2904,2912,2917,2936,2944,2951,2970,2992,3006,3018,3029,3036,3043,3057,3062,3064,3070,3076,3081,3094,3097,3117,3121,3128,3130,3135,3141,3151,3166,3182,3219,3225,3230,3231,3237,3258,3285,3311,3322,3328,3342,3361,3381,3402,3425,3433,3439,3455,3460,3473,3480,3494,3507,3508,3510,3513,3521,3522,3523,3529,3533,3545,3564,3565,3566,3576,3586,3591,3593,3604,3607,3610,3613,3614,3626,3628,3630,3642,3644,3654,3662,3675,3680,3682,3696,3702,3707,3708,3714,3727,3734,3735,3744,3748,3751,3755,3763,3772,3784,3787,3791,3795,3798,3809,3814,3821,3822,3826,3831,3841,3842,3844,3860,3862,3866,3870,3885,3889,3890,3897,3900,3902,3910,3914,3916,3918,3920,3922,3928,3931,3934,3943,3947,3958,3962,3965,3971,3980,3981,3984,3985,3988,3989,3991,3995,3999,4000,4002,4004,4011,4014,4022,4023,4025,4029,4036,4041,4043,4045,4056,4058,4073,4075,4083,4085,4086,4088,4097,4100,4103,4104,4109,4115,4117,4121,4124,4131,4133,4136,4147,4153,4161,4166,4183,4188,4202,4203,4204,4205,4206,4207,4208,4209,4210,4211,4212,4230,4231,4245,4262,4267,4275,4279,4283,4284,4285,4297,4300,4305,4309,4313,4320,4325,4328,4331,4334,4339,4341,4345,4349,4350,4351,4358,4368,4369,4370,4373,4381,4382,4387,4390,4391,4398,4399,4402,4404,4406,4417,4420,4442,4443,4444,4445,4446,4447,4448,4449,4450,4451,4452,4453,4454,4455,4457,4458,4459,4463,4464,4472,4475,4488,4493,4496,4502,4504,4505,4522,4523,4525,4528,4538,4539,4550,4552,4557,4560,4563,4566,4574,4580,4584,4591,4597,4600,4601,4602,4614,4615,4617,4618,4631,4635,4641,4648,4651,4652,4653,4663,4665,4666,4667,4669,4681,4686,4687,4690,4702,4703,4707,4709,4710,4712,4722,4726,4735,4739,4742,4743,4745,4751,4760,4766,4767,4768,4770,4771,4772,4774,4777,4778,4779,4780,4781,4782,4784,4789,4790,4796,4797,4798,4799,4800,4805,4806,4812,4816,4817,4818,4828,4831,4832,4842,4844,4845,4847,4849,4850,4854,4868,4872,4876,4880,4882,4883,4884,4885,4889,4891,4898,4901,4903,4904,4909,4914,4917,4921,4922,4929,4931,4933,4935,4936,4937,4949,4950,4957,4961,4967,4969,4976,4977,4978,4987,4988,4990,4994,4997,4999,5000,5002,5011,5015,5016,5019,5021,5023,5024,5028,5029,5030,5032,5034,5035,5041,5044,5046,5055,5057,5058,5061,5068,5077,5081,5083,5085,5087,5096,5102,5105,5111,5114,5117,5121,5123,5124,5125,5126,5128,5139,5147,5148,5150,5152,5154,5158,5159,5160,5161,5162,5163,5175,5178,5181,5182,5183,5189,5191,5194,5195,5197,5202,5203,5205,5207,5219,5224,5225,5226,5227,5234,5236,5238,5239,5240,5241,5265,5266,5275,5276,5277,5278,5283,5284,5285,5286,5287,5289,5290,5291,5292,5293,5295,5306,5309,5313,5325,5329,5331,5333,5336,5337,5338,5341,5350,5354,5358,5359,5365,5373,5374,5378,5379,5381,5383,5384,5386,5404,5418,5422,5426,5427,5429,5430,5432,5441,5450,5452,5455,5456,5462,5467,5471,5472,5474,5475,5476,5477,5480,5484,5489,5490,5491,5492,5493,5494,5496,5501,5502,5503,5507,5515,5525,5532,5536,5537,5538,5541,5542,5543,5546,5550,5561,5564,5565,5568,5578,5581,5582,5583,5584,5585,5596,5600,5603,5604,5608,5609,5611,5612,5620,5623,5629,5630,5634,5635,5638,5642,5643,5644,5645,5646,5647,5652,5660,5662,5663,5665,5669,5670,5676,5677,5681,5682,5683,5686,5689,5695,5696,5697,5701,5704,5705,5712,5720,5724,5729,5730,5736,5738,5739,5740,5755,5756,5757,5763,5764,5768,5771,5780,5784,5785,5789,5790,5799,5809,5810,5812,5814,5815,5829,5833,5851,5853,5855,5856,5861,5862,5868,5874,5875,5878,5879,5880,5881,5889,5891,5898,5900,5903,5904,5905,5912,5916,5917,5918,5920,5925,5926,5927,5929,5930,5932,5933,5937,5941,5946,5949,5954,5959,5960,5965,5971,5974,5976,5978,5983,5985,5988,6002,6005,6006,6010,6016,6018,6019,6023,6024,6031,6035,6040,6042,6043,6045,6048,6049,6051,6053,6055,6057,6058,6062,6072,6073,6074,6075,6083,6087,6088,6089,6101,6107,6108,6110,6117,6120,6121,6128,6130,6132,6133,6137,6142,6146,6151,6152,6158,6168,6171,6182,6185,6186,6192,6201,6204,6206,6213,6214,6216,6217,6220,6221,6229,6232,6241,6242,6249,6250,6253,6254,6255,6257,6261,6262,6272,6280,6281,6282,6286,6302,6303,6305,6310,6317,6323,6338,6339,6340,6356,6361,6362,6371,6376,6377,6381,6386,6387,6388,6395,6396,6399,6400,6415,6416,6417,6418,6421,6423,6433,6434,6443,6444,6447,6449,6451,6453,6463,6468,6471,6472,6473,6475,6485,6488,6491,6494,6495,6499,6509,6510,6513,6514,6518,6520,6530,6536,6537,6540,6541,6545,6548,6550,6554,6562,6564,6578,6579,6582,6583,6584,6585,6586,6588,6595,6597,6598,6600,6614,6615,6616,6618,6619,6629,6631,6632,6641,6642,6643,6647,6648,6663,6666,6674,6675,6677,6679,6681,6682,6698,6699,6705,6706,6707,6709,6715,6718,6722,6725,6726,6728,6735,6738,6739,6740,6742,6746,6748,6749,6752,6753,6759,6761,6763,6764,6769,6771,6772,6773,6775,6779,6787,6793,6799,6800,6803,6805,6806,6807,6821,6831,6832,6833,6835,6836,6842,6843,6849,6851,6853,6857,6860,6864,6868,6869,6871,6873,6877,6879,6884,6887,6891,6894,6895,6898,6900,6903,6911,6916,6918,6920,6921,6925,6938,6951,6952,6953,6957,6961,6965,6967,6968,6969,6970,6979,6982,6983,6985,6986,6990,6993,7003,7004,7006,7010,7012,7015,7016,7017,7020,7022,7024,7025,7028,7030,7032,7035,7036,7037,7039,7048,7052,7053,7057,7059,7060,7063,7070,7071,7072,7078,7081,7085,7087,7088,7089,7095,7096,7103,7104,7105,7108,7110,7111,7112,7114,7115,7116,7119,7121,7124,7125,7126,7127,7131,7132,7142,7144,7148,7149,7150,7154,7155,7156,7161,7163,7164,7166,7170,7173,7175,7177,7178,7179,7180,7183,7188,7191,7193,7194,7197,7198,7211,7218,7223,7225,7227,7229,7232,7234,7236,7237,7240,7248,7254,7261,7262,7270,7275,7276,7277,7278,7280,7281,7283,7289,7292,7293,7295,7299,7300,7306,7317,7318,7320,7323,7329,7330,7341,7346,7354,7357,7363,7364,7365,7366,7368,7371,7374,7376,7377,7382,7386,7391,7393,7394,7395,7396,7404,7407,7414,7416,7417,7418,7419,7423,7424,7436,7437,7441,7442,7449,7457,7459,7460,7461,7462,7468,7474,7476,7499,7518,7519,7522,7524,7527,7528,7534,7536,7537,7553,7554,7561,7562,7572,7576,7579,7581,7582,7584,7586,7587,7592,7593,7598,7599,7600,7601,7603,7605,7608,7623,7625,7626,7629,7630,7635,7636,7637,7639,7643,7644,7646,7647,7648,7649,7652,7653,7654,7663,7667,7669,7671,7672,7673,7674,7675,7678,7680,7682,7684,7689,7694,7695,7701,7702,7707,7714,7718,7719,7720,7737,7740,7741,7742,7744,7747,7760,7768,7769,7770,7774,7775,7778,7791,7793,7794,7796,7802,7813,7814,7816,7820,7826,7828,7830,7831,7834,7838,7845,7846,7851,7854,7855,7856,7858,7860,7861,7862,7866,7868,7872,7879,7880,7881,7882,7883,7885,7886,7889,7891,7892,7893,7894,7895,7896,7899,7900,7901,7902,7906,7914,7915,7917,7918,7920,7922,7925,7926,7927,7928,7931,7933,7935,7936,7937,7941,7944,7952,7955,7963,7966,7971,7972,7984,7985,7986,7987,7992,8013,8015,8017,8019,8020,8021,8035,8038,8040,8059,8061,8062,8063,8065,8067,8073,8095,8096,8102,8103,8104,8108,8109,8110,8111,8113,8114,8116,8117,8119,8122,8130,8131,8132,8138,8147,8151,8155,8159,8174,8175,8179,8180,8181,8186,8194,8195,8199,8202,8203,8206,8207,8209,8226,8227,8232,8233,8247,8248,8249,8251,8252,8260,8261,8264,8266,8267,8270,8274,8276,8278,8280,8282,8283,8286,8287,8288,8289,8290,8294,8295,8296,8302,8308,8309,8310,8312,8313,8314,8315,8316,8321,8323,8324,8326,8327,8334,8339,8340,8345,8346,8347,8349,8351,8352,8359,8360,8362,8364,8365,8368,8374,8375,8376,8377,8378,8379,8397,8398,8399,8400,8402,8404,8406,8407,8408,8409,8411,8413,8416,8417,8419,8420,8422,8424,8425,8427,8430,8431,8437,8438,8441,8444,8445,8446,8447,8448,8449,8450,8451,8452,8457,8460,8462,8463,8466,8467,8468,8470,8474,8476,8478,8481,8482,8484,8487,8488,8489,8490,8492,8493,8494,8498,8501,8502,8506,8508,8511,8514,8516,8517,8519,8521,8522,8523,8524,8526,8527,8533,8535,8536,8538,8541,8543,8544,8545,8546,8547,8549,8550,8553,8554,8556,8557,8560,8562,8564,8565,8566,8568,8569,8571,8575,8576,8577,8580,8581,8582,8584,8585,8586,8587,8593,8594,8596,8598,8600,8601,8605,8606,8608,8610,8612,8613,8615,8616,8617,8621,8622,8625,8630,8633,8635,8636,8637,8639,8640,8641,8642,8643,8644,8645,8647,8650,8654,8656,8658,8659,8661,8663,8665,8667,8669,8672,8673,8678,8679,8680,8681,8682,8684,8685,8686,8687,8688,8690,8692,8693,8695,8698,8700,8701,8702,8703,8704,8705,8708,8713,8716,8718,8719,8720,8723,8730,8731,8732,8734,8735,8736,8742,8745,8746,8748,8749,8750,8753,8756,8761,8762,8764,8765,8769,8770,8771,8772,8774,8777,8780,8782,8783,8784,8789,8794,8796,8798,8800,8803,8804,8805,8811,8813,8816,8819,8820,8822,8823,8824,8827,8833,8835,8836,8838,8839,8840,8841,8842,8844,8855,8857,8858,8861,8862,8864,8871,8875,8876,8879,8880,8881,8883,8884,8885,8887,8889,8890,8892,8893,8896,8900,8901,8902,8903,8904,8905,8906,8907,8909,8910,8912,8914,8915,8918,8919,8920,8925,8926,8927,8928,8934,8937,8938,8939,8941,8942,8950,8952,8954,8957,8959,8960,8961,8962,8967,8968,8969,8970,8971,8972,8973,8974,8975,8976,8977,8981,8985,8987,9002,9008,9011,9012,9013,9014,9018,9026,9029,9033,9034,9035,9041,9044,9048,9050,9052,9055,9056,9061,9062,9070,9071,9075,9076,9077,9079,9080,9081,9082,9083,9084,9085,9087,9089,9091,9100,9101,9105,9108,9109,9110,9114,9115,9116,9117,9118,9123,9126,9133,9135,9136,9137,9138,9139,9140,9142,9143,9145,9146,9147,9148,9149,9150,9153,9155,9157,9158,9159,9160,9163,9164,9167,9170,9173,9174,9176,9177,9178,9180,9184,9187,9189,9192,9194,9197,9198,9204,9205,9207,9208,9211,9212,9213,9216,9218,9219,9220,9221,9228,9230,9238,9240,9241,9242,9248,9251,9256,9257,9260,9263,9264,9273,9281,9282,9285,9286,9292,9298,9299,9302,9305,9314,9318,9319,9321,9323,9325,9326,9327,9328,9329,9330,9335,9339,9341,9343,9347,9349,9354,9355,9359,9361,9362,9363,9364,9366,9367,9368,9369,9370,9371,9372,9373,9374,9375,9376,9377,9378,9379,9383,9384,9385,9386,9388,9389,9390,9391,9392,9393,9394,9396,9397,9398,9401,9402,9403,9405,9406,9407,9408,9409,9410,9413,9415,9416,9418,9423,9425,9428,9429,9430,9434,9437,9451,9461,9469,9475,9476,9477,9494,9501,9512,9520,9526,9528,9529,9530,9531,9532,9533,9535,9536,9537,9538,9539,9540,9541,9542,9543,9544,9545,9546,9547,9548,9549,9550,9551,9552,9553,9554,9555,9556,9557,9558,9559,9560,9561,9562,9563,9564,9565,9566,9567,9568,9569,9570,9571,9572,9573,9574,9575,9576,9577,9578,9579,9580,9581,9582,9583,9584,9585,9586,9587,9594,9627,9640,9646,9654,9660,9676,9682,9686,9693,9702,9714,9726,9730,9734,9735,9736,9737,9743,9751,9783,9785,9790,9792,9799,9801,9823,9842,9858,9863,9864,9867,9875,9889,9890,9893,9897,9898,9904,9905,9919,9927,9952,9957,9960)),1,`a`.`del_status`) AS `del_status`,`a`.`online_status` AS `online_status`,if(`b`.`id`,`b`.`status`,`a`.`status`) AS `status`,`a`.`yuqi_faxi` AS `yuqi_faxi`,`a`.`order_limit` AS `order_limit`,`a`.`isPrivate` AS `isPrivate`,`a`.`allowedUids` AS `allowedUids`,`a`.`finish_rate` AS `finish_rate`,`a`.`is_jixi` AS `is_jixi`,`a`.`sort` AS `sort`,`a`.`contract_type` AS `contract_type`,`a`.`creator_id` AS `creator_id`,`a`.`created_at` AS `created_at`,`a`.`updated_at` AS `updated_at`,`a`.`isFlexRate` AS `isFlexRate`,`a`.`rateSteps` AS `rateSteps`,`a`.`issuer` AS `issuer`,`a`.`issuerSn` AS `issuerSn`,`a`.`paymentDay` AS `paymentDay`,`a`.`isTest` AS `isTest`,`a`.`filingAmount` AS `filingAmount`,`a`.`allowUseCoupon` AS `allowUseCoupon`,`a`.`tags` AS `tags`,`a`.`isLicai` AS `isLicai`,`a`.`pointsMultiple` AS `pointsMultiple`,`a`.`allowTransfer` AS `allowTransfer`,`a`.`isCustomRepayment` AS `isCustomRepayment`,`a`.`isJixiExamined` AS `isJixiExamined`,`a`.`internalTitle` AS `internalTitle`,`a`.`publishTime` AS `publishTime`,`a`.`balance_limit` AS `balance_limit`,`a`.`allowRateCoupon` AS `allowRateCoupon`,`a`.`originalBorrower` AS `originalBorrower`,`a`.`pkg_sn` AS `pkg_sn`,`a`.`isRedeemable` AS `isRedeemable`,`a`.`redemptionPeriods` AS `redemptionPeriods`,`a`.`redemptionPaymentDates` AS `redemptionPaymentDates`,`a`.`isDailyAccrual` AS `isDailyAccrual`,`a`.`flexRepay` AS `flexRepay`,`a`.`alternativeRepayer` AS `alternativeRepayer`,`a`.`borrowerRate` AS `borrowerRate`,`a`.`fundReceiver` AS `fundReceiver`,`a`.`guarantee` AS `guarantee` from (`online_product` `a` left join `online_product_vv` `b` on((`a`.`sn` = `b`.`sn`)));

-- Dumping structure for view wjf.repayment_v1
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `repayment_v1`;
CREATE ALGORITHM=UNDEFINED DEFINER=`wjf2017`@`%` SQL SECURITY DEFINER VIEW `repayment_v1` AS select `repayment`.`id` AS `id`,`repayment`.`loan_id` AS `loan_id`,`repayment`.`term` AS `term`,`repayment`.`dueDate` AS `dueDate`,`repayment`.`amount` AS `amount`,`repayment`.`principal` AS `principal`,`repayment`.`interest` AS `interest`,if((`repayment`.`loan_id` in ('id',3967,3969,3970,3979,4009,4015,4049,4072,4084,4118,4477,4485,4516,4531,4547,4558,4567,4604,4640,4695,4732,4752,4765,4803,4821,4861,4892,4913,4986,5031,5040,5053,5080,5101,5119,5165,5172,5208,5209,5230,5262,5264,5267,5274,5317,5330,5366,5391,5403,5409,5416,5421,5438,5473,5508,5524,5651,5678,5684,5691,5693,5702,5709,5710,5717,5732,5737,5750,5751,5752,5758,5759,5760,5762,5769,5774,5778,5782,5783,5787,5793,5811,5818,5828,5830,5836,5844,5850,5858,5870,5876,5883,5886,5892,5893,6020,6033,6039,6069,6070,6077,6078,6091,6100,6105,6106,6113,6115,6124,6135,6138,6144,6153,6162,6167,6180,6188,6189,6196,6197,6212,6233,6238,6258,6260,6265,6268,6271,6275,6279,6285,6295,6304,6308,6312,6319,6332,6334,6341,6345,6346,6359,6367,6370,6379,6384,6397,6407,6419,6422,6426,6431,6435,6440,6441,6442,6452,6454,6464,6478,6484,6500,6512,6529,6534,6542,6559,6576,6577,6589,6606,6610,6622,6635,6657,6659,6662,6671,6676,6680,6691,6703,6711,6721,6734,6776,6778,6801,6810,6812,6818,6829,6839,6840,6852,6865,6872,6881,6890,6897,6914,6935,6950,6963,6984,6995,6999,7026,7042,7045,7067,7074,7076,7090,7093,7100,7102,7109,7123,7138,7141,7147,7153,7157,7162,7171,7172,7176,7181,7186,7189,7192,7201,7202,7203,7206,7207,7214,7220,7221,7224,7230,7231,7238,7243,7244,7245,7256,7257,7258,7259,7260,7263,7264,7267,7268,7279,7284,7287,7294,7302,7307,7309,7316,7321,7325,7326,7336,7338,7342,7347,7353,7356,7359,7367,7372,7375,7381,7389,7398,7399,7400,7401,7402,7403,7408,7420,7422,7426,7434,7435,7447,7453,7455,7464,7471,7473,7475,7482,7487,7491,7496,7500,7512,7513,7514,7515,7516,7517,7540,7541,7556,7559,7560,7563,7564,7565,7566,7567,7580,7585,7589,7597,7604,7606,7609,7614,7621,7631,7634,7641,7642,7645,7662,7665,7677,7679,7681,7688,7690,7693,7696,7697,7704,7709,7710,7713,7715,7722,7723,7724,7727,7728,7730,7733,7734,7739,7746,7748,7752,7757,7758,7763,7765,7766,7771,7777,7779,7780,7781,7784,7787,7790,7797,7798,7799,7801,7803,7804,7805,7809,7811,7818,7821,7825,7827,7833,7840,7841,7897,7949,7950,7954,7961,7968,7969,7970,7974,7976,7981,7989,7994,7996,7998,8004,8008,8009,8010,8012,8023,8025,8029,8030,8032,8036,8041,8049,8050,8051,8052,8055,8056,8057,8058,8082,8083,8084,8085,8086,8087,8088,8089,8090,8091,8092,8093,8105,8106,8107,8112,8121,8126,8127,8128,8129,8133,8141,8143,8145,8150,8153,8161,8164,8166,8177,8178,8184,8187,8189,8192,8193,8196,8201,8204,8208,8210,8211,8214,8215,8216,8221,8224,8229,8230,8231,8234,8235,8239,8241,8243,8245,8253,8255,8256,8257,8258,8263,8268,8269,8273,8277,8279,8281,8285,8291,8292,8297,8300,8301,8304,8319,8322,8325,8328,8331,8335,8337,8338,8342,8343,8344,8348,8350,8353,8355,8356,8358,8366,8367,8370,8372,8373,8381,8383,8386,8389,8394,8405,8410,8412,8415,8423,8429,8435,8436,8439,8440,8443,8456,8458,8469,8471,8472,8473,8485,8495,8497,8499,8505,8518,8528,8530,8532,8539,8548,8570,8578,8589,8595,8603,8604,8611,8618,8632,8634,8652,8660,8674,8675,8676,8694,8706,8710,8711,8712,8714,8717,8721,8722,8725,8726,8727,8729,8737,8738,8739,8741,8747,8751,8754,8757,8759,8760,8767,8768,8776,8778,8779,8787,8791,8793,8797,8799,8806,8808,8812,8814,8818,8825,8828,8831,8832,8847,8848,8850,8852,8854,8859,8863,8865,8873,8874,8877,8882,8886,8888,8891,8894,8895,8897,8898,8899,8908,8911,8913,8916,8917,8921,8922,8923,8924,8929,8930,8931,8932,8933,8935,8936,8940,8943,8944,8945,8946,8947,8948,8949,8951,8953,8955,8956,8958,8963,8964,8965,8966,8978,8979,8980,8982,8983,8984,8986,8988,8989,8990,8991,8992,8993,8994,8995,8996,8997,8998,8999,9000,9001,9003,9004,9005,9006,9007,9009,9010,9015,9016,9017,9019,9020,9022,9023,9024,9025,9027,9028,9030,9031,9032,9036,9037,9039,9040,9042,9043,9045,9046,9047,9049,9051,9053,9054,9057,9059,9060,9063,9064,9065,9066,9068,9069,9072,9073,9074,9078,9086,9088,9090,9092,9093,9094,9095,9096,9097,9098,9099,9102,9103,9104,9106,9107,9111,9112,9113,9119,9121,9122,9124,9125,9127,9128,9129,9130,9131,9132,9134,9141,9144,9151,9152,9154,9156,9161,9162,9165,9166,9168,9169,9171,9172,9175,9179,9181,9182,9183,9185,9186,9188,9190,9191,9195,9196,9199,9200,9201,9202,9203,9206,9209,9210,9214,9215,9217,9223,9224,9225,9226,9227,9229,9231,9232,9233,9234,9235,9236,9237,9239,9243,9244,9245,9246,9247,9249,9250,9252,9253,9254,9255,9258,9261,9262,9265,9266,9267,9268,9269,9270,9271,9272,9274,9275,9276,9277,9278,9280,9283,9284,9287,9288,9289,9290,9293,9294,9296,9297,9300,9301,9303,9304,9306,9307,9308,9309,9310,9311,9312,9313,9315,9317,9320,9322,9324,9331,9332,9333,9334,9336,9337,9338,9340,9342,9345,9346,9348,9350,9351,9356,9358,9387)),1,0) AS `isRepaid`,if((`repayment`.`loan_id` in ('id',3967,3969,3970,3979,4009,4015,4049,4072,4084,4118,4477,4485,4516,4531,4547,4558,4567,4604,4640,4695,4732,4752,4765,4803,4821,4861,4892,4913,4986,5031,5040,5053,5080,5101,5119,5165,5172,5208,5209,5230,5262,5264,5267,5274,5317,5330,5366,5391,5403,5409,5416,5421,5438,5473,5508,5524,5651,5678,5684,5691,5693,5702,5709,5710,5717,5732,5737,5750,5751,5752,5758,5759,5760,5762,5769,5774,5778,5782,5783,5787,5793,5811,5818,5828,5830,5836,5844,5850,5858,5870,5876,5883,5886,5892,5893,6020,6033,6039,6069,6070,6077,6078,6091,6100,6105,6106,6113,6115,6124,6135,6138,6144,6153,6162,6167,6180,6188,6189,6196,6197,6212,6233,6238,6258,6260,6265,6268,6271,6275,6279,6285,6295,6304,6308,6312,6319,6332,6334,6341,6345,6346,6359,6367,6370,6379,6384,6397,6407,6419,6422,6426,6431,6435,6440,6441,6442,6452,6454,6464,6478,6484,6500,6512,6529,6534,6542,6559,6576,6577,6589,6606,6610,6622,6635,6657,6659,6662,6671,6676,6680,6691,6703,6711,6721,6734,6776,6778,6801,6810,6812,6818,6829,6839,6840,6852,6865,6872,6881,6890,6897,6914,6935,6950,6963,6984,6995,6999,7026,7042,7045,7067,7074,7076,7090,7093,7100,7102,7109,7123,7138,7141,7147,7153,7157,7162,7171,7172,7176,7181,7186,7189,7192,7201,7202,7203,7206,7207,7214,7220,7221,7224,7230,7231,7238,7243,7244,7245,7256,7257,7258,7259,7260,7263,7264,7267,7268,7279,7284,7287,7294,7302,7307,7309,7316,7321,7325,7326,7336,7338,7342,7347,7353,7356,7359,7367,7372,7375,7381,7389,7398,7399,7400,7401,7402,7403,7408,7420,7422,7426,7434,7435,7447,7453,7455,7464,7471,7473,7475,7482,7487,7491,7496,7500,7512,7513,7514,7515,7516,7517,7540,7541,7556,7559,7560,7563,7564,7565,7566,7567,7580,7585,7589,7597,7604,7606,7609,7614,7621,7631,7634,7641,7642,7645,7662,7665,7677,7679,7681,7688,7690,7693,7696,7697,7704,7709,7710,7713,7715,7722,7723,7724,7727,7728,7730,7733,7734,7739,7746,7748,7752,7757,7758,7763,7765,7766,7771,7777,7779,7780,7781,7784,7787,7790,7797,7798,7799,7801,7803,7804,7805,7809,7811,7818,7821,7825,7827,7833,7840,7841,7897,7949,7950,7954,7961,7968,7969,7970,7974,7976,7981,7989,7994,7996,7998,8004,8008,8009,8010,8012,8023,8025,8029,8030,8032,8036,8041,8049,8050,8051,8052,8055,8056,8057,8058,8082,8083,8084,8085,8086,8087,8088,8089,8090,8091,8092,8093,8105,8106,8107,8112,8121,8126,8127,8128,8129,8133,8141,8143,8145,8150,8153,8161,8164,8166,8177,8178,8184,8187,8189,8192,8193,8196,8201,8204,8208,8210,8211,8214,8215,8216,8221,8224,8229,8230,8231,8234,8235,8239,8241,8243,8245,8253,8255,8256,8257,8258,8263,8268,8269,8273,8277,8279,8281,8285,8291,8292,8297,8300,8301,8304,8319,8322,8325,8328,8331,8335,8337,8338,8342,8343,8344,8348,8350,8353,8355,8356,8358,8366,8367,8370,8372,8373,8381,8383,8386,8389,8394,8405,8410,8412,8415,8423,8429,8435,8436,8439,8440,8443,8456,8458,8469,8471,8472,8473,8485,8495,8497,8499,8505,8518,8528,8530,8532,8539,8548,8570,8578,8589,8595,8603,8604,8611,8618,8632,8634,8652,8660,8674,8675,8676,8694,8706,8710,8711,8712,8714,8717,8721,8722,8725,8726,8727,8729,8737,8738,8739,8741,8747,8751,8754,8757,8759,8760,8767,8768,8776,8778,8779,8787,8791,8793,8797,8799,8806,8808,8812,8814,8818,8825,8828,8831,8832,8847,8848,8850,8852,8854,8859,8863,8865,8873,8874,8877,8882,8886,8888,8891,8894,8895,8897,8898,8899,8908,8911,8913,8916,8917,8921,8922,8923,8924,8929,8930,8931,8932,8933,8935,8936,8940,8943,8944,8945,8946,8947,8948,8949,8951,8953,8955,8956,8958,8963,8964,8965,8966,8978,8979,8980,8982,8983,8984,8986,8988,8989,8990,8991,8992,8993,8994,8995,8996,8997,8998,8999,9000,9001,9003,9004,9005,9006,9007,9009,9010,9015,9016,9017,9019,9020,9022,9023,9024,9025,9027,9028,9030,9031,9032,9036,9037,9039,9040,9042,9043,9045,9046,9047,9049,9051,9053,9054,9057,9059,9060,9063,9064,9065,9066,9068,9069,9072,9073,9074,9078,9086,9088,9090,9092,9093,9094,9095,9096,9097,9098,9099,9102,9103,9104,9106,9107,9111,9112,9113,9119,9121,9122,9124,9125,9127,9128,9129,9130,9131,9132,9134,9141,9144,9151,9152,9154,9156,9161,9162,9165,9166,9168,9169,9171,9172,9175,9179,9181,9182,9183,9185,9186,9188,9190,9191,9195,9196,9199,9200,9201,9202,9203,9206,9209,9210,9214,9215,9217,9223,9224,9225,9226,9227,9229,9231,9232,9233,9234,9235,9236,9237,9239,9243,9244,9245,9246,9247,9249,9250,9252,9253,9254,9255,9258,9261,9262,9265,9266,9267,9268,9269,9270,9271,9272,9274,9275,9276,9277,9278,9280,9283,9284,9287,9288,9289,9290,9293,9294,9296,9297,9300,9301,9303,9304,9306,9307,9308,9309,9310,9311,9312,9313,9315,9317,9320,9322,9324,9331,9332,9333,9334,9336,9337,9338,9340,9342,9345,9346,9348,9350,9351,9356,9358,9387)),('2018-04-01' + interval (`repayment`.`id` % 80) day),NULL) AS `repaidAt`,if((`repayment`.`loan_id` in (3967,3969,3970,3979,4009,4015,4049,4072,4084,4118,4477,4485,4516,4531,4547,4558,4567,4604,4640,4695,4732,4752,4765,4803,4821,5508,5524,6020,6033,6039,6069,6070,6077,6078,6091,6100,6105,6106,6113,6115,6124,6135,6138,6144,6153,6162,6167,6180,6188,6189,6196,6197,6212,6233,6238,6258,6260,6265,6268,6271,6275,6279,6285,6295,6304,6308,6312,6319,6332,6334,6341,6345,6346,6359,6367,6370,6379,6384,6397,6407,6419,6422,6426,6431,6435,6440,6441,6442,6452,6454,6464,6478,6484,6500,6512,6529,6534,6542,6559,6576,6577,6589,6606,6610,6622,6635,6657,6659,6662,6671,6676,6680,6691,6703,6711,6721,6734,6776,6778,6801,6810,6812,6818,6829,6839,6840,6852,6865,6872,6881,6890,6897,6914,6935,6950,6963,6984,6995,6999,7026,7042,7045,7067,7074,7076,7090,7093,7100,7102,7176,7181,7203,7206,7220,7221,7230,7243,7257,7258,7259,7260,7263,7264,7267,7302,7307,7316,7321,7326,7336,7342,7353,7359,7372,7375,7389,7398,7399,7400,7401,7402,7420,7434,7453,7487,7500,7512,7513,7556,7563,7564,7565,7566,7567,7585,7606,7609,7634,7642,7645,7662,7677,7679,7681,7688,7693,7696,7704,7710,7713,7722,7723,7724,7727,7733,7746,7748,7758,7763,7765,7777,7779,7780,7781,7787,7790,7797,7801,7803,7809,7821,7825,7827,7833,7840,7841,7897,7949,7950,7954,7961,7968,7969,7974,7994,7996,7998,8008,8009,8010,8025,8029,8030,8041,8049,8050,8051,8052,8055,8056,8082,8083,8084,8085,8086,8087,8105,8106,8107,8126,8127,8133,8141,8143,8145,8153,8184,8187,8189,8192,8193,8208,8210,8211,8214,8224,8229,8231,8234,8241,8253,8255,8256,8268,8269,8277,8279,8281,8285,8291,8292,8300,8301,8325,8328,8331,8337,8343,8348,8353,8358,8366,8370,8372,8386,8389,8394,8410,8423,8429,8435,8439,8440,8456,8458,8469,8472,8473,8485,8495,8497,8505,8518,8528,8532,8548,8570,8595,8603,8611,8634,8652,8674,8694,8706,8710,8711,8712,8717,8721,8722,8725,8726,8729,8737,8738,8739,8741,8747,8754,8759,8760,8767,8768,8776,8779,8787,8791,8793,8797,8799,8806,8808,8812,8818,8825,8831,8832,8847,8848,8852,8854,8859,8863,8865,8874,8882,8888,8891,8894,8898,8899,8908,8911,8913,8916,8917,8921,8922,8923,8924,8930,8932,8933,8936,8940,8943,8944,8945,8946,8947,8948,8949,8951,8953,8956,8958,8963,8966,8978,8979,8980,8982,8983,8984,8986,8988,8989,8990,8991,8992,8993,8995,8996,8997,8999,9000,9001,9003,9004,9005,9006,9007,9009,9015,9016,9017,9019,9022,9023,9024,9025,9027,9028,9030,9031,9032,9037,9039,9040,9043,9045,9046,9047,9049,9051,9053,9054,9059,9060,9063,9065,9069,9072,9073,9074,9078,9088,9093,9094,9096,9097,9098,9099,9102,9104,9106,9107,9112,9113,9119,9125,9127,9128,9129,9130,9131,9132,9134,9144,9152,9154,9156,9161,9165,9169,9171,9175,9179,9181,9182,9183,9186,9188,9190,9196,9199,9200,9202,9203,9209,9210,9214,9217,9223,9224,9225,9226,9229,9231,9232,9233,9234,9235,9236,9239,9243,9244,9246,9247,9249,9250,9252,9253,9255,9258,9261,9262,9265,9267,9268,9269,9270,9272,9274,9276,9277,9278,9280,9283,9284,9287,9288,9289,9293,9294,9296,9297,9300,9301,9303,9306,9308,9309,9310,9311,9313,9315,9317,9320,9331,9333,9334,9336,9337,9340,9342,9346,9348,9351,9356,9358,9387)),1,0) AS `isRefunded`,if((`repayment`.`loan_id` in ('id',3967,3969,3970,3979,4009,4015,4049,4072,4084,4118,4477,4485,4516,4531,4547,4558,4567,4604,4640,4695,4732,4752,4765,4803,4821,4861,4892,4913,4986,5031,5040,5053,5080,5101,5119,5165,5172,5208,5209,5230,5262,5264,5267,5274,5317,5330,5366,5391,5403,5409,5416,5421,5438,5473,5508,5524,5651,5678,5684,5691,5693,5702,5709,5710,5717,5732,5737,5750,5751,5752,5758,5759,5760,5762,5769,5774,5778,5782,5783,5787,5793,5811,5818,5828,5830,5836,5844,5850,5858,5870,5876,5883,5886,5892,5893,6020,6033,6039,6069,6070,6077,6078,6091,6100,6105,6106,6113,6115,6124,6135,6138,6144,6153,6162,6167,6180,6188,6189,6196,6197,6212,6233,6238,6258,6260,6265,6268,6271,6275,6279,6285,6295,6304,6308,6312,6319,6332,6334,6341,6345,6346,6359,6367,6370,6379,6384,6397,6407,6419,6422,6426,6431,6435,6440,6441,6442,6452,6454,6464,6478,6484,6500,6512,6529,6534,6542,6559,6576,6577,6589,6606,6610,6622,6635,6657,6659,6662,6671,6676,6680,6691,6703,6711,6721,6734,6776,6778,6801,6810,6812,6818,6829,6839,6840,6852,6865,6872,6881,6890,6897,6914,6935,6950,6963,6984,6995,6999,7026,7042,7045,7067,7074,7076,7090,7093,7100,7102,7109,7123,7138,7141,7147,7153,7157,7162,7171,7172,7176,7181,7186,7189,7192,7201,7202,7203,7206,7207,7214,7220,7221,7224,7230,7231,7238,7243,7244,7245,7256,7257,7258,7259,7260,7263,7264,7267,7268,7279,7284,7287,7294,7302,7307,7309,7316,7321,7325,7326,7336,7338,7342,7347,7353,7356,7359,7367,7372,7375,7381,7389,7398,7399,7400,7401,7402,7403,7408,7420,7422,7426,7434,7435,7447,7453,7455,7464,7471,7473,7475,7482,7487,7491,7496,7500,7512,7513,7514,7515,7516,7517,7540,7541,7556,7559,7560,7563,7564,7565,7566,7567,7580,7585,7589,7597,7604,7606,7609,7614,7621,7631,7634,7641,7642,7645,7662,7665,7677,7679,7681,7688,7690,7693,7696,7697,7704,7709,7710,7713,7715,7722,7723,7724,7727,7728,7730,7733,7734,7739,7746,7748,7752,7757,7758,7763,7765,7766,7771,7777,7779,7780,7781,7784,7787,7790,7797,7798,7799,7801,7803,7804,7805,7809,7811,7818,7821,7825,7827,7833,7840,7841,7897,7949,7950,7954,7961,7968,7969,7970,7974,7976,7981,7989,7994,7996,7998,8004,8008,8009,8010,8012,8023,8025,8029,8030,8032,8036,8041,8049,8050,8051,8052,8055,8056,8057,8058,8082,8083,8084,8085,8086,8087,8088,8089,8090,8091,8092,8093,8105,8106,8107,8112,8121,8126,8127,8128,8129,8133,8141,8143,8145,8150,8153,8161,8164,8166,8177,8178,8184,8187,8189,8192,8193,8196,8201,8204,8208,8210,8211,8214,8215,8216,8221,8224,8229,8230,8231,8234,8235,8239,8241,8243,8245,8253,8255,8256,8257,8258,8263,8268,8269,8273,8277,8279,8281,8285,8291,8292,8297,8300,8301,8304,8319,8322,8325,8328,8331,8335,8337,8338,8342,8343,8344,8348,8350,8353,8355,8356,8358,8366,8367,8370,8372,8373,8381,8383,8386,8389,8394,8405,8410,8412,8415,8423,8429,8435,8436,8439,8440,8443,8456,8458,8469,8471,8472,8473,8485,8495,8497,8499,8505,8518,8528,8530,8532,8539,8548,8570,8578,8589,8595,8603,8604,8611,8618,8632,8634,8652,8660,8674,8675,8676,8694,8706,8710,8711,8712,8714,8717,8721,8722,8725,8726,8727,8729,8737,8738,8739,8741,8747,8751,8754,8757,8759,8760,8767,8768,8776,8778,8779,8787,8791,8793,8797,8799,8806,8808,8812,8814,8818,8825,8828,8831,8832,8847,8848,8850,8852,8854,8859,8863,8865,8873,8874,8877,8882,8886,8888,8891,8894,8895,8897,8898,8899,8908,8911,8913,8916,8917,8921,8922,8923,8924,8929,8930,8931,8932,8933,8935,8936,8940,8943,8944,8945,8946,8947,8948,8949,8951,8953,8955,8956,8958,8963,8964,8965,8966,8978,8979,8980,8982,8983,8984,8986,8988,8989,8990,8991,8992,8993,8994,8995,8996,8997,8998,8999,9000,9001,9003,9004,9005,9006,9007,9009,9010,9015,9016,9017,9019,9020,9022,9023,9024,9025,9027,9028,9030,9031,9032,9036,9037,9039,9040,9042,9043,9045,9046,9047,9049,9051,9053,9054,9057,9059,9060,9063,9064,9065,9066,9068,9069,9072,9073,9074,9078,9086,9088,9090,9092,9093,9094,9095,9096,9097,9098,9099,9102,9103,9104,9106,9107,9111,9112,9113,9119,9121,9122,9124,9125,9127,9128,9129,9130,9131,9132,9134,9141,9144,9151,9152,9154,9156,9161,9162,9165,9166,9168,9169,9171,9172,9175,9179,9181,9182,9183,9185,9186,9188,9190,9191,9195,9196,9199,9200,9201,9202,9203,9206,9209,9210,9214,9215,9217,9223,9224,9225,9226,9227,9229,9231,9232,9233,9234,9235,9236,9237,9239,9243,9244,9245,9246,9247,9249,9250,9252,9253,9254,9255,9258,9261,9262,9265,9266,9267,9268,9269,9270,9271,9272,9274,9275,9276,9277,9278,9280,9283,9284,9287,9288,9289,9290,9293,9294,9296,9297,9300,9301,9303,9304,9306,9307,9308,9309,9310,9311,9312,9313,9315,9317,9320,9322,9324,9331,9332,9333,9334,9336,9337,9338,9340,9342,9345,9346,9348,9350,9351,9356,9358,9387)),('2018-04-01' + interval (`repayment`.`id` % 80) day),NULL) AS `refundedAt` from `repayment`;

-- Dumping structure for view wjf.repayment_v2
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `repayment_v2`;
CREATE ALGORITHM=UNDEFINED DEFINER=`wjf2017`@`%` SQL SECURITY DEFINER VIEW `repayment_v2` AS select `a`.`id` AS `id`,`a`.`loan_id` AS `loan_id`,`a`.`term` AS `term`,`a`.`dueDate` AS `dueDate`,`a`.`amount` AS `amount`,`a`.`principal` AS `principal`,`a`.`interest` AS `interest`,if(`b`.`id`,`b`.`isRepaid`,`a`.`isRepaid`) AS `isRepaid`,if(`b`.`id`,`b`.`repaidAt`,`a`.`repaidAt`) AS `repaidAt`,if(`b`.`id`,`b`.`isRefunded`,`a`.`isRefunded`) AS `isRefunded`,if(`b`.`id`,`b`.`refundedAt`,`a`.`refundedAt`) AS `refundedAt` from (`repayment` `a` left join `repayment_vv` `b` on((`a`.`id` = `b`.`id`)));

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
