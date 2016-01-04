ALTER TABLE `recharge_record`   
  ADD COLUMN `pay_type` TINYINT(1) NOT NULL  COMMENT '1快捷充值,2网银充值' AFTER `sn`;
  
UPDATE recharge_record SET pay_type=1;-- 将所有的充值类型变为快捷充值