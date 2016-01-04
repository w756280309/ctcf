CREATE TABLE `login_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `ip` varchar(30) NOT NULL COMMENT 'IP地址',
  `type` tinyint(1) NOT NULL COMMENT '渠道类型：1代表前台wap;2代表前台pc端;3代表后端控制台',
  `user_name` varchar(30) NOT NULL COMMENT '用户登陆名',
  `updated_at` int(11) DEFAULT NULL COMMENT '记录更新时间',
  `created_at` int(11) NOT NULL COMMENT '记录创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=utf8 COMMENT='登陆错误日志表';