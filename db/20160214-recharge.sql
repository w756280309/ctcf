ALTER TABLE `recharge_record`   
  ADD COLUMN `epayUserId` VARCHAR(60) NOT NULL COMMENT '托管平台用户号' AFTER `fund`,
  ADD COLUMN `clientIp` INT(10) NOT NULL COMMENT 'ip地址' AFTER `epayUserId`;