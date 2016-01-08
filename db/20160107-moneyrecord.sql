ALTER TABLE `money_record`   
  DROP COLUMN `status`;
  
ALTER TABLE `user_account`   
  ADD COLUMN `investment_balance` DECIMAL(14,2) UNSIGNED DEFAULT 0.00  NULL  COMMENT '理财金额' AFTER `profit_balance`;