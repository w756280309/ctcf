CREATE TABLE `QpayBinding` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `binding_sn` VARCHAR(32) NOT NULL COMMENT '绑卡流水号',
  `uid` INT(10) UNSIGNED DEFAULT NULL,
  `epayUserId` VARCHAR(60) NOT NULL COMMENT '托管平台用户号',
  `bank_id` VARCHAR(255) DEFAULT NULL COMMENT '银行id',
  `bank_name` VARCHAR(255) DEFAULT NULL COMMENT '银行名称',
  `sub_bank_name` VARCHAR(255) DEFAULT NULL COMMENT '开户支行名称',
  `province` VARCHAR(30) DEFAULT NULL COMMENT '省',
  `city` VARCHAR(30) DEFAULT NULL COMMENT '城市',
  `account` VARCHAR(30) DEFAULT NULL COMMENT '持卡人姓名',
  `card_number` VARCHAR(50) DEFAULT NULL COMMENT '银行卡号',
  `account_type` TINYINT(2) UNSIGNED DEFAULT '11' COMMENT '11=个人账户 12=企业账户',
  `mobile` VARCHAR(11) DEFAULT NULL COMMENT '手机号码',
  `status` TINYINT(2) UNSIGNED DEFAULT '0' COMMENT '状态 0-未绑定 1-已绑定 3-处理中',
  `created_at` INT(10) UNSIGNED DEFAULT NULL,
  `updated_at` INT(10) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `binding_sn` (`binding_sn`)
) ENGINE=INNODB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COMMENT='用户绑卡申请表';

ALTER TABLE `user_bank`   
  ADD COLUMN `epayUserId` VARCHAR(60) NOT NULL  COMMENT '托管平台用户号' AFTER `uid`;
