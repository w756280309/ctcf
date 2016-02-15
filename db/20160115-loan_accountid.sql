ALTER TABLE `online_product`   
  ADD COLUMN `epayLoanAccountId` VARCHAR(15) NOT NULL  COMMENT '标的在托管平台的账户号' AFTER `id`;