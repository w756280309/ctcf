DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL COMMENT '分类名称',
  `parent_id` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '上级分类id',
  `level` TINYINT(1) NOT NULL DEFAULT '1' COMMENT '分类层级 ',
  `description` CHAR(128) NOT NULL DEFAULT '' COMMENT '描述',
  `sort` INT(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '分类序号',
  `status` TINYINT(1) NOT NULL DEFAULT '1' COMMENT '状态 0-锁定 1-正常 ',
  `type` TINYINT(1) NOT NULL DEFAULT '1' COMMENT '分类类型 1-资讯分类 ',
  `updated_at` INT(10) NOT NULL COMMENT '更新时间',
  `created_at` INT(10) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8 COMMENT='公共分类表';

DROP TABLE IF EXISTS `item_category`;
CREATE TABLE `item_category` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `item_id` INT(10) UNSIGNED NOT NULL COMMENT '项目ID',
  `category_id` INT(10) UNSIGNED NOT NULL COMMENT '分类ID',
   `type` TINYINT(1) NOT NULL DEFAULT '1' COMMENT '分类类型 1-资讯分类 ',
  `updated_at` INT(10) NOT NULL COMMENT '更新时间',
  `created_at` INT(10) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MYISAM DEFAULT CHARSET=utf8 COMMENT='项目、分类对照表';
