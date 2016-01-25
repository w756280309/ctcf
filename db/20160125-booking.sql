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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='预约记录表';

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