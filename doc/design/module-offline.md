# Offline Module:

## Summary:
* Tables Structure
* Models
* Current Functions

> Tables Structure

```
线下用户表 offline_user
```
```
CREATE TABLE `offline_user` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `realName` varchar(50) NOT NULL,
 `mobile` varchar(20) NOT NULL,
 `idCard` varchar(30) NOT NULL,
 `points` int(11) DEFAULT '0',
 `annualInvestment` decimal(14,2) NOT NULL DEFAULT '0.00',
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
```

```
线下标的表 offline_loan
``` 
```
CREATE TABLE `offline_loan` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `title` varchar(255) NOT NULL,
 `expires` smallint(6) NOT NULL,
 `unit` varchar(20) DEFAULT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8

```

```
线下标的订单表 offline_order
```
```
CREATE TABLE `offline_order` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `affiliator_id` int(10) NOT NULL,
 `loan_id` int(10) NOT NULL,
 `realName` varchar(50) NOT NULL,
 `mobile` varchar(20) NOT NULL,
 `money` decimal(14,2) NOT NULL,
 `orderDate` date NOT NULL,
 `created_at` int(10) NOT NULL,
 `isDeleted` tinyint(1) NOT NULL,
 `user_id` int(11) NOT NULL,
 `idCard` varchar(30) NOT NULL,
 `accBankName` varchar(255) NOT NULL,
 `bankCardNo` varchar(30) NOT NULL,
 `valueDate` date DEFAULT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8
```

```
线下统计数据表 offline_stats
```
```
CREATE TABLE `offline_stats` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `tradedAmount` decimal(14,2) NOT NULL,
 `refundedPrincipal` decimal(14,2) NOT NULL,
 `refundedInterest` decimal(14,2) NOT NULL,
 `createTime` datetime NOT NULL,
 `updateTime` datetime DEFAULT NULL,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8

```

> Models
- File Directory
    - wdjf/common/models/offline
- Model Instruction
    - Models Files
        - ImportForm.php &nbsp;&nbsp;用于线下数据->导入新数据
        - OfflineLoan.php &nbsp;&nbsp;AR Model With Table `offline_loan`
        - OfflineOrder.php &nbsp;&nbsp;AR Model With Table `offline_order`
        - OfflinePointManager.php &nbsp;&nbsp;线下积分管理类-提供了根据订单和类型更新积分的公共方法
        - OfflineStats.php &nbsp;&nbsp;AR Model With Table `offline_stats`
        - OfflineUser.php &nbsp;&nbsp;AR Model With Table `offline_user`
        - OfflineUserManager.php &nbsp;&nbsp;线下会员管理类-提供了根据线下订单更新用户的累计年化投资金额及记录财富值流水的公共方法
    
> Current Functions
- 线下数据>线下数据
    - 筛选项：分销商，产品名称，姓名，联系方式
- 线下数据>线下数据>编辑线下数据统计项（数据项用于前台首页展示）
- 线下数据>线下数据>导入新数据
- 线下数据>线下数据>编辑（客户姓名，证件号，开户行名称，银行卡账号）
- 线下数据>线下数据>确认计息（起息日期）
- 线下数据>线下数据>删除（isDeleted = true）
- 会员管理>线下会员>线下会员列表
    - 筛选项：真实姓名，手机号码（最新导入的那个）
- 线上会员详情-线下会员
- 分销后台>首页（线下总交易额，线下交易人数，今日线下交易额，交易人数）
- 分销后台>线下交易记录
    - 筛选项：认购时间（全部，今日，一个月）