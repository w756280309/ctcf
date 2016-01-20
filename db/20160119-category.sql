ALTER TABLE `online_product`   
  ADD COLUMN `is_xs` TINYINT(1) NOT NULL  COMMENT '是否新手标1是' AFTER `pcid`;

update online_product set is_xs=0;
update online_product set cid=1;