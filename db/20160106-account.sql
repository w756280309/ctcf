ALTER TABLE `user_account`   
  ADD COLUMN `drawable_balance` DECIMAL(14,2) UNSIGNED DEFAULT 0.00  NULL  COMMENT '可提现金额' AFTER `profit_balance`;