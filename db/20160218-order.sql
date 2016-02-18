ALTER TABLE `online_order`   
  CHANGE `status` `status` TINYINT(2) NOT NULL  COMMENT '0--投标失败---1-投标成功 2.撤标 3，无效';