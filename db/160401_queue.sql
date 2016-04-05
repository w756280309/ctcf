CREATE TABLE IF NOT EXISTS `OrderQueue` (
  `orderSn` varchar(30) NOT NULL COMMENT '订单sn',
  `status` tinyint(1) NOT NULL COMMENT '处理状态0未处理1处理',
  `updated_at` int(11) NOT NULL,
  `created_at` int(11) NOT NULL,
  PRIMARY KEY (`orderSn`),
  KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;