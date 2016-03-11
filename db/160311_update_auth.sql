ALTER TABLE `online_product` ADD COLUMN `recommendTime` int(10) DEFAULT NULL COMMENT '推荐时间' AFTER `is_xs`;
insert into `auth` ( `auth_name`, `auth_description`, `created_at`, `psn`, `path`, `type`, `level`, `order_code`, `updated_at`, `sn`, `status`) values ( '推荐/取消推荐', '推荐/取消推荐', null, 'P200100', 'product/productonline/recommend', '2', '3', '2', null, 'P200113', '1');
