ALTER TABLE `draw_record`   
  CHANGE `uid` `uid` INT(10) UNSIGNED NOT NULL,
  ADD COLUMN `identification_type` TINYINT(1) NOT NULL  COMMENT '证件类型0=身份证 1=户口簿 2=护照 3=军官证 4=士兵证 5=港澳居民来往内地通行证 6=台湾同胞来往内地通行证 7=临时身份证 8=外国人居留证 9=警官证' AFTER `uid`,
  ADD COLUMN `identification_number` VARCHAR(32) NOT NULL  COMMENT '证件号' AFTER `identification_type`,
  ADD COLUMN `user_bank_id` INT(11) NOT NULL  COMMENT 'userbank的id' AFTER `identification_number`,
  ADD COLUMN `sub_bank_name` VARCHAR(255) NOT NULL  COMMENT '分支行名称' AFTER `user_bank_id`,
  ADD COLUMN `province` VARCHAR(30) NOT NULL  COMMENT '省' AFTER `sub_bank_name`,
  ADD COLUMN `city` VARCHAR(30) NOT NULL  COMMENT '市' AFTER `province`,
  ADD COLUMN `mobile` VARCHAR(16) NOT NULL AFTER `money`;

CREATE TABLE `batchpay` (
  `id` INT(10) NOT NULL AUTO_INCREMENT,
  `sn` CHAR(32) NOT NULL,
  `admin_id` INT(10) NOT NULL COMMENT '操作人员',
  `total_amount` DECIMAL(10,2) NOT NULL COMMENT '批次提现总额',  
  `total_count` INT(1) NOT NULL COMMENT '总笔数',
  `payment_flag` TINYINT(1) NOT NULL COMMENT '代付标识0=普通代付1=支付账户余额代付',
  `is_launch` TINYINT(1) NOT NULL COMMENT '是否发起请求0未发起，1已发起',
  `remark` VARCHAR(96) DEFAULT '',
  `created_at` INT(10) NOT NULL,
  `updated_at` INT(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COMMENT='批量代付表';


CREATE TABLE `batchpay_item` (
  `id` INT(10) NOT NULL AUTO_INCREMENT,
  `batchpay_id` INT(10) NOT NULL COMMENT '批次id',
  `draw_id` INT(10) NOT NULL COMMENT '提现id',
  `uid` INT(10) NOT NULL COMMENT '用户id',
  `amount` DECIMAL(10,2) NOT NULL COMMENT '金额',
  `account_id` INT(10) NOT NULL COMMENT '账户id',
  `user_bank_id` INT(10) DEFAULT NULL COMMENT '用户绑卡的id',
  `bank_id` CHAR(10) NOT NULL COMMENT '本平台银行ID.',
  `pay_bank_id` CHAR(10) NOT NULL COMMENT '银行ID.参考中金银行编码，params配置文件中也可查找',
  `account_type` TINYINT(1) NOT NULL COMMENT '账户类型： 11=个人账户 12=企业账户',
  `account_name` VARCHAR(20) NOT NULL COMMENT '账户名称.',
  `account_number` VARCHAR(20) NOT NULL COMMENT '账户号码.',
  `branch_name` VARCHAR(96) NOT NULL COMMENT '分支行.',
  `province` VARCHAR(32) NOT NULL COMMENT '分支行省份.',
  `city` VARCHAR(32) NOT NULL COMMENT '分支行城市.',
  `phone_number` VARCHAR(16) DEFAULT '',
  `identification_type` CHAR(4) DEFAULT '0' COMMENT '开户证件类型 0=身份证 1=户口簿 2=护照 3=军官证 4=士兵证 5=港澳居民来往内地通行证 6=台湾同胞来往内地通行证 7=临时身份证 8=外国人居留证 9=警官证 X=其他证件',
  `identification_number` CHAR(32) DEFAULT '' COMMENT '证件号码',
  `status` TINYINT(1) DEFAULT '10' COMMENT '交易状态 10=未处理 20=正在处理 30=代付成功 40=代付失败',
  `banktxtime` CHAR(20) DEFAULT NULL COMMENT '回盘时间， 格式：yyyyMMddhh24mmss 当Status=30或40时，该项不空',
  `created_at` INT(10) NOT NULL,
  `updated_at` INT(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COMMENT='批量代付项目表';


  
ALTER TABLE `draw_record`   
  CHANGE `status` `status` TINYINT(2) UNSIGNED DEFAULT 0  NULL  COMMENT '状态 0-未处理 1-已审核 2-提现成功 3-提现不成功 4-已放款 5已经处理 11-提现驳回';
