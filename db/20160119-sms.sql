ALTER TABLE `sms_message`   
  ADD COLUMN `level` TINYINT(1) NOT NULL  COMMENT '1,2,3：1最高' AFTER `message`;