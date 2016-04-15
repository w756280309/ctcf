
DROP TABLE IF EXISTS `lenderStats`;

CREATE TABLE `lenderStats` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) NOT NULL,
  `created_at` int(10) NOT NULL,
  `updated_at` int(10) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `mobile` char(11) DEFAULT NULL,
  `idcard` varchar(50) DEFAULT NULL,
  `idcardStatus` tinyint(1) DEFAULT NULL,
  `mianmiStatus` tinyint(1) DEFAULT NULL,
  `bid` tinyint(1) DEFAULT NULL,
  `accountBalance` decimal(14,2) DEFAULT NULL,
  `rtotalFund` decimal(14,2) DEFAULT NULL,
  `rtotalNum` int(10) DEFAULT NULL,
  `dtotalFund` decimal(14,2) DEFAULT NULL,
  `dtotalNum` int(10) DEFAULT NULL,
  `ototalFund` decimal(14,2) DEFAULT NULL,
  `ototalNum` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
