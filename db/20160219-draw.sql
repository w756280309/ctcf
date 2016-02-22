ALTER TABLE `draw_record`   
  ADD COLUMN `fee` DECIMAL(4,2) NOT NULL  COMMENT '提现手续费' AFTER `money`;