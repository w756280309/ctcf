ALTER TABLE `online_product`   
  ADD COLUMN `is_xs` TINYINT(1) NOT NULL  COMMENT '是否新手标1是' AFTER `pcid`;
