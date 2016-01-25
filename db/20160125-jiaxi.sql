alter table `online_product` 
   add column `jiaxi` decimal(6,4) NULL COMMENT '加息利率（%）' after `yield_rate`;

alter table `online_product` 
   change `jiaxi` `jiaxi` decimal(3,1) NULL  comment '加息利率（%）';