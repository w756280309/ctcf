-- --------------------------------------------------------
-- Host:                         rr-bp1z450fa58j6w0tv.mysql.rds.aliyuncs.com
-- Server version:               5.6.16-log - Source distribution
-- Server OS:                    Linux
-- HeidiSQL Version:             9.5.0.5196
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Dumping structure for table tx.credit_note
CREATE TABLE IF NOT EXISTS `credit_note` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(14,0) NOT NULL,
  `tradedAmount` decimal(14,0) NOT NULL,
  `discountRate` decimal(5,2) NOT NULL,
  `isClosed` tinyint(1) NOT NULL,
  `isCancelled` tinyint(1) NOT NULL,
  `config` text NOT NULL,
  `isTest` tinyint(1) NOT NULL,
  `createTime` datetime NOT NULL,
  `endTime` datetime NOT NULL,
  `closeTime` datetime DEFAULT NULL,
  `cancelTime` datetime DEFAULT NULL,
  `loan_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `isManualCanceled` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5705 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table tx.credit_order
CREATE TABLE IF NOT EXISTS `credit_order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `note_id` int(11) NOT NULL,
  `asset_id` int(11) NOT NULL,
  `amount` decimal(14,0) NOT NULL,
  `fee` decimal(14,0) NOT NULL,
  `principal` decimal(14,0) NOT NULL,
  `interest` decimal(14,0) NOT NULL,
  `status` smallint(6) NOT NULL,
  `createTime` datetime NOT NULL,
  `updateTime` datetime DEFAULT NULL,
  `buyerPaymentStatus` smallint(6) NOT NULL,
  `sellerRefundStatus` smallint(6) NOT NULL,
  `feeTransferStatus` smallint(6) NOT NULL,
  `buyerAmount` decimal(14,0) DEFAULT NULL,
  `sellerAmount` decimal(14,0) DEFAULT NULL,
  `settleTime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3799 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table tx.migration
CREATE TABLE IF NOT EXISTS `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table tx.settle
CREATE TABLE IF NOT EXISTS `settle` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `txSn` varchar(60) NOT NULL,
  `fee` decimal(14,0) DEFAULT NULL,
  `amount` decimal(14,0) DEFAULT NULL,
  `txType` smallint(6) NOT NULL,
  `txDate` date DEFAULT NULL,
  `fcFee` decimal(14,0) DEFAULT NULL,
  `fcAmount` decimal(14,0) DEFAULT NULL,
  `fcDate` date DEFAULT NULL,
  `fcSn` varchar(60) DEFAULT NULL,
  `settleDate` date DEFAULT NULL,
  `isChecked` tinyint(1) NOT NULL,
  `isSettled` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `txSn` (`txSn`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table tx.transfer
CREATE TABLE IF NOT EXISTS `transfer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sn` varchar(60) NOT NULL,
  `type` varchar(60) NOT NULL,
  `amount` decimal(14,0) NOT NULL,
  `fromAccount` varchar(60) NOT NULL,
  `toAccount` varchar(60) NOT NULL,
  `sourceType` varchar(60) DEFAULT NULL,
  `sourceTxSn` varchar(60) DEFAULT NULL,
  `status` varchar(60) DEFAULT NULL,
  `createTime` datetime NOT NULL,
  `updateTime` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sn` (`sn`)
) ENGINE=InnoDB AUTO_INCREMENT=10109 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Dumping structure for table tx.user_asset
CREATE TABLE IF NOT EXISTS `user_asset` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `loan_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `isRepaid` tinyint(1) NOT NULL,
  `amount` decimal(14,0) NOT NULL,
  `orderTime` datetime NOT NULL,
  `createTime` datetime NOT NULL,
  `updateTime` datetime DEFAULT NULL,
  `asset_id` int(11) DEFAULT NULL,
  `tradeCount` smallint(6) DEFAULT NULL,
  `maxTradableAmount` decimal(14,0) DEFAULT NULL,
  `isTrading` tinyint(1) NOT NULL,
  `isTest` tinyint(1) NOT NULL,
  `note_id` int(11) DEFAULT NULL,
  `isInvalid` tinyint(1) NOT NULL,
  `credit_order_id` int(11) DEFAULT NULL,
  `allowTransfer` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=175512 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
