ALTER TABLE `user` 
   ADD COLUMN `mianmiStatus` TINYINT(1) DEFAULT '0' NULL COMMENT '投资免密协议是否签署1签署0未签署' AFTER `finance_status`