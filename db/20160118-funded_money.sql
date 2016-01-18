ALTER TABLE `online_product`   
  ADD COLUMN `funded_money` DECIMAL(14,2) NOT NULL  COMMENT '实际募集金额' AFTER `money`;