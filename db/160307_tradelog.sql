CREATE TABLE IF NOT EXISTS `TradeLog` (
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='交易日志表'