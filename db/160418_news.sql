DROP TABLE IF EXISTS `news`;
CREATE TABLE `news` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL COMMENT '新闻标题',
  `summary` varchar(200) DEFAULT NULL  COMMENT '新闻概括',
  `child_title` varchar(100) DEFAULT NULL COMMENT '新闻副标题',
  `image` varchar(250) DEFAULT NULL COMMENT '内容图片',
  `source` varchar(100) NOT NULL COMMENT '内容来源',
  `category_id` int(10) unsigned NOT NULL COMMENT '分类id',
  `creator_id` int(10) unsigned NOT NULL COMMENT '创建者管理员id',
  `status` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '状态 0-草稿 1-正常 3-删除',
  `home_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否在首页显示 0-不显示 1-显示',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '序号',
  `body` text NOT NULL COMMENT '新闻内容',
  `news_time` int(10) NOT NULL COMMENT '新闻发布时间',
  `attach_file` varchar(255) NOT NULL COMMENT '关联附件',
  `updated_at` int(10) NOT NULL,
  `created_at` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='新闻表';

