CREATE TABLE `sms_message` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `uid` INT(11) NOT NULL COMMENT 'uid',
  `template_id` INT(1) NOT NULL COMMENT '短信模板id',
  `mobile` VARCHAR(11) NOT NULL COMMENT '手机号',
  `message` VARCHAR(300) NOT NULL COMMENT '短信内容,json',
  `status` TINYINT(1) DEFAULT '0' COMMENT '状态0未发送，1已发送',
  `created_at` INT(11) DEFAULT NULL COMMENT '创建时间',
  `updated_at` INT(11) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8