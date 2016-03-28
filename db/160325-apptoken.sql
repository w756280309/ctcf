CREATE TABLE IF NOT EXISTS `AccessToken` (
  `uid` int(10) NOT NULL COMMENT '用户id',
  `expireTime` int(10) NOT NULL COMMENT '过期时间【暂定一个月】',
  `token` varchar(50) NOT NULL COMMENT '用户登录token',
  `clientType` char(10) NOT NULL COMMENT '客户端类型',
  `deviceName` varchar(50) NOT NULL COMMENT '设备名称',
  `clientInfo` varchar(100) NOT NULL COMMENT '客户端信息 ',
  `create_time` int(10) NOT NULL,
  PRIMARY KEY (`token`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='APP用户登录Token表';
