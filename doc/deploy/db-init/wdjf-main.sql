-- MySQL dump 10.13  Distrib 5.7.22, for Linux (x86_64)
--
-- Host: localhost    Database: wdjf_main
-- ------------------------------------------------------
-- Server version	5.7.22-0ubuntu0.16.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `accesstoken`
--

DROP TABLE IF EXISTS `accesstoken`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `accesstoken` (
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `accesstoken`
--

LOCK TABLES `accesstoken` WRITE;
/*!40000 ALTER TABLE `accesstoken` DISABLE KEYS */;
/*!40000 ALTER TABLE `accesstoken` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin`
--

DROP TABLE IF EXISTS `admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='管理员用户表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin`
--

LOCK TABLES `admin` WRITE;
/*!40000 ALTER TABLE `admin` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_auth`
--

DROP TABLE IF EXISTS `admin_auth`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_auth` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `admin_id` int(4) NOT NULL COMMENT '管理者id',
  `role_sn` char(24) DEFAULT '' COMMENT '角色sn',
  `auth_sn` char(24) DEFAULT '' COMMENT '权限sn',
  `auth_name` varchar(30) NOT NULL COMMENT '权限名称',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态0冻结，1激活',
  `updated_at` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_auth`
--

LOCK TABLES `admin_auth` WRITE;
/*!40000 ALTER TABLE `admin_auth` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_auth` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_log`
--

DROP TABLE IF EXISTS `admin_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_log` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_log`
--

LOCK TABLES `admin_log` WRITE;
/*!40000 ALTER TABLE `admin_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_op_log`
--

DROP TABLE IF EXISTS `admin_op_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_op_log` (
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_op_log`
--

LOCK TABLES `admin_op_log` WRITE;
/*!40000 ALTER TABLE `admin_op_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_op_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `admin_upload`
--

DROP TABLE IF EXISTS `admin_upload`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_upload` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(30) NOT NULL,
  `link` varchar(100) NOT NULL,
  `allowHtml` smallint(1) DEFAULT '0',
  `isDeleted` smallint(1) DEFAULT '0',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `admin_upload`
--

LOCK TABLES `admin_upload` WRITE;
/*!40000 ALTER TABLE `admin_upload` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_upload` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `adv`
--

DROP TABLE IF EXISTS `adv`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `adv` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='广告表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `adv`
--

LOCK TABLES `adv` WRITE;
/*!40000 ALTER TABLE `adv` DISABLE KEYS */;
/*!40000 ALTER TABLE `adv` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `affiliate_campaign`
--

DROP TABLE IF EXISTS `affiliate_campaign`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `affiliate_campaign` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `trackCode` varchar(255) DEFAULT NULL,
  `affiliator_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `trackCode` (`trackCode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `affiliate_campaign`
--

LOCK TABLES `affiliate_campaign` WRITE;
/*!40000 ALTER TABLE `affiliate_campaign` DISABLE KEYS */;
/*!40000 ALTER TABLE `affiliate_campaign` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `affiliator`
--

DROP TABLE IF EXISTS `affiliator`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `affiliator` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `affiliator`
--

LOCK TABLES `affiliator` WRITE;
/*!40000 ALTER TABLE `affiliator` DISABLE KEYS */;
/*!40000 ALTER TABLE `affiliator` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `annual_report`
--

DROP TABLE IF EXISTS `annual_report`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `annual_report` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `totalProfit` decimal(14,2) DEFAULT '0.00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `annual_report`
--

LOCK TABLES `annual_report` WRITE;
/*!40000 ALTER TABLE `annual_report` DISABLE KEYS */;
/*!40000 ALTER TABLE `annual_report` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `app_meta`
--

DROP TABLE IF EXISTS `app_meta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `app_meta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `key` varchar(64) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `app_meta`
--

LOCK TABLES `app_meta` WRITE;
/*!40000 ALTER TABLE `app_meta` DISABLE KEYS */;
/*!40000 ALTER TABLE `app_meta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `appliament`
--

DROP TABLE IF EXISTS `appliament`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appliament` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userID` int(11) unsigned NOT NULL COMMENT '用户ID',
  `appointmentTime` int(11) unsigned NOT NULL COMMENT '预约时间',
  `appointmentAward` decimal(14,2) unsigned NOT NULL COMMENT '预约金额',
  `appointmentObjectId` smallint(6) unsigned NOT NULL COMMENT '预约类型',
  `appointmentAwardType` smallint(6) unsigned NOT NULL COMMENT '获奖类型，1：喜卡，2：加息券',
  PRIMARY KEY (`id`),
  KEY `userId` (`userID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `appliament`
--

LOCK TABLES `appliament` WRITE;
/*!40000 ALTER TABLE `appliament` DISABLE KEYS */;
/*!40000 ALTER TABLE `appliament` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `asset`
--

DROP TABLE IF EXISTS `asset`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `asset` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asset`
--

LOCK TABLES `asset` WRITE;
/*!40000 ALTER TABLE `asset` DISABLE KEYS */;
/*!40000 ALTER TABLE `asset` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `auth`
--

DROP TABLE IF EXISTS `auth`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `auth` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auth`
--

LOCK TABLES `auth` WRITE;
/*!40000 ALTER TABLE `auth` DISABLE KEYS */;
/*!40000 ALTER TABLE `auth` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `award`
--

DROP TABLE IF EXISTS `award`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `award` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `award`
--

LOCK TABLES `award` WRITE;
/*!40000 ALTER TABLE `award` DISABLE KEYS */;
/*!40000 ALTER TABLE `award` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bank`
--

DROP TABLE IF EXISTS `bank`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bank` (
  `id` int(1) NOT NULL AUTO_INCREMENT,
  `bankName` varchar(100) NOT NULL,
  `gateId` varchar(50) NOT NULL COMMENT '银行英文简称',
  PRIMARY KEY (`id`),
  UNIQUE KEY `gateId` (`gateId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bank`
--

LOCK TABLES `bank` WRITE;
/*!40000 ALTER TABLE `bank` DISABLE KEYS */;
/*!40000 ALTER TABLE `bank` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bank_card_update`
--

DROP TABLE IF EXISTS `bank_card_update`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bank_card_update` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bank_card_update`
--

LOCK TABLES `bank_card_update` WRITE;
/*!40000 ALTER TABLE `bank_card_update` DISABLE KEYS */;
/*!40000 ALTER TABLE `bank_card_update` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bankcardbin`
--

DROP TABLE IF EXISTS `bankcardbin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bankcardbin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cardBin` varchar(50) DEFAULT NULL COMMENT '卡号唯一判断数字',
  `cardType` varchar(20) NOT NULL COMMENT '卡类型',
  `bankId` int(11) DEFAULT NULL COMMENT '银行id',
  `binDigits` int(11) DEFAULT NULL COMMENT '判断长度',
  `cardDigits` int(11) NOT NULL COMMENT '卡长度',
  PRIMARY KEY (`id`),
  UNIQUE KEY `card_bin` (`cardBin`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bankcardbin`
--

LOCK TABLES `bankcardbin` WRITE;
/*!40000 ALTER TABLE `bankcardbin` DISABLE KEYS */;
/*!40000 ALTER TABLE `bankcardbin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bao_quan_queue`
--

DROP TABLE IF EXISTS `bao_quan_queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bao_quan_queue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `itemId` int(11) DEFAULT NULL,
  `status` int(1) DEFAULT '1',
  `created_at` int(10) DEFAULT NULL,
  `updated_at` int(10) DEFAULT NULL,
  `itemType` varchar(20) DEFAULT 'loan',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_item` (`itemId`,`itemType`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bao_quan_queue`
--

LOCK TABLES `bao_quan_queue` WRITE;
/*!40000 ALTER TABLE `bao_quan_queue` DISABLE KEYS */;
/*!40000 ALTER TABLE `bao_quan_queue` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `booking_log`
--

DROP TABLE IF EXISTS `booking_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `booking_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `uid` int(10) NOT NULL COMMENT '用户ID',
  `pid` int(10) NOT NULL COMMENT '项目ID',
  `name` varchar(50) NOT NULL COMMENT '姓名',
  `mobile` char(11) NOT NULL COMMENT '手机号',
  `fund` int(10) NOT NULL COMMENT '预约金额',
  `created_at` int(10) NOT NULL COMMENT '创建时间',
  `updated_at` int(10) NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='预约记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `booking_log`
--

LOCK TABLES `booking_log` WRITE;
/*!40000 ALTER TABLE `booking_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `booking_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `booking_product`
--

DROP TABLE IF EXISTS `booking_product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `booking_product` (
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `booking_product`
--

LOCK TABLES `booking_product` WRITE;
/*!40000 ALTER TABLE `booking_product` DISABLE KEYS */;
/*!40000 ALTER TABLE `booking_product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `borrower`
--

DROP TABLE IF EXISTS `borrower`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `borrower` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL DEFAULT '0' COMMENT '融资会员ID',
  `allowDisbursement` tinyint(1) NOT NULL DEFAULT '0' COMMENT '能否作为放款方',
  `type` smallint(6) NOT NULL DEFAULT '1' COMMENT '会员类型 1企业融资方 2个人融资方 3用款方 4代偿方 5担保方',
  `created_at` int(11) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updated_at` int(11) NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `borrower`
--

LOCK TABLES `borrower` WRITE;
/*!40000 ALTER TABLE `borrower` DISABLE KEYS */;
/*!40000 ALTER TABLE `borrower` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_entry`
--

DROP TABLE IF EXISTS `cache_entry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_entry` (
  `id` char(128) NOT NULL DEFAULT '',
  `expire` int(11) DEFAULT NULL,
  `data` blob,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_entry`
--

LOCK TABLES `cache_entry` WRITE;
/*!40000 ALTER TABLE `cache_entry` DISABLE KEYS */;
INSERT INTO `cache_entry` VALUES ('4e6ba1ecc9cf577fd6666d4eb7d2c062',1531893823,'a:2:{i:0;a:4:{s:16:\"totalTradeAmount\";s:4:\"0.00\";s:17:\"totalRefundAmount\";s:4:\"0.00\";s:19:\"totalRefundInterest\";s:4:\"0.00\";s:17:\"totalCharityAount\";s:4:\"0.00\";}i:1;N;}');
/*!40000 ALTER TABLE `cache_entry` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `callout`
--

DROP TABLE IF EXISTS `callout`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `callout` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT '召集人ID',
  `endTime` datetime DEFAULT NULL COMMENT '召集截止时间',
  `responderCount` int(11) DEFAULT '0' COMMENT '响应人数',
  `promo_id` int(11) DEFAULT NULL COMMENT '参与活动ID',
  `createTime` datetime DEFAULT NULL COMMENT '创建时间',
  `callerOpenId` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_key` (`promo_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `callout`
--

LOCK TABLES `callout` WRITE;
/*!40000 ALTER TABLE `callout` DISABLE KEYS */;
/*!40000 ALTER TABLE `callout` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `callout_responder`
--

DROP TABLE IF EXISTS `callout_responder`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `callout_responder` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `openid` varchar(64) DEFAULT NULL COMMENT '用户开放身份标识',
  `callout_id` int(11) NOT NULL COMMENT '召集ID',
  `ip` varchar(15) DEFAULT NULL COMMENT '响应人IP',
  `createTime` datetime DEFAULT NULL COMMENT '创建时间',
  `promo_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `callout_responder`
--

LOCK TABLES `callout_responder` WRITE;
/*!40000 ALTER TABLE `callout_responder` DISABLE KEYS */;
/*!40000 ALTER TABLE `callout_responder` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cancelorder`
--

DROP TABLE IF EXISTS `cancelorder`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cancelorder` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `orderSn` varchar(30) NOT NULL,
  `txSn` varchar(30) NOT NULL,
  `money` decimal(14,2) NOT NULL COMMENT '返还金额。负数含义',
  `txStatus` tinyint(1) NOT NULL COMMENT '0初始,1处理中,2成功,3失败',
  `created_at` int(10) NOT NULL,
  `updated_at` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='取消订单表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cancelorder`
--

LOCK TABLES `cancelorder` WRITE;
/*!40000 ALTER TABLE `cancelorder` DISABLE KEYS */;
/*!40000 ALTER TABLE `cancelorder` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `captcha`
--

DROP TABLE IF EXISTS `captcha`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `captcha` (
  `id` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `createTime` datetime NOT NULL,
  `expireTime` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `captcha`
--

LOCK TABLES `captcha` WRITE;
/*!40000 ALTER TABLE `captcha` DISABLE KEYS */;
/*!40000 ALTER TABLE `captcha` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `category` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='公共分类表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `category`
--

LOCK TABLES `category` WRITE;
/*!40000 ALTER TABLE `category` DISABLE KEYS */;
/*!40000 ALTER TABLE `category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `channel`
--

DROP TABLE IF EXISTS `channel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `channel` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `thirdPartyUser_id` varchar(255) NOT NULL,
  `createTime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `userId` (`userId`),
  UNIQUE KEY `thirdPartyUser_id` (`thirdPartyUser_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `channel`
--

LOCK TABLES `channel` WRITE;
/*!40000 ALTER TABLE `channel` DISABLE KEYS */;
/*!40000 ALTER TABLE `channel` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `check_in`
--

DROP TABLE IF EXISTS `check_in`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `check_in` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `checkDate` date NOT NULL,
  `lastCheckDate` date DEFAULT NULL,
  `streak` int(11) DEFAULT NULL,
  `createTime` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_date` (`user_id`,`checkDate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `check_in`
--

LOCK TABLES `check_in` WRITE;
/*!40000 ALTER TABLE `check_in` DISABLE KEYS */;
/*!40000 ALTER TABLE `check_in` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `code`
--

DROP TABLE IF EXISTS `code`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `code` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `code`
--

LOCK TABLES `code` WRITE;
/*!40000 ALTER TABLE `code` DISABLE KEYS */;
/*!40000 ALTER TABLE `code` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coins_record`
--

DROP TABLE IF EXISTS `coins_record`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `coins_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `incrCoins` int(11) NOT NULL,
  `finalCoins` int(11) NOT NULL,
  `createTime` datetime NOT NULL,
  `isOffline` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coins_record`
--

LOCK TABLES `coins_record` WRITE;
/*!40000 ALTER TABLE `coins_record` DISABLE KEYS */;
/*!40000 ALTER TABLE `coins_record` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contract`
--

DROP TABLE IF EXISTS `contract`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contract` (
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contract`
--

LOCK TABLES `contract` WRITE;
/*!40000 ALTER TABLE `contract` DISABLE KEYS */;
/*!40000 ALTER TABLE `contract` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contract_template`
--

DROP TABLE IF EXISTS `contract_template`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contract_template` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `type` int(1) DEFAULT '0' COMMENT '0固定1特殊模板',
  `pid` int(10) DEFAULT NULL COMMENT '产品id',
  `name` char(30) DEFAULT NULL,
  `content` longtext,
  `path` varchar(100) DEFAULT NULL COMMENT '模板路径',
  `status` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contract_template`
--

LOCK TABLES `contract_template` WRITE;
/*!40000 ALTER TABLE `contract_template` DISABLE KEYS */;
/*!40000 ALTER TABLE `contract_template` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coupon_type`
--

DROP TABLE IF EXISTS `coupon_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `coupon_type` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coupon_type`
--

LOCK TABLES `coupon_type` WRITE;
/*!40000 ALTER TABLE `coupon_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `coupon_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `crm_account`
--

DROP TABLE IF EXISTS `crm_account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `crm_account` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `creator_id` int(11) DEFAULT NULL,
  `createTime` datetime DEFAULT NULL,
  `updateTime` datetime DEFAULT NULL,
  `primaryContact_id` int(11) DEFAULT NULL,
  `type` varchar(10) DEFAULT NULL,
  `isConverted` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pc_id` (`primaryContact_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `crm_account`
--

LOCK TABLES `crm_account` WRITE;
/*!40000 ALTER TABLE `crm_account` DISABLE KEYS */;
/*!40000 ALTER TABLE `crm_account` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `crm_account_contact`
--

DROP TABLE IF EXISTS `crm_account_contact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `crm_account_contact` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(11) DEFAULT NULL,
  `contact_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_account_contact` (`account_id`,`contact_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `crm_account_contact`
--

LOCK TABLES `crm_account_contact` WRITE;
/*!40000 ALTER TABLE `crm_account_contact` DISABLE KEYS */;
/*!40000 ALTER TABLE `crm_account_contact` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `crm_activity`
--

DROP TABLE IF EXISTS `crm_activity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `crm_activity` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(11) DEFAULT NULL,
  `creator_id` int(11) DEFAULT NULL,
  `createTime` datetime DEFAULT NULL,
  `updateTime` datetime DEFAULT NULL,
  `ref_type` varchar(255) DEFAULT NULL,
  `ref_id` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `crm_activity`
--

LOCK TABLES `crm_activity` WRITE;
/*!40000 ALTER TABLE `crm_activity` DISABLE KEYS */;
/*!40000 ALTER TABLE `crm_activity` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `crm_branch_visit`
--

DROP TABLE IF EXISTS `crm_branch_visit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `crm_branch_visit` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `crm_branch_visit`
--

LOCK TABLES `crm_branch_visit` WRITE;
/*!40000 ALTER TABLE `crm_branch_visit` DISABLE KEYS */;
/*!40000 ALTER TABLE `crm_branch_visit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `crm_contact`
--

DROP TABLE IF EXISTS `crm_contact`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `crm_contact` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(11) DEFAULT NULL,
  `creator_id` int(11) DEFAULT NULL,
  `createTime` datetime DEFAULT NULL,
  `updateTime` datetime DEFAULT NULL,
  `type` varchar(10) DEFAULT NULL,
  `obfsNumber` varchar(255) DEFAULT NULL,
  `encryptedNumber` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `crm_contact`
--

LOCK TABLES `crm_contact` WRITE;
/*!40000 ALTER TABLE `crm_contact` DISABLE KEYS */;
/*!40000 ALTER TABLE `crm_contact` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `crm_gift`
--

DROP TABLE IF EXISTS `crm_gift`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `crm_gift` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `crm_gift`
--

LOCK TABLES `crm_gift` WRITE;
/*!40000 ALTER TABLE `crm_gift` DISABLE KEYS */;
/*!40000 ALTER TABLE `crm_gift` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `crm_identity`
--

DROP TABLE IF EXISTS `crm_identity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `crm_identity` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `crm_identity`
--

LOCK TABLES `crm_identity` WRITE;
/*!40000 ALTER TABLE `crm_identity` DISABLE KEYS */;
/*!40000 ALTER TABLE `crm_identity` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `crm_note`
--

DROP TABLE IF EXISTS `crm_note`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `crm_note` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` int(11) DEFAULT NULL,
  `creator_id` int(11) DEFAULT NULL,
  `createTime` datetime DEFAULT NULL,
  `updateTime` datetime DEFAULT NULL,
  `content` text,
  `isSolved` tinyint(1) DEFAULT '0' COMMENT '是否解决',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `crm_note`
--

LOCK TABLES `crm_note` WRITE;
/*!40000 ALTER TABLE `crm_note` DISABLE KEYS */;
/*!40000 ALTER TABLE `crm_note` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `crm_order`
--

DROP TABLE IF EXISTS `crm_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `crm_order` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `crm_order`
--

LOCK TABLES `crm_order` WRITE;
/*!40000 ALTER TABLE `crm_order` DISABLE KEYS */;
/*!40000 ALTER TABLE `crm_order` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `crm_phone_call`
--

DROP TABLE IF EXISTS `crm_phone_call`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `crm_phone_call` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `crm_phone_call`
--

LOCK TABLES `crm_phone_call` WRITE;
/*!40000 ALTER TABLE `crm_phone_call` DISABLE KEYS */;
/*!40000 ALTER TABLE `crm_phone_call` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `crm_solve_detail`
--

DROP TABLE IF EXISTS `crm_solve_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `crm_solve_detail` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL COMMENT '需求类型',
  `ref_id` int(11) NOT NULL COMMENT '需求id',
  `content` varchar(255) NOT NULL COMMENT '内容',
  `isSolved` tinyint(1) NOT NULL DEFAULT '0' COMMENT '需求是否解决',
  `auditor` int(11) NOT NULL COMMENT '操作人',
  `created_at` datetime DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `crm_solve_detail`
--

LOCK TABLES `crm_solve_detail` WRITE;
/*!40000 ALTER TABLE `crm_solve_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `crm_solve_detail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `crm_test`
--

DROP TABLE IF EXISTS `crm_test`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `crm_test` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `crm_test`
--

LOCK TABLES `crm_test` WRITE;
/*!40000 ALTER TABLE `crm_test` DISABLE KEYS */;
/*!40000 ALTER TABLE `crm_test` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `draw_record`
--

DROP TABLE IF EXISTS `draw_record`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `draw_record` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='提现记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `draw_record`
--

LOCK TABLES `draw_record` WRITE;
/*!40000 ALTER TABLE `draw_record` DISABLE KEYS */;
/*!40000 ALTER TABLE `draw_record` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ebankconfig`
--

DROP TABLE IF EXISTS `ebankconfig`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ebankconfig` (
  `bankId` int(4) NOT NULL,
  `typePersonal` tinyint(1) NOT NULL DEFAULT '0' COMMENT '支持个人为1',
  `typeBusiness` tinyint(1) NOT NULL DEFAULT '0' COMMENT '支持企业为1',
  `isDisabled` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否禁用0否',
  `singleLimit` decimal(14,2) NOT NULL COMMENT '单次限额',
  `dailyLimit` decimal(14,2) NOT NULL COMMENT '单日限额',
  PRIMARY KEY (`bankId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ebankconfig`
--

LOCK TABLES `ebankconfig` WRITE;
/*!40000 ALTER TABLE `ebankconfig` DISABLE KEYS */;
/*!40000 ALTER TABLE `ebankconfig` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ebao_quan`
--

DROP TABLE IF EXISTS `ebao_quan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ebao_quan` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ebao_quan`
--

LOCK TABLES `ebao_quan` WRITE;
/*!40000 ALTER TABLE `ebao_quan` DISABLE KEYS */;
/*!40000 ALTER TABLE `ebao_quan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `epayuser`
--

DROP TABLE IF EXISTS `epayuser`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `epayuser` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='托管方用户表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `epayuser`
--

LOCK TABLES `epayuser` WRITE;
/*!40000 ALTER TABLE `epayuser` DISABLE KEYS */;
/*!40000 ALTER TABLE `epayuser` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `goods_type`
--

DROP TABLE IF EXISTS `goods_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `goods_type` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `goods_type`
--

LOCK TABLES `goods_type` WRITE;
/*!40000 ALTER TABLE `goods_type` DISABLE KEYS */;
/*!40000 ALTER TABLE `goods_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invite_record`
--

DROP TABLE IF EXISTS `invite_record`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `invite_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `invitee_id` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invite_record`
--

LOCK TABLES `invite_record` WRITE;
/*!40000 ALTER TABLE `invite_record` DISABLE KEYS */;
/*!40000 ALTER TABLE `invite_record` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inviterrelation`
--

DROP TABLE IF EXISTS `inviterrelation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inviterrelation` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `inviterUid` int(10) NOT NULL COMMENT '邀请人uid',
  `inviteeUid` int(10) NOT NULL COMMENT '被邀请人uid',
  `code` varchar(50) NOT NULL COMMENT '邀请码',
  `created_at` int(10) NOT NULL,
  `updated_at` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `inviterUid` (`inviterUid`,`inviteeUid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='邀请关系表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inviterrelation`
--

LOCK TABLES `inviterrelation` WRITE;
/*!40000 ALTER TABLE `inviterrelation` DISABLE KEYS */;
/*!40000 ALTER TABLE `inviterrelation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `issuer`
--

DROP TABLE IF EXISTS `issuer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `issuer` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `issuer`
--

LOCK TABLES `issuer` WRITE;
/*!40000 ALTER TABLE `issuer` DISABLE KEYS */;
/*!40000 ALTER TABLE `issuer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `item_category`
--

DROP TABLE IF EXISTS `item_category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `item_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(10) unsigned NOT NULL COMMENT '项目ID',
  `category_id` int(10) unsigned NOT NULL COMMENT '分类ID',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '分类类型 1-资讯分类 ',
  `updated_at` int(10) NOT NULL COMMENT '更新时间',
  `created_at` int(10) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='项目、分类对照表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `item_category`
--

LOCK TABLES `item_category` WRITE;
/*!40000 ALTER TABLE `item_category` DISABLE KEYS */;
/*!40000 ALTER TABLE `item_category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `item_message`
--

DROP TABLE IF EXISTS `item_message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `item_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ticketId` int(11) DEFAULT NULL COMMENT '抽奖机会id',
  `content` varchar(255) DEFAULT NULL COMMENT '描述内容',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `item_message`
--

LOCK TABLES `item_message` WRITE;
/*!40000 ALTER TABLE `item_message` DISABLE KEYS */;
/*!40000 ALTER TABLE `item_message` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jx_page`
--

DROP TABLE IF EXISTS `jx_page`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jx_page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `issuerId` int(11) unsigned NOT NULL,
  `title` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `createTime` datetime DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `issuerId` (`issuerId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jx_page`
--

LOCK TABLES `jx_page` WRITE;
/*!40000 ALTER TABLE `jx_page` DISABLE KEYS */;
/*!40000 ALTER TABLE `jx_page` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `lenderstats`
--

DROP TABLE IF EXISTS `lenderstats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lenderstats` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lenderstats`
--

LOCK TABLES `lenderstats` WRITE;
/*!40000 ALTER TABLE `lenderstats` DISABLE KEYS */;
/*!40000 ALTER TABLE `lenderstats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `login_log`
--

DROP TABLE IF EXISTS `login_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `login_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `ip` varchar(30) NOT NULL COMMENT 'IP地址',
  `type` tinyint(1) NOT NULL COMMENT '渠道类型：1代表前台wap;2代表前台pc端;3代表后端控制台',
  `user_name` varchar(30) NOT NULL COMMENT '用户登陆名',
  `updated_at` int(11) DEFAULT NULL COMMENT '记录更新时间',
  `created_at` int(11) NOT NULL COMMENT '记录创建时间',
  `status` tinyint(1) DEFAULT '0' COMMENT '状态：0-失败，1-成功',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='登陆错误日志表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `login_log`
--

LOCK TABLES `login_log` WRITE;
/*!40000 ALTER TABLE `login_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `login_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `media`
--

DROP TABLE IF EXISTS `media`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `media` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(255) NOT NULL,
  `uri` varchar(255) NOT NULL,
  `createTime` datetime NOT NULL,
  `updateTime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `media`
--

LOCK TABLES `media` WRITE;
/*!40000 ALTER TABLE `media` DISABLE KEYS */;
/*!40000 ALTER TABLE `media` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migration`
--

DROP TABLE IF EXISTS `migration`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migration`
--

LOCK TABLES `migration` WRITE;
/*!40000 ALTER TABLE `migration` DISABLE KEYS */;
INSERT INTO `migration` VALUES ('m000000_000000_base',1461840166),('m160428_071753_create_perf_table',1461840167),('m160503_123142_create_BankCardUpdate_table',1462533612),('m160505_023846_alter_table_user_add_column',1462533612),('m160505_093225_ebao_quan',1462951600),('m160509_050909_bao_quan_queue',1462951600),('m160510_170800_create_UserAffiliation_table',1462951600),('m160510_170801_create_OrderAffiliation_table',1462951600),('m160510_170802_create_AffiliateCampaign_table',1462951600),('m160510_170803_create_Affiliator_table',1462951600),('m160511_064438_alter_perf',1463468342),('m160516_034721_create_user_coupon_table',1463468342),('m160516_075042_create_coupon_type_table',1463468342),('m160516_132201_create_Promo160520Log_table',1463643399),('m160517_182811_alter_online_order',1463643399),('m160518_065317_ranking_promo',1464226862),('m160518_065910_ranking_promo_offline_sale',1464226862),('m160518_120836_create_weixin_auth_table',1463643399),('m160518_120858_create_weixin_url_table',1463643399),('m160518_155122_create_payment_log_table',1463643399),('m160523_013926_ranking_auth',1464226862),('m160524_091620_update_auth_table',1464226862),('m160524_092322_update_afiliator_table',1464226862),('m160524_092750_update_affiliate_campaign_table',1464226862),('m160525_015557_alter_offline_sale',1464226862),('m160525_020030_update_coupon_type_table',1464319676),('m160525_021623_insert_auth_table',1464319676),('m160526_081854_alter_perf',1464347955),('m160527_022727_update_coupon_type_table',1464319676),('m160527_054319_update_affiliator_table',1464691180),('m160527_070253_alter_perf',1464347955),('m160530_080514_alter_perf',1464691181),('m160531_072333_add_count_link_auth',1464847605),('m160531_114436_update_adv_table',1467119959),('m160612_053750_drop_user_bank_status',1467119959),('m160615_060245_update_user_coupon_table',1467119959),('m160622_063725_update_category_kind',1467119959),('m160623_055621_update_news_table',1467119959),('m160629_035251_create_issuer_table',1467335326),('m160629_050623_insert_issuer_table',1467335326),('m160629_052236_insert_online_product_table',1467335326),('m160629_084500_update_auth_table',1467335326),('m160701_012547_update_auth_table',1467680913),('m160701_062435_add_admin_upload',1468377616),('m160704_065638_insert_auth_table',1468377616),('m160705_051053_promo_lottery_ticket',1467900113),('m160705_051111_user_promo',1467900113),('m160705_051849_update_auth_table',1467900113),('m160705_090130_alter_ranking_promo',1467900113),('m160705_092713_add_coupon_type',1467900113),('m160705_102713_rename_table_ranking_promo',1467900113),('m160706_092301_update_auth_table',1467900113),('m160706_125347_alter_promo_lottery_ticket',1467900113),('m160713_023355_alter_onlineproduct',1468570129),('m160718_060959_user_info',1468979910),('m160720_033626_repayment',1469170922),('m160721_095211_update_auth_table',1469692333),('m160722_034451_alter_user',1469692333),('m160725_020651_add_auth',1469692333),('m160725_055112_alter_order',1469692333),('m160726_021546_insert_auth_table',1469692333),('m160726_033302_alter_draw_record_table',1469692333),('m160726_055758_insert_auth_table',1469692333),('m160727_063548_delete_invite',1470037023),('m160727_064130_invite_record',1470037023),('m160727_070611_add_coupon_type',1470037023),('m160728_023604_create_CouponCode_table',1469764108),('m160728_031158_create_invite_promo',1470037023),('m160729_012349_update_couponcode_table',1469764108),('m160730_113145_add_coupon_type',1470037023),('m160801_073625_alter_end_date',1470308930),('m160809_012954_create_settle_table',1470728398),('m160809_052857_insert_promo_table',1470900019),('m160809_092045_create_promo0809_log_table',1470900019),('m160810_030509_alter_loan',1471412573),('m160811_103351_update_settle_table',1471412573),('m160815_012611_alter_user_table',1471412573),('m160815_052503_alter_user_bank_table',1471413056),('m160815_060433_alter_online_product_table',1471413056),('m160816_012313_create_sale_branch_table',1471413056),('m160816_012532_create_offline_loan_table',1471413056),('m160816_012735_create_offline_order_table',1471413056),('m160816_102547_create_credit_trade_table',1471572527),('m160817_032529_insert_auth_table',1472007818),('m160818_091444_user_asset',1472007818),('m160818_092303_init_user_asset',1472007819),('m160819_081634_insert_auth_table',1472007819),('m160822_031115_drop_salebranch_table',1472007819),('m160822_032524_alter_offlineorder_table',1472007819),('m160825_034539_update_online_product_table',1472174437),('m160825_051340_insert_auth_table',1472214464),('m160825_060453_admin_log',1472697148),('m160826_032258_update_offline_order_table',1472214464),('m160829_092707_alter_admin_log',1472697148),('m160830_075734_alter_online_product_table',1472697148),('m160831_012750_insert_coupon_type_table',1473126739),('m160901_064105_insert_auth_table',1473126739),('m160912_074636_alter_recharge_record_table',1477893217),('m160912_075320_alter_draw_record_table',1477893217),('m160913_091529_delete_credit_trade',1477893217),('m160919_064033_insert_coupon_type_table',1474854575),('m160929_034329_alter_online_repayment_plan_table',1477893217),('m160929_082209_delete_user_asset_table',1477893217),('m161018_091202_add_auth',1477998384),('m161019_013414_update_issuer_table',1477893217),('m161020_080654_alter_bao_quan_queue',1477893217),('m161025_022306_alter_ebao_quan',1477998384),('m161027_093521_insert_auth_table',1478224454),('m161027_095235_alter_offline_order_table',1478224454),('m161031_093818_alter_online_product_table',1477998384),('m161101_073418_alter_bao_quan_queue',1477998384),('m161104_082727_insert_auth_table',1478590250),('m161108_020150_insert_auth_table',1478655349),('m161109_085730_alter_user',1478763243),('m161111_071621_delete_auth_table',1479108142),('m161116_013919_alter_issuer_table',1479310097),('m161116_064557_insert_auth_table',1479310097),('m161117_014225_alter_adv',1479384307),('m161117_014719_share',1479384307),('m161117_021803_insert_auth_table',1480242782),('m161117_070925_alter_user_table',1479384307),('m161122_014042_insert_auth_table',1480242782),('m161122_073043_alter_issuer_table',1480482768),('m161123_064657_insert_share_table',1480242782),('m161123_083805_create_media_table',1480482768),('m161124_073646_alter_share_table',1480242782),('m161125_060548_update_share_table',1480242782),('m161125_111434_delete_auth_table',1480482768),('m161128_104536_alter_draw_record',1481888187),('m161129_020439_update_share_table',1480482768),('m161129_061331_alter_perf',1480924471),('m161201_064355_third_party_connect',1480924471),('m161202_060828_insert_auth_table',1481705666),('m161205_060225_insert_auth_table',1480924471),('m161205_070754_alter_promo_ticket',1481300603),('m161205_071051_alter_promo',1481300603),('m161205_094615_add_promo',1481300603),('m161205_161756_add_coupon_type',1481300603),('m161207_052841_add_promo',1481300603),('m161207_063847_add_coupon_type',1481300603),('m161212_023225_alter_online_product',1481705666),('m161212_023733_alter_adv',1481705666),('m161212_034752_insert_auth_table',1481705666),('m161212_051712_add_auth',1481705666),('m161213_030052_add_auth',1481705666),('m161214_014450_alter_repayment_plan',1481705667),('m161220_022159_insert_auth_table',1482368639),('m161220_025019_add_promo',1482368639),('m161220_034921_create_page_meta_table',1482368639),('m161220_070100_add_coupon',1482368639),('m161220_121032_add_category',1482368639),('m161221_114104_add_use',1483092895),('m161222_013843_alter_user',1483092896),('m161222_013937_point_order',1483092896),('m161222_013945_point_record',1483092896),('m161222_015344_add_coupon_type',1482485969),('m161222_045514_add_promo',1482485969),('m161222_053626_alter_point_order',1483092896),('m161222_114458_alter_promo_order',1483092896),('m161223_011138_alter_point_order',1483092896),('m161223_030308_add_promo',1483092896),('m161223_060209_alter_user',1483092896),('m161226_064250_alter_user_table',1483092897),('m161227_053212_add_online_product',1483092897),('m161227_093151_create_coins_record_table',1483092897),('m161228_011750_add_auth',1483092897),('m161229_025958_alter_news_table',1482988344),('m161229_031345_add_coupon_type',1483092897),('m161229_052632_alter_coupon_code',1483092902),('m161229_060700_create_goods_type',1483092902),('m161230_070049_alter_point_record_table',1483092902),('m161230_103205_add_auth',1483098081),('m161230_105416_add_coupon_type',1483096526),('m170103_050909_update_auth_table',1483434619),('m170103_060920_alter_loadn',1483434619),('m170103_071042_alter_promo_table',1483692346),('m170104_092720_alter_goods_type',1483692346),('m170105_073832_alter_loan_sort',1483692346),('m170105_092644_add_auth',1483692346),('m170105_100708_add_auth_table',1483692346),('m170106_074528_add_issuer_table',1487036296),('m170106_080757_add_auth',1484025588),('m170106_083632_alter_offline_product',1484189427),('m170106_083641_alter_offline_order',1484189427),('m170106_083718_create_offline_user',1484189427),('m170109_065359_add_auth_table',1484025588),('m170109_104833_add_auth_table',1487036296),('m170109_114147_alter_access_token_table',1487725613),('m170110_073702_add_auth',1484189427),('m170110_073753_alter_offline_user',1484189427),('m170111_030947_create_offline_stats_table',1484189427),('m170111_032119_insert_offline_stats_table',1484189427),('m170111_033910_alter_adv',1484189427),('m170111_054547_add_auth',1484653324),('m170111_055152_insert_auth_table',1484189427),('m170111_060233_alter_point_order',1484653324),('m170111_060258_alter_point_record',1484653324),('m170111_060444_alter_coins_record',1484653324),('m170112_013744_alter_promo_table',1484653324),('m170112_081754_add_point_record',1484653324),('m170112_082354_add_auth',1484653324),('m170113_071200_insert_auth_table',1484653324),('m170113_094124_add_promo',1484653324),('m170114_013005_insert_promo_table',1484653324),('m170114_032601_alter_offline_order',1484877501),('m170114_060226_add_auth',1484877501),('m170117_062400_referral_source',1484877501),('m170118_081317_add_auth',1484877501),('m170119_012424_add_promo',1484925647),('m170120_081625_add_auth',1486630686),('m170122_033538_add_auth_table',1486447008),('m170126_080032_insert_promo_table',1485426391),('m170213_013733_alter_affilicate',1487298712),('m170213_022851_recommend',1487298712),('m170213_093820_alter_adv_table',1487747147),('m170214_023849_add_auth',1487298712),('m170214_052002_drop_adv_pos_table',1487747147),('m170214_080308_update_auth_table',1487747147),('m170215_054134_add_auth',1487298712),('m170216_012815_create_promo_mobile_table',1487298712),('m170216_012840_alter_promo_table',1487298712),('m170216_031533_insert_promo_table',1487298712),('m170216_081530_alter_promo_mobile_table',1487298712),('m170216_114817_update_promo_table',1487298712),('m170217_021607_update_promo_table',1487747147),('m170221_015708_alter_issuer',1488157610),('m170221_075759_create_jx_page',1488157610),('m170222_173758_add_auth',1488157610),('m170223_022107_create_sms_config_table',1488157610),('m170223_030025_insert_sms_config_table',1488157610),('m170224_085557_alter_user',1488448926),('m170227_022016_add_column',1488448927),('m170227_032639_alter_referral_source',1488448927),('m170227_080848_add_promo',1488448927),('m170227_090151_add_referral_source',1488448927),('m170227_174521_add_auth_table',1488448927),('m170228_082125_alter_user',1488935017),('m170302_074046_alter_perf',1488804855),('m170306_084543_insert_promo_table',1488804855),('m170307_011321_alter_perf',1488935017),('m170307_053851_alter_perf',1489045272),('m170307_061825_alter_user',1488935017),('m170307_074748_alter_coupon_type_table',1489045272),('m170307_081121_insert_coupon_type_table',1489045272),('m170308_074947_insert_auth_table',1489045272),('m170308_113144_create_virtual_card',1489137214),('m170308_114553_add_goods_type',1489137214),('m170308_115109_add_promo',1489137214),('m170308_115128_alter_referral_source',1489137214),('m170310_061726_update_promo',1489137214),('m170310_064949_insert_auth_table',1489482259),('m170313_062041_alter_coupon_type_table',1489482259),('m170313_062101_insert_coupon_type_table',1489482259),('m170314_013147_captcha',1491379467),('m170314_031905_alter_goods_type',1489988089),('m170314_031928_update_goods_type',1489988089),('m170314_031954_alter_affiliator',1489988089),('m170314_032021_update_affiliator',1489988090),('m170314_094903_insert_auth_table',1489988090),('m170314_122422_alter_virtual_card',1489988090),('m170314_124438_update_virtual_card',1489988090),('m170316_034306_add_auth',1489988090),('m170316_071552_points_batch',1490337341),('m170316_112432_add_auth',1490337341),('m170321_014330_add_affiliator',1490064118),('m170321_014341_add_goods_type',1490064118),('m170321_014355_add_virtual_card',1490064119),('m170321_014419_add_fin_union_admin',1490064119),('m170321_083039_update_promo',1490147210),('m170323_034247_alter_virtual_card',1490337341),('m170323_051425_insert_auth_table',1490337341),('m170323_072156_alter_points_batch',1490608669),('m170323_124315_insert_auth_table',1490337341),('m170324_014403_add_auth',1490608669),('m170324_093356_alter_user_info',1491008653),('m170327_080015_alter_user',1490768464),('m170327_080022_alter_promo',1490768464),('m170327_080447_alter_referral_source',1490768464),('m170329_032332_alter_draw_record',1491376495),('m170330_060038_queue_task',1491484603),('m170331_030207_insert_auth_table',1491376495),('m170331_061510_alter_user_coupon_table',1491376496),('m170401_051704_alter_qpaybinding_table',1491484603),('m170401_064636_alter_queue_task',1491484603),('m170405_102910_alter_offline_oder_table',1491484604),('m170406_025741_add_promo',1491814365),('m170406_051337_create_reward',1491814365),('m170406_064148_alter_sms_table',1492155468),('m170406_065324_alter_sms_message_table',1492155468),('m170410_021913_alter_reward',1491814365),('m170410_022458_add_coupon',1491814365),('m170412_012632_alter_online_order_table',1492518097),('m170413_081316_check_in',1492094759),('m170413_091408_add_coupon',1492094759),('m170418_025645_alter_offline_loan_table',1492776561),('m170418_032116_insert_auth_table',1492776561),('m170418_051250_create_voucher',1492596342),('m170418_053554_alter_goods_type',1492596342),('m170418_124754_offline_repayment',1492776561),('m170419_030758_alter_auth',1492596342),('m170419_055255_alter_bao_quan_queue',1492681930),('m170419_081213_insert_auth_table',1492776561),('m170420_102121_insert_auth_table',1492776561),('m170421_024709_alter_queue_task',1492776561),('m170421_085158_insert_auth_table',1494325641),('m170421_090116_alter_offline_loan_table',1492776561),('m170421_115703_insert_auth_table',1492776561),('m170424_051738_crm_account',1493116707),('m170424_064018_alter_crm',1493116707),('m170425_024102_alter_crm_contact',1493116707),('m170426_024359_crm_phone_call',1493274473),('m170426_072108_add_promo',1493271642),('m170427_030129_update_promo',1493293169),('m170427_070941_alter_phone_call',1493293219),('m170427_091608_alter_ebgagenebt',1493293219),('m170428_050154_alter_crm',1493378383),('m170428_072027_crm_test',1493378383),('m170502_061402_crm',1493717312),('m170503_095317_create_promo_sequence_table',1493946603),('m170503_100626_alter_promo_lottery_ticket_table',1493946603),('m170503_101556_insert_promo_sequence_table',1493946603),('m170504_110248_insert_promo_table',1493946603),('m170508_091145_alter_my_cache_table',1494325641),('m170509_015459_add_auth',1495875954),('m170510_095636_alter_user_table',1494573530),('m170511_052927_insert_auth_table',1494573530),('m170511_062150_alter_voucher',1494573530),('m170511_094021_alter_voucher',1494573530),('m170512_055441_alter_perf',1494997678),('m170512_082818_insert_auth_table',1494834257),('m170515_092932_create_transfer_table',1495099073),('m170516_055250_alter_online_product',1495616424),('m170518_032832_add_promo',1495185686),('m170521_145009_alter_user',1495616457),('m170524_071435_insert_auth_table',1495875954),('m170525_023633_insert_auth_table',1495875954),('m170525_053341_crm_gift',1496384794),('m170526_025332_create_app_meta_table',1495882367),('m170526_033310_insert_auth_table',1495882367),('m170531_073814_callout',1496418922),('m170531_073836_callout_responder',1496418922),('m170602_083149_alter_transfer',1496418922),('m170602_095548_alter_callout',1496418922),('m170606_024004_insert_auth_table',1497410170),('m170606_024921_social_connect',1497410170),('m170606_024931_social_connect_log',1497410170),('m170606_070447_create_referral_table',1497410170),('m170612_101431_alter_draw_record',1497410170),('m170614_054708_alter_loan',1498612553),('m170614_080252_insert_auth_table',1498612553),('m170615_055231_alter_callout',1497619682),('m170619_032659_alter_transfer',1497856505),('m170619_081803_alter_product',1498612553),('m170620_063122_add_auth',1498612553),('m170621_024103_award',1498612553),('m170621_031033_create_retention_table',1498477124),('m170626_075752_alter_crm_gift',1498641572),('m170628_075139_alter_online_order',1498828282),('m170629_084742_add_auth',1498735454),('m170629_104309_add_auth',1498735454),('m170629_125123_alter_user_coupon_table',1498828282),('m170703_080709_alter_online_product',1499084428),('m170707_025650_alter_online_product_table',1499761670),('m170711_090719_crm_account_contact',1500000598),('m170712_022654_alter_promo_lottery_ticket',1500036158),('m170713_011630_ticket_token',1500036158),('m170720_200201_alter_online_product',1500606402),('m170724_063903_alter_voucher',1500968184),('m170725_075244_create_table_risk_assessment',1501205044),('m170726_075503_alter_points_batch',1501205450),('m170728_032330_alter_table_adv',1501489031),('m170728_080316_alter_promo_mobile',1501251514),('m170804_054121_alter_offline_user',1502244159),('m170807_030552_alter_crm_account',1502244159),('m170807_033449_alter_offline_user',1502244157),('m170808_061948_add_auth',1502443990),('m170809_011625_alter_offline_order',1502443990),('m170809_030405_alter_offline_loan',1502443990),('m170809_030614_alter_user',1502275635),('m170816_061924_alter_user',1506304953),('m170817_023510_add_table',1503307519),('m170821_013200_alter_online_product',1503307520),('m170821_052444_alter_open_account',1503307520),('m170822_095057_alter_open_account',1503475886),('m170823_014024_alter_open_account',1503475886),('m170824_114808_alter_award',1503825169),('m170828_071521_alter_contract_template',1503904940),('m170906_094254_create_table_poker',1505114984),('m170906_094722_create_table_poker_user',1505114984),('m170906_110057_add_auth',1504763727),('m170907_014608_alter_sms_message',1504865202),('m170908_024929_alter_templateid',1504865204),('m170913_070920_add_auth',1505382824),('m170914_032832_alter_affiliator',1505382824),('m170925_063231_alter_table_user_affiliation_add_index',1506326355),('m170928_104755_alter_ebao_quan',1508120490),('m171010_024147_alter_online_product',1507605553),('m171011_112348_insert_auth_table',1508120490),('m171019_060348_alter_admin',1508479658),('m171019_084708_add_uid_index',1508403776),('m171019_103209_add_idx_user_id',1508420328),('m171026_005507_create_second_kill_table',1509334646),('m171026_050753_create_appliament_table',1509334646),('m171031_032036_alter_coupon_type',1510122464),('m171031_032052_alter_online_product',1510122466),('m171101_020353_alter_coupon_type',1510122466),('m171105_024905_alter_payment_log',1512371703),('m171108_011952_update_auth_table',1510130775),('m171109_031558_insert_auth_table',1510660981),('m171110_084423_insert_auth',1510637308),('m171114_032349_insert_auth',1511167127),('m171116_032108_add_column_pkg_sn_online_product',1511404227),('m171116_084757_alter_user_info',1511159729),('m171117_033420_alter_social_connect',1510902701),('m171120_032706_alter_online_product',1511173183),('m171121_012543_insert_auth',1511255083),('m171122_062053_alter_affiliator',1511426518),('m171122_074602_alter_fin_admin',1511426518),('m171122_075331_insert_auth',1511426518),('m171123_021847_insert_auth',1511876853),('m171127_054830_alter_crm_note',1513649267),('m171127_061342_alter_crm_identity',1513649267),('m171128_030601_create_table_crm_solve_detail',1513649267),('m171201_082302_alter_admin_and_affiliator',1513652438),('m171206_100732_create_wechat_reply',1512617600),('m171207_031732_insert_wechat_reply',1512617600),('m171209_053956_alter_crm_identity',1513649268),('m171209_083308_alter_online_and_offline',1513649270),('m171211_075545_create_crm_order',1513649270),('m171211_094402_alter_crm_identity',1513649270),('m171212_060213_alter_wechat_reply',1513066338),('m171212_070527_insert_auth',1513066338),('m171212_072602_insert_auth_table',1514624928),('m171212_080857_create_splash_table',1514624928),('m171214_102034_alter_offline_loan',1513760548),('m171215_064121_create_offline_repayment_plan',1513760548),('m171218_010420_alter_offline_repayment_plan',1513760548),('m171218_011340_alter_online_product',1513686204),('m171218_070335_insert_auth',1513760548),('m171219_064038_alter_offline_repayment_plan',1513760548),('m171220_021445_alter_offline_loan',1513760548),('m171221_094044_alter_crm_order',1513858821),('m171225_090904_alter_offline_order_and_crm_order',1515575981),('m171227_130746_alter_online_product',1514423453),('m180102_032058_insert_auth_table',1514884821),('m180103_030542_create_annual_export',1515075255),('m180104_011835_alter_adv',1515055667),('m180104_030132_alter_news',1515055667),('m180104_075143_insert_auth_table',1515499394),('m180106_042559_alter_callout_responder',1515327521),('m180108_085307_alter_auth',1515464595),('m180110_061148_insert_auth_table',1515575979),('m180111_065041_insert_auth_table',1515758033),('m180115_065647_revise_auth_table',1516274144),('m180124_114308_alter_code_table',1516794940),('m180126_090125_create_share_log_table',1517307666),('m180129_114936_insert_auth',1518179508),('m180201_091334_alter_share_log_table',1517479158),('m180202_012402_alter_offline_loan',1517897963),('m180205_070810_create_session',1518010706),('m180206_081635_insert_auth_table',1519695106),('m180207_100607_insert_app_meta',1518058845),('m180209_114441_insert_auth',1518179508),('m180210_054002_create_question_table',1519228356),('m180210_054018_create_option_table',1519228356),('m180210_054058_alter_session',1519228356),('m180210_074126_insert_auth',1518260655),('m180226_102122_insert_auth_table',1521102917),('m180228_062636_alter_crm_identity',1520488336),('m180304_062232_insert_auth',1520328915),('m180304_090503_alter_affiliator',1520161817),('m180305_084304_insert_auth',1520328915),('m180313_051727_alter_offline_order',1521535302),('m180316_090651_alter_online_product',1522125743),('m180326_033307_alter_qpayconfig',1522125744),('m180326_062021_update_qpayconfig',1522125744),('m180404_052707_alter_transfer',1522839895),('m180404_053006_alter_bank_card_update',1522839895),('m180408_022753_insert_auth',1523332575),('m180409_053141_alter_login_log',1523332576),('m180411_072139_insert_auth',1523436566),('m180417_024119_create_channel',1524461852),('m180417_062346_alter_promo_table',1525325330),('m180419_071959_insert_auth',1524132770),('m180419_100134_insert_auth',1526442225),('m180428_024953_create_item_message_table',1525681906),('m180502_062929_create_transfer_tx',1525360485),('m180509_110751_insert_auth',1525870791),('m180515_064925_insert_auth',1526453846),('m180517_040837_alter_online_product',1527238653),('m180528_073326_create_table_asset',1530007003),('m180530_061951_alter_online_product',1528192067),('m180530_115414_insert_into_auth',1528451295),('m180531_021831_create_borrower_table',1530078048),('m180531_060713_alter_user_and_offlineuser_table',1528337859),('m180625_022600_create_identity_table',1531995280),('m180626_085718_alter_asset',1530007003),('m180627_063440_alter_online_product',1530522777),('m180628_020619_insert_auth',1530522777),('m180630_082356_insert_auth_table',1531357083);
/*!40000 ALTER TABLE `migration` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `money_record`
--

DROP TABLE IF EXISTS `money_record`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `money_record` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户资金记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `money_record`
--

LOCK TABLES `money_record` WRITE;
/*!40000 ALTER TABLE `money_record` DISABLE KEYS */;
/*!40000 ALTER TABLE `money_record` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `news`
--

DROP TABLE IF EXISTS `news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `news` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='新闻表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `news`
--

LOCK TABLES `news` WRITE;
/*!40000 ALTER TABLE `news` DISABLE KEYS */;
/*!40000 ALTER TABLE `news` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifylog`
--

DROP TABLE IF EXISTS `notifylog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifylog` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `query` text NOT NULL,
  `created_at` int(10) NOT NULL,
  `updated_at` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifylog`
--

LOCK TABLES `notifylog` WRITE;
/*!40000 ALTER TABLE `notifylog` DISABLE KEYS */;
/*!40000 ALTER TABLE `notifylog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `offline_loan`
--

DROP TABLE IF EXISTS `offline_loan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `offline_loan` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `offline_loan`
--

LOCK TABLES `offline_loan` WRITE;
/*!40000 ALTER TABLE `offline_loan` DISABLE KEYS */;
/*!40000 ALTER TABLE `offline_loan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `offline_order`
--

DROP TABLE IF EXISTS `offline_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `offline_order` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `offline_order`
--

LOCK TABLES `offline_order` WRITE;
/*!40000 ALTER TABLE `offline_order` DISABLE KEYS */;
/*!40000 ALTER TABLE `offline_order` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `offline_repayment`
--

DROP TABLE IF EXISTS `offline_repayment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `offline_repayment` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `offline_repayment`
--

LOCK TABLES `offline_repayment` WRITE;
/*!40000 ALTER TABLE `offline_repayment` DISABLE KEYS */;
/*!40000 ALTER TABLE `offline_repayment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `offline_repayment_plan`
--

DROP TABLE IF EXISTS `offline_repayment_plan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `offline_repayment_plan` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `offline_repayment_plan`
--

LOCK TABLES `offline_repayment_plan` WRITE;
/*!40000 ALTER TABLE `offline_repayment_plan` DISABLE KEYS */;
/*!40000 ALTER TABLE `offline_repayment_plan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `offline_stats`
--

DROP TABLE IF EXISTS `offline_stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `offline_stats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tradedAmount` decimal(14,2) NOT NULL,
  `refundedPrincipal` decimal(14,2) NOT NULL,
  `refundedInterest` decimal(14,2) NOT NULL,
  `createTime` datetime NOT NULL,
  `updateTime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `offline_stats`
--

LOCK TABLES `offline_stats` WRITE;
/*!40000 ALTER TABLE `offline_stats` DISABLE KEYS */;
/*!40000 ALTER TABLE `offline_stats` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `offline_user`
--

DROP TABLE IF EXISTS `offline_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `offline_user` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `offline_user`
--

LOCK TABLES `offline_user` WRITE;
/*!40000 ALTER TABLE `offline_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `offline_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `online_fangkuan`
--

DROP TABLE IF EXISTS `online_fangkuan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `online_fangkuan` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='在线标的放款订单';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `online_fangkuan`
--

LOCK TABLES `online_fangkuan` WRITE;
/*!40000 ALTER TABLE `online_fangkuan` DISABLE KEYS */;
/*!40000 ALTER TABLE `online_fangkuan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `online_fangkuan_detail`
--

DROP TABLE IF EXISTS `online_fangkuan_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `online_fangkuan_detail` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='在线标的放款订单明细';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `online_fangkuan_detail`
--

LOCK TABLES `online_fangkuan_detail` WRITE;
/*!40000 ALTER TABLE `online_fangkuan_detail` DISABLE KEYS */;
/*!40000 ALTER TABLE `online_fangkuan_detail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `online_order`
--

DROP TABLE IF EXISTS `online_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `online_order` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='标的订单';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `online_order`
--

LOCK TABLES `online_order` WRITE;
/*!40000 ALTER TABLE `online_order` DISABLE KEYS */;
/*!40000 ALTER TABLE `online_order` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `online_product`
--

DROP TABLE IF EXISTS `online_product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `online_product` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='线上标的产品表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `online_product`
--

LOCK TABLES `online_product` WRITE;
/*!40000 ALTER TABLE `online_product` DISABLE KEYS */;
/*!40000 ALTER TABLE `online_product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `online_repayment_plan`
--

DROP TABLE IF EXISTS `online_repayment_plan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `online_repayment_plan` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='标的还款计划表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `online_repayment_plan`
--

LOCK TABLES `online_repayment_plan` WRITE;
/*!40000 ALTER TABLE `online_repayment_plan` DISABLE KEYS */;
/*!40000 ALTER TABLE `online_repayment_plan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `online_repayment_record`
--

DROP TABLE IF EXISTS `online_repayment_record`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `online_repayment_record` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='还款记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `online_repayment_record`
--

LOCK TABLES `online_repayment_record` WRITE;
/*!40000 ALTER TABLE `online_repayment_record` DISABLE KEYS */;
/*!40000 ALTER TABLE `online_repayment_record` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `open_account`
--

DROP TABLE IF EXISTS `open_account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `open_account` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `open_account`
--

LOCK TABLES `open_account` WRITE;
/*!40000 ALTER TABLE `open_account` DISABLE KEYS */;
/*!40000 ALTER TABLE `open_account` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `option`
--

DROP TABLE IF EXISTS `option`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `option` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `questionId` int(11) NOT NULL COMMENT '题目ID',
  `content` text NOT NULL COMMENT '选项内容',
  `createTime` datetime DEFAULT NULL COMMENT '创建时间',
  `updateTime` datetime DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `option`
--

LOCK TABLES `option` WRITE;
/*!40000 ALTER TABLE `option` DISABLE KEYS */;
/*!40000 ALTER TABLE `option` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_affiliation`
--

DROP TABLE IF EXISTS `order_affiliation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_affiliation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL,
  `trackCode` varchar(255) DEFAULT NULL,
  `affiliator_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_affiliation`
--

LOCK TABLES `order_affiliation` WRITE;
/*!40000 ALTER TABLE `order_affiliation` DISABLE KEYS */;
/*!40000 ALTER TABLE `order_affiliation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orderqueue`
--

DROP TABLE IF EXISTS `orderqueue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orderqueue` (
  `orderSn` varchar(30) NOT NULL COMMENT '订单sn',
  `status` tinyint(1) NOT NULL COMMENT '处理状态0未处理1处理',
  `updated_at` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  PRIMARY KEY (`orderSn`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orderqueue`
--

LOCK TABLES `orderqueue` WRITE;
/*!40000 ALTER TABLE `orderqueue` DISABLE KEYS */;
/*!40000 ALTER TABLE `orderqueue` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `page_meta`
--

DROP TABLE IF EXISTS `page_meta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `page_meta` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `href` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `keywords` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `page_meta`
--

LOCK TABLES `page_meta` WRITE;
/*!40000 ALTER TABLE `page_meta` DISABLE KEYS */;
/*!40000 ALTER TABLE `page_meta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment`
--

DROP TABLE IF EXISTS `payment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment` (
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment`
--

LOCK TABLES `payment` WRITE;
/*!40000 ALTER TABLE `payment` DISABLE KEYS */;
/*!40000 ALTER TABLE `payment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payment_log`
--

DROP TABLE IF EXISTS `payment_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payment_log` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payment_log`
--

LOCK TABLES `payment_log` WRITE;
/*!40000 ALTER TABLE `payment_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `payment_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `perf`
--

DROP TABLE IF EXISTS `perf`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `perf` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `perf`
--

LOCK TABLES `perf` WRITE;
/*!40000 ALTER TABLE `perf` DISABLE KEYS */;
/*!40000 ALTER TABLE `perf` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `point_order`
--

DROP TABLE IF EXISTS `point_order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `point_order` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `point_order`
--

LOCK TABLES `point_order` WRITE;
/*!40000 ALTER TABLE `point_order` DISABLE KEYS */;
/*!40000 ALTER TABLE `point_order` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `point_record`
--

DROP TABLE IF EXISTS `point_record`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `point_record` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `point_record`
--

LOCK TABLES `point_record` WRITE;
/*!40000 ALTER TABLE `point_record` DISABLE KEYS */;
/*!40000 ALTER TABLE `point_record` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `points_batch`
--

DROP TABLE IF EXISTS `points_batch`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `points_batch` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `points_batch`
--

LOCK TABLES `points_batch` WRITE;
/*!40000 ALTER TABLE `points_batch` DISABLE KEYS */;
/*!40000 ALTER TABLE `points_batch` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `poker`
--

DROP TABLE IF EXISTS `poker`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `poker` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `term` varchar(10) NOT NULL,
  `spade` int(11) NOT NULL,
  `heart` int(11) NOT NULL,
  `club` int(11) NOT NULL,
  `diamond` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `term` (`term`),
  UNIQUE KEY `unique_term` (`term`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `poker`
--

LOCK TABLES `poker` WRITE;
/*!40000 ALTER TABLE `poker` DISABLE KEYS */;
/*!40000 ALTER TABLE `poker` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `poker_user`
--

DROP TABLE IF EXISTS `poker_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `poker_user` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `poker_user`
--

LOCK TABLES `poker_user` WRITE;
/*!40000 ALTER TABLE `poker_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `poker_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `promo`
--

DROP TABLE IF EXISTS `promo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `promo` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `promo`
--

LOCK TABLES `promo` WRITE;
/*!40000 ALTER TABLE `promo` DISABLE KEYS */;
/*!40000 ALTER TABLE `promo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `promo0809_log`
--

DROP TABLE IF EXISTS `promo0809_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `promo0809_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `prize_id` int(1) NOT NULL,
  `user_address` varchar(255) NOT NULL,
  `createdAt` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `promo0809_log`
--

LOCK TABLES `promo0809_log` WRITE;
/*!40000 ALTER TABLE `promo0809_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `promo0809_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `promo160520_log`
--

DROP TABLE IF EXISTS `promo160520_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `promo160520_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mobile` varchar(20) NOT NULL,
  `prizeId` smallint(1) NOT NULL,
  `isNewUser` tinyint(1) NOT NULL,
  `count` smallint(1) NOT NULL,
  `createdAt` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mobile` (`mobile`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `promo160520_log`
--

LOCK TABLES `promo160520_log` WRITE;
/*!40000 ALTER TABLE `promo160520_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `promo160520_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `promo_lottery_ticket`
--

DROP TABLE IF EXISTS `promo_lottery_ticket`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `promo_lottery_ticket` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `promo_lottery_ticket`
--

LOCK TABLES `promo_lottery_ticket` WRITE;
/*!40000 ALTER TABLE `promo_lottery_ticket` DISABLE KEYS */;
/*!40000 ALTER TABLE `promo_lottery_ticket` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `promo_mobile`
--

DROP TABLE IF EXISTS `promo_mobile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `promo_mobile` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `promo_id` int(11) DEFAULT NULL,
  `mobile` varchar(255) NOT NULL,
  `createTime` datetime NOT NULL,
  `ip` varchar(255) NOT NULL,
  `referralSource` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `promo_mobile`
--

LOCK TABLES `promo_mobile` WRITE;
/*!40000 ALTER TABLE `promo_mobile` DISABLE KEYS */;
/*!40000 ALTER TABLE `promo_mobile` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `promo_sequence`
--

DROP TABLE IF EXISTS `promo_sequence`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `promo_sequence` (
  `id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `promo_sequence`
--

LOCK TABLES `promo_sequence` WRITE;
/*!40000 ALTER TABLE `promo_sequence` DISABLE KEYS */;
/*!40000 ALTER TABLE `promo_sequence` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `qpaybinding`
--

DROP TABLE IF EXISTS `qpaybinding`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `qpaybinding` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户绑卡申请表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `qpaybinding`
--

LOCK TABLES `qpaybinding` WRITE;
/*!40000 ALTER TABLE `qpaybinding` DISABLE KEYS */;
/*!40000 ALTER TABLE `qpaybinding` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `qpayconfig`
--

DROP TABLE IF EXISTS `qpayconfig`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `qpayconfig` (
  `bankId` int(4) NOT NULL,
  `isDisabled` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否禁用0否1是',
  `singleLimit` decimal(14,2) NOT NULL COMMENT '单次限额',
  `dailyLimit` decimal(14,2) NOT NULL COMMENT '单日限额',
  `allowBind` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`bankId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `qpayconfig`
--

LOCK TABLES `qpayconfig` WRITE;
/*!40000 ALTER TABLE `qpayconfig` DISABLE KEYS */;
/*!40000 ALTER TABLE `qpayconfig` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `question`
--

DROP TABLE IF EXISTS `question`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `question` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL COMMENT '问题',
  `batchSn` varchar(20) NOT NULL COMMENT '批次号',
  `promoId` int(11) DEFAULT NULL COMMENT '活动ID',
  `answer` varchar(255) DEFAULT NULL COMMENT '答案',
  `createTime` datetime DEFAULT NULL COMMENT '创建时间',
  `updateTime` datetime DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `question`
--

LOCK TABLES `question` WRITE;
/*!40000 ALTER TABLE `question` DISABLE KEYS */;
/*!40000 ALTER TABLE `question` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `queue_task`
--

DROP TABLE IF EXISTS `queue_task`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `queue_task` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `queue_task`
--

LOCK TABLES `queue_task` WRITE;
/*!40000 ALTER TABLE `queue_task` DISABLE KEYS */;
/*!40000 ALTER TABLE `queue_task` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ranking_promo_offline_sale`
--

DROP TABLE IF EXISTS `ranking_promo_offline_sale`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ranking_promo_offline_sale` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rankingPromoOfflineSale_id` int(11) NOT NULL,
  `mobile` varchar(11) DEFAULT NULL,
  `totalInvest` decimal(10,0) DEFAULT NULL,
  `investedAt` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ranking_promo_offline_sale`
--

LOCK TABLES `ranking_promo_offline_sale` WRITE;
/*!40000 ALTER TABLE `ranking_promo_offline_sale` DISABLE KEYS */;
/*!40000 ALTER TABLE `ranking_promo_offline_sale` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `recharge_record`
--

DROP TABLE IF EXISTS `recharge_record`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `recharge_record` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='充值记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `recharge_record`
--

LOCK TABLES `recharge_record` WRITE;
/*!40000 ALTER TABLE `recharge_record` DISABLE KEYS */;
/*!40000 ALTER TABLE `recharge_record` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `referral`
--

DROP TABLE IF EXISTS `referral`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `referral` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `code` varchar(255) NOT NULL,
  `created_at` int(10) DEFAULT NULL,
  `updated_at` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `referral`
--

LOCK TABLES `referral` WRITE;
/*!40000 ALTER TABLE `referral` DISABLE KEYS */;
/*!40000 ALTER TABLE `referral` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `referral_source`
--

DROP TABLE IF EXISTS `referral_source`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `referral_source` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(15) NOT NULL,
  `target` varchar(255) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `isActive` tinyint(1) DEFAULT '1',
  `createTime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `referral_source`
--

LOCK TABLES `referral_source` WRITE;
/*!40000 ALTER TABLE `referral_source` DISABLE KEYS */;
/*!40000 ALTER TABLE `referral_source` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `region`
--

DROP TABLE IF EXISTS `region`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `region` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '区域ID',
  `code` varchar(64) NOT NULL COMMENT '代码',
  `name` varchar(256) NOT NULL COMMENT '名称',
  `province_id` int(11) NOT NULL COMMENT '所属省ID（0不存在）',
  `city_id` int(11) NOT NULL COMMENT '所属市ID（0不存在）',
  `show_order` int(11) NOT NULL COMMENT '显示顺序',
  PRIMARY KEY (`id`),
  KEY `i_region_code` (`code`),
  KEY `i_region_province_city` (`province_id`,`city_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='区域表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `region`
--

LOCK TABLES `region` WRITE;
/*!40000 ALTER TABLE `region` DISABLE KEYS */;
/*!40000 ALTER TABLE `region` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `repayment`
--

DROP TABLE IF EXISTS `repayment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `repayment` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `repayment`
--

LOCK TABLES `repayment` WRITE;
/*!40000 ALTER TABLE `repayment` DISABLE KEYS */;
/*!40000 ALTER TABLE `repayment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `retention`
--

DROP TABLE IF EXISTS `retention`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `retention` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `retention`
--

LOCK TABLES `retention` WRITE;
/*!40000 ALTER TABLE `retention` DISABLE KEYS */;
/*!40000 ALTER TABLE `retention` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reward`
--

DROP TABLE IF EXISTS `reward`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reward` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reward`
--

LOCK TABLES `reward` WRITE;
/*!40000 ALTER TABLE `reward` DISABLE KEYS */;
/*!40000 ALTER TABLE `reward` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `risk_assessment`
--

DROP TABLE IF EXISTS `risk_assessment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `risk_assessment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  `grade` int(11) NOT NULL,
  `content` varchar(255) NOT NULL,
  `createTime` datetime NOT NULL,
  `isDel` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `risk_assessment`
--

LOCK TABLES `risk_assessment` WRITE;
/*!40000 ALTER TABLE `risk_assessment` DISABLE KEYS */;
/*!40000 ALTER TABLE `risk_assessment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role`
--

DROP TABLE IF EXISTS `role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `sn` char(10) DEFAULT '' COMMENT '编号',
  `role_name` varchar(50) NOT NULL COMMENT '角色',
  `role_description` varchar(100) NOT NULL COMMENT '角色说明',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态0冻结，1激活',
  `updated_at` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sn_unique` (`sn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role`
--

LOCK TABLES `role` WRITE;
/*!40000 ALTER TABLE `role` DISABLE KEYS */;
/*!40000 ALTER TABLE `role` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_auth`
--

DROP TABLE IF EXISTS `role_auth`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_auth` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `role_sn` char(24) NOT NULL COMMENT '角色sn',
  `auth_sn` char(24) NOT NULL COMMENT '权限sn',
  `auth_name` varchar(30) NOT NULL COMMENT '权限名称',
  `status` tinyint(1) DEFAULT '1' COMMENT '状态0冻结，1激活',
  `updated_at` int(11) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_auth`
--

LOCK TABLES `role_auth` WRITE;
/*!40000 ALTER TABLE `role_auth` DISABLE KEYS */;
/*!40000 ALTER TABLE `role_auth` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `second_kill`
--

DROP TABLE IF EXISTS `second_kill`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `second_kill` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(11) unsigned NOT NULL COMMENT '用户ID ',
  `createTime` int(11) unsigned NOT NULL COMMENT '获奖时间',
  `term` varchar(10) NOT NULL COMMENT '物品编号',
  PRIMARY KEY (`id`),
  UNIQUE KEY `term_2` (`userId`,`term`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `second_kill`
--

LOCK TABLES `second_kill` WRITE;
/*!40000 ALTER TABLE `second_kill` DISABLE KEYS */;
/*!40000 ALTER TABLE `second_kill` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `session`
--

DROP TABLE IF EXISTS `session`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `session` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL,
  `batchSn` varchar(255) NOT NULL,
  `createTime` datetime DEFAULT NULL,
  `answers` text COMMENT '答题信息记录',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `session`
--

LOCK TABLES `session` WRITE;
/*!40000 ALTER TABLE `session` DISABLE KEYS */;
/*!40000 ALTER TABLE `session` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settle`
--

DROP TABLE IF EXISTS `settle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `txSn` varchar(60) NOT NULL,
  `txDate` date NOT NULL,
  `money` decimal(14,2) NOT NULL,
  `fee` decimal(14,2) DEFAULT NULL,
  `serviceSn` varchar(60) NOT NULL,
  `txType` int(11) NOT NULL,
  `status` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settle`
--

LOCK TABLES `settle` WRITE;
/*!40000 ALTER TABLE `settle` DISABLE KEYS */;
/*!40000 ALTER TABLE `settle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `share`
--

DROP TABLE IF EXISTS `share`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `share` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `share`
--

LOCK TABLES `share` WRITE;
/*!40000 ALTER TABLE `share` DISABLE KEYS */;
/*!40000 ALTER TABLE `share` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `share_log`
--

DROP TABLE IF EXISTS `share_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `share_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userId` int(11) NOT NULL COMMENT '用户id',
  `scene` varchar(10) NOT NULL COMMENT '分享场景',
  `shareUrl` varchar(255) NOT NULL COMMENT '分享的url',
  `ipAddress` varchar(50) DEFAULT NULL COMMENT 'ip地址',
  `createdAt` date NOT NULL COMMENT '分享日期',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `share_log`
--

LOCK TABLES `share_log` WRITE;
/*!40000 ALTER TABLE `share_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `share_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sms`
--

DROP TABLE IF EXISTS `sms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sms` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sms`
--

LOCK TABLES `sms` WRITE;
/*!40000 ALTER TABLE `sms` DISABLE KEYS */;
/*!40000 ALTER TABLE `sms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sms_config`
--

DROP TABLE IF EXISTS `sms_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sms_config` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `template_id` varchar(255) NOT NULL,
  `config` text NOT NULL,
  `createTime` datetime NOT NULL,
  `updateTime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sms_config`
--

LOCK TABLES `sms_config` WRITE;
/*!40000 ALTER TABLE `sms_config` DISABLE KEYS */;
/*!40000 ALTER TABLE `sms_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sms_message`
--

DROP TABLE IF EXISTS `sms_message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sms_message` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sms_message`
--

LOCK TABLES `sms_message` WRITE;
/*!40000 ALTER TABLE `sms_message` DISABLE KEYS */;
/*!40000 ALTER TABLE `sms_message` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `social_connect`
--

DROP TABLE IF EXISTS `social_connect`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `social_connect` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `resourceOwner_id` varchar(128) NOT NULL,
  `provider_type` varchar(20) DEFAULT NULL,
  `createTime` datetime DEFAULT NULL,
  `isAutoLogin` tinyint(1) DEFAULT '1' COMMENT '是否自动登录',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_owner` (`resourceOwner_id`,`provider_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `social_connect`
--

LOCK TABLES `social_connect` WRITE;
/*!40000 ALTER TABLE `social_connect` DISABLE KEYS */;
/*!40000 ALTER TABLE `social_connect` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `social_connect_log`
--

DROP TABLE IF EXISTS `social_connect_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `social_connect_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `resourceOwner_id` varchar(128) NOT NULL,
  `action` varchar(20) DEFAULT NULL,
  `provider_type` varchar(20) DEFAULT NULL,
  `data` text,
  `createTime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `social_connect_log`
--

LOCK TABLES `social_connect_log` WRITE;
/*!40000 ALTER TABLE `social_connect_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `social_connect_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `splash`
--

DROP TABLE IF EXISTS `splash`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `splash` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `splash`
--

LOCK TABLES `splash` WRITE;
/*!40000 ALTER TABLE `splash` DISABLE KEYS */;
/*!40000 ALTER TABLE `splash` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `third_party_connect`
--

DROP TABLE IF EXISTS `third_party_connect`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `third_party_connect` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `publicId` varchar(255) NOT NULL,
  `visitor_id` varchar(255) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `thirdPartyUser_id` varchar(255) DEFAULT NULL,
  `createTime` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `third_party_connect`
--

LOCK TABLES `third_party_connect` WRITE;
/*!40000 ALTER TABLE `third_party_connect` DISABLE KEYS */;
/*!40000 ALTER TABLE `third_party_connect` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ticket_token`
--

DROP TABLE IF EXISTS `ticket_token`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ticket_token` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ticket_token`
--

LOCK TABLES `ticket_token` WRITE;
/*!40000 ALTER TABLE `ticket_token` DISABLE KEYS */;
/*!40000 ALTER TABLE `ticket_token` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tradelog`
--

DROP TABLE IF EXISTS `tradelog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tradelog` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='交易日志表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tradelog`
--

LOCK TABLES `tradelog` WRITE;
/*!40000 ALTER TABLE `tradelog` DISABLE KEYS */;
/*!40000 ALTER TABLE `tradelog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transfer`
--

DROP TABLE IF EXISTS `transfer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transfer` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transfer`
--

LOCK TABLES `transfer` WRITE;
/*!40000 ALTER TABLE `transfer` DISABLE KEYS */;
/*!40000 ALTER TABLE `transfer` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `transfer_tx`
--

DROP TABLE IF EXISTS `transfer_tx`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `transfer_tx` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `transfer_tx`
--

LOCK TABLES `transfer_tx` WRITE;
/*!40000 ALTER TABLE `transfer_tx` DISABLE KEYS */;
/*!40000 ALTER TABLE `transfer_tx` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_account`
--

DROP TABLE IF EXISTS `user_account`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_account` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户资金表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_account`
--

LOCK TABLES `user_account` WRITE;
/*!40000 ALTER TABLE `user_account` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_account` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_affiliation`
--

DROP TABLE IF EXISTS `user_affiliation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_affiliation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `trackCode` varchar(255) DEFAULT NULL,
  `affiliator_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `affiliator_id` (`affiliator_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_affiliation`
--

LOCK TABLES `user_affiliation` WRITE;
/*!40000 ALTER TABLE `user_affiliation` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_affiliation` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_bank`
--

DROP TABLE IF EXISTS `user_bank`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_bank` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户银行账号';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_bank`
--

LOCK TABLES `user_bank` WRITE;
/*!40000 ALTER TABLE `user_bank` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_bank` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_coupon`
--

DROP TABLE IF EXISTS `user_coupon`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_coupon` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_coupon`
--

LOCK TABLES `user_coupon` WRITE;
/*!40000 ALTER TABLE `user_coupon` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_coupon` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_info`
--

DROP TABLE IF EXISTS `user_info`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_info` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_info`
--

LOCK TABLES `user_info` WRITE;
/*!40000 ALTER TABLE `user_info` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_info` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_promo`
--

DROP TABLE IF EXISTS `user_promo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_promo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `promo_key` varchar(50) DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_uid_key` (`user_id`,`promo_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_promo`
--

LOCK TABLES `user_promo` WRITE;
/*!40000 ALTER TABLE `user_promo` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_promo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `virtual_card`
--

DROP TABLE IF EXISTS `virtual_card`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `virtual_card` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `virtual_card`
--

LOCK TABLES `virtual_card` WRITE;
/*!40000 ALTER TABLE `virtual_card` DISABLE KEYS */;
/*!40000 ALTER TABLE `virtual_card` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `voucher`
--

DROP TABLE IF EXISTS `voucher`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `voucher` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `voucher`
--

LOCK TABLES `voucher` WRITE;
/*!40000 ALTER TABLE `voucher` DISABLE KEYS */;
/*!40000 ALTER TABLE `voucher` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `wechat_reply`
--

DROP TABLE IF EXISTS `wechat_reply`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `wechat_reply` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(255) DEFAULT NULL COMMENT '回复类型',
  `keyword` varchar(255) DEFAULT NULL COMMENT '关键字',
  `content` text COMMENT '内容',
  `isDel` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `createdAt` int(11) NOT NULL COMMENT '创建时间',
  `updatedAt` int(11) NOT NULL COMMENT '更新时间',
  `style` varchar(255) DEFAULT NULL COMMENT '消息类型',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `wechat_reply`
--

LOCK TABLES `wechat_reply` WRITE;
/*!40000 ALTER TABLE `wechat_reply` DISABLE KEYS */;
/*!40000 ALTER TABLE `wechat_reply` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `weixin_auth`
--

DROP TABLE IF EXISTS `weixin_auth`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weixin_auth` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `appId` varchar(255) NOT NULL,
  `accessToken` varchar(255) DEFAULT NULL,
  `jsApiTicket` varchar(255) DEFAULT NULL,
  `expiresAt` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `appId` (`appId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `weixin_auth`
--

LOCK TABLES `weixin_auth` WRITE;
/*!40000 ALTER TABLE `weixin_auth` DISABLE KEYS */;
/*!40000 ALTER TABLE `weixin_auth` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `weixin_url`
--

DROP TABLE IF EXISTS `weixin_url`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `weixin_url` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `auth_id` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `weixin_url`
--

LOCK TABLES `weixin_url` WRITE;
/*!40000 ALTER TABLE `weixin_url` DISABLE KEYS */;
/*!40000 ALTER TABLE `weixin_url` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2018-07-20 15:21:09
