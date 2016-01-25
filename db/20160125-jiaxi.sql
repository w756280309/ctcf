alter table `online_product` 
   add column `jiaxi` decimal(6,4) NULL COMMENT '加息利率（%）' after `yield_rate`;