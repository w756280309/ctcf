<?php

namespace common\models\stats;

use common\models\coupon\CouponType;
use common\models\coupon\UserCoupon;
use common\models\growth\AppMeta;
use common\models\offline\OfflineStats;
use common\models\payment\Repayment;
use common\models\product\OnlineProduct;
use yii\db\ActiveRecord;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "perf".
 *
 * @property integer $id
 * @property string $bizDate 日期
 * @property integer $uv
 * @property integer $pv
 * @property double $bounceRate
 * @property integer $reg   注册用户数
 * @property double $regConv
 * @property integer $idVerified    实名认证
 * @property integer $qpayEnabled   绑卡
 * @property integer $investor  投资人数
 * @property integer $newInvestor   新增投资人数, 非当日注册当日投资
 * @property integer $newRegisterAndInvestor   当日注册当日投资人数
 * @property double $newRegAndNewInveAmount 当日注册当日投资金额
 * @property double $preRegAndNewInveAmount 非当日注册当日投资金额
 * @property string $chargeViaPos   POS充值
 * @property string $chargeViaEpay  线上充值
 * @property string $drawAmount 提现
 * @property string $investmentInWyj    温盈金
 * @property string $investmentInWyb    温盈宝
 * @property string $onlineInvestment    线上投资金额
 * @property string $offlineInvestment    线下投资金额
 * @property string $totalInvestment    总投资金额
 * @property integer $successFound    融资项目
 * @property string $remainMoney    贷后余额
 * @property string $usableMoney    可用余额
 * @property string $rechargeMoney    充值金额
 * @property string $rechargeCost    充值手续费
 * @property string $draw    提现
 * @property integer $created_at    统计时间
 * @property integer $investAndLogin    已投用户登录
 * @property integer $notInvestAndLogin 未投用户登录
 * @property double  $repayMoney          回款金额
 * @property int     $repayLoanCount      回款项目数
 * @property int     $repayUserCount      回款人数
 * @property int    $licaiNewInvCount   理财计划新增投资人数
 * @property double $licaiNewInvSum     理财计划新增投资用户的投资金额
 * @property int    $licaiInvCount      理财计划的总投资人数
 * @property double $licaiInvSum        理财计划的总投资金额
 * @property int    $xsNewInvCount      新手标的新增投资人数
 * @property double $xsNewInvSum        新手标的新增投资用户的投资金额
 * @property int    $xsInvCount         新手标的总投资人数
 * @property double $xsInvSum           新手标的总投资金额
 * @property int    $checkIn            签到人数
 */
class Perf extends ActiveRecord
{
    private $loanOrderUids;
    private $visitorUids;

    /**
     * @return yii\db\Connection
     */
    private static function getDbRead()
    {
        return Yii::$app->db_read;
    }

    //获取统计开始时间
    public static function getStartDate()
    {
        $date = self::getDbRead()->createCommand('SELECT MIN(DATE(FROM_UNIXTIME(created_at))) FROM user WHERE type=1')
            ->queryScalar();
        return $date;
    }

    //获取统计结束时间
    public static function getEndDate()
    {
        return (new \DateTime('-1 day'))->format('Y-m-d');
    }

    //获取上次统计时间
    public static function getLastTime()
    {
        $time = self::getDbRead()->createCommand('SELECT MAX(created_at) FROM perf')->queryScalar();
        if (null === $time) {
            return time();
        } else {
            return $time;
        }
    }

    //注册数
    public function getReg($date)
    {
        return self::getDbRead()->createCommand('SELECT COUNT(id) FROM user WHERE type=1 AND DATE(FROM_UNIXTIME(created_at))=:date')
            ->bindValue('date', $date, \PDO::PARAM_STR)
            ->queryScalar();
    }

    //实名认证
    public function getIdVerified($date)
    {
        return self::getDbRead()->createCommand('SELECT COUNT(id) FROM epayuser WHERE regDate=:date')
            ->bindValue('date', $date, \PDO::PARAM_STR)
            ->queryScalar();
    }

    //绑卡
    public function getQpayEnabled($date)
    {
        return self::getDbRead()->createCommand('SELECT COUNT(id) FROM user_bank WHERE DATE(FROM_UNIXTIME(created_at))=:date')
            ->bindValue('date', $date, \PDO::PARAM_STR)
            ->queryScalar();
    }

    //投资人数
    public function getInvestor($date)
    {
        return self::getDbRead()->createCommand('SELECT COUNT(DISTINCT(o.`uid`)) FROM online_order AS o INNER JOIN online_product AS p ON o.`online_pid` = p.`id` WHERE o.`status`=1 AND p.`isTest` = 0 AND DATE(FROM_UNIXTIME(o.created_at))=:date')
            ->bindValue('date', $date, \PDO::PARAM_STR)
            ->queryScalar();
    }

    //新增投资人数（以前注册未投资，但今日投资了的）
    public function getNewInvestor($date)
    {
        $totalInvestor = self::getDbRead()->createCommand('SELECT COUNT(DISTINCT(o.uid)) FROM online_order AS o LEFT JOIN `user` AS u ON o.uid = u.id INNER JOIN online_product AS p ON o.online_pid = p.id WHERE o.`status`= 1 AND p.isTest = 0 AND DATE(FROM_UNIXTIME(u.`created_at`)) < :date AND DATE(FROM_UNIXTIME(o.created_at))<=:date')
            ->bindValue('date', $date, \PDO::PARAM_STR)
            ->queryScalar();
        $investor = self::getDbRead()->createCommand('SELECT COUNT(DISTINCT(o.uid)) FROM online_order AS o LEFT JOIN `user` AS u ON o.uid = u.id INNER JOIN online_product AS p ON o.online_pid = p.id WHERE o.`status`= 1 AND p.isTest = 0 AND DATE(FROM_UNIXTIME(u.`created_at`)) < :date AND DATE(FROM_UNIXTIME(o.created_at)) <:date')
            ->bindValue('date', $date, \PDO::PARAM_STR)
            ->queryScalar();

        return $totalInvestor - $investor;
    }

    //当日注册当日投资人数
    public function getNewRegisterAndInvestor($date)
    {
        $investor = self::getDbRead()->createCommand('SELECT COUNT(DISTINCT(o.uid)) FROM online_order AS o LEFT JOIN `user` AS u ON o.uid = u.id INNER JOIN online_product AS p ON o.`online_pid` = p.`id` WHERE o.`status`= 1 AND p.isTest = 0 AND DATE(FROM_UNIXTIME(u.`created_at`)) = :date AND DATE(FROM_UNIXTIME(o.created_at)) = :date')
            ->bindValue('date', $date, \PDO::PARAM_STR)
            ->queryScalar();

        return $investor;
    }

    //当日注册当日投资金额
    public function getNewRegAndNewInveAmount($date)
    {
        $sql = "SELECT SUM( o.order_money ) 
FROM online_order AS o
INNER JOIN  `user` AS u ON o.uid = u.id
INNER JOIN online_product AS p ON o.`online_pid` = p.`id` 
WHERE o.`status` =1
AND p.isTest =0
AND DATE( FROM_UNIXTIME( u.`created_at` ) ) =  :date
AND DATE( FROM_UNIXTIME( o.created_at ) ) =  :date";
        return self::getDbRead()->createCommand($sql, ['date' => $date])->queryScalar();
    }

    //非当日注册当日投资金额
    public function getPreRegAndNewInveAmount($date)
    {
        $ids = $this->getDayNewInvestor($date);// 获取以前未投资今日投资的用户数
        if (empty($ids)) {
            return 0;
        } else {
            $sql = "SELECT SUM( o.order_money ) 
FROM online_order AS o
INNER JOIN  `user` AS u ON o.uid = u.id
INNER JOIN online_product AS p ON o.`online_pid` = p.`id` 
WHERE o.`status` =1
AND p.isTest =0
AND u.id
IN ( ". implode(', ', $ids) ." ) 
AND DATE( FROM_UNIXTIME( o.created_at ) ) =  :date";
            return self::getDbRead()->createCommand($sql, ['date' => $date])->queryScalar();
        }
    }

    //理财计划（胜券在握）新增投资人数（包括当日注册当日投资和非当日注册当日投资的，投资胜券在握前未投资过的）
    public function getLicaiNewInvCount($date)
    {
        $sql = "SELECT COUNT(DISTINCT o.uid)
FROM online_order AS o
INNER JOIN user AS u ON o.uid = u.id
INNER JOIN online_product AS p ON o.online_pid = p.id
WHERE o.status =1
AND p.isTest =0
AND u.type = 1
AND DATE( FROM_UNIXTIME( o.created_at ) ) = :date
AND o.uid NOT IN (
SELECT DISTINCT o.uid
FROM online_order AS o
INNER JOIN user AS u ON o.uid = u.id
INNER JOIN online_product AS p ON o.online_pid = p.id
WHERE o.status =1
AND p.isTest =0
AND u.type = 1
AND DATE( FROM_UNIXTIME( o.created_at ) ) <  :date
)
AND p.isLicai = 1
";
        return self::getDbRead()->createCommand($sql, ['date' => $date])->queryScalar();
    }
    //理财计划（胜券在握）新增用户的投资金额
    public function getLicaiNewInvSum($date)
    {
        $sql = "SELECT SUM(o.order_money)
FROM online_order AS o
INNER JOIN user AS u ON o.uid = u.id
INNER JOIN online_product AS p ON o.online_pid = p.id
WHERE o.status =1
AND p.isTest =0
AND u.type = 1
AND DATE( FROM_UNIXTIME( o.created_at ) ) = :date
AND o.uid NOT IN (
SELECT DISTINCT o.uid
FROM online_order AS o
INNER JOIN user AS u ON o.uid = u.id
INNER JOIN online_product AS p ON o.online_pid = p.id
WHERE o.status =1
AND p.isTest =0
AND u.type = 1
AND DATE( FROM_UNIXTIME( o.created_at ) ) <  :date
)
AND p.isLicai = 1
";
        return self::getDbRead()->createCommand($sql, ['date' => $date])->queryScalar();
    }

    //理财计划（胜券在握）当日总投人数
    public function getLicaiInvCount($date)
    {
        $sql = "SELECT COUNT(DISTINCT o.uid)
FROM online_order AS o
INNER JOIN user AS u ON o.uid = u.id
INNER JOIN online_product AS p ON o.online_pid = p.id
WHERE o.status =1
AND p.isTest =0
AND u.type = 1
AND DATE( FROM_UNIXTIME( o.created_at ) ) = :date
AND p.isLicai = 1
";
        return self::getDbRead()->createCommand($sql, ['date' => $date])->queryScalar();
    }
    //理财计划（胜券在握）当日总投资金额
    public function getLicaiInvSum($date)
    {
        $sql = "SELECT SUM(o.order_money)
FROM online_order AS o
INNER JOIN user AS u ON o.uid = u.id
INNER JOIN online_product AS p ON o.online_pid = p.id
WHERE o.status =1
AND p.isTest =0
AND u.type = 1
AND DATE( FROM_UNIXTIME( o.created_at ) ) = :date
AND p.isLicai = 1
";
        return self::getDbRead()->createCommand($sql, ['date' => $date])->queryScalar();
    }

    //新手标新增投资人数（包括当日注册当日投资和非当日注册当日投资的，投资新手标前未投资过的）
    public function getXsNewInvCount($date)
    {
        $sql = "SELECT COUNT(DISTINCT o.uid)
FROM online_order AS o
INNER JOIN user AS u ON o.uid = u.id
INNER JOIN online_product AS p ON o.online_pid = p.id
WHERE o.status =1
AND p.isTest =0
AND u.type = 1
AND DATE( FROM_UNIXTIME( o.created_at ) ) = :date
AND o.uid NOT IN (
SELECT DISTINCT o.uid
FROM online_order AS o
INNER JOIN user AS u ON o.uid = u.id
INNER JOIN online_product AS p ON o.online_pid = p.id
WHERE o.status =1
AND p.isTest =0
AND u.type = 1
AND DATE( FROM_UNIXTIME( o.created_at ) ) <  :date
)
AND p.is_xs = 1
";
        return self::getDbRead()->createCommand($sql, ['date' => $date])->queryScalar();
    }
    //新手标新增用户的投资金额
    public function getXsNewInvSum($date)
    {
        $sql = "SELECT SUM(o.order_money)
FROM online_order AS o
INNER JOIN user AS u ON o.uid = u.id
INNER JOIN online_product AS p ON o.online_pid = p.id
WHERE o.status =1
AND p.isTest =0
AND u.type = 1
AND DATE( FROM_UNIXTIME( o.created_at ) ) = :date
AND o.uid NOT IN (
SELECT DISTINCT o.uid
FROM online_order AS o
INNER JOIN user AS u ON o.uid = u.id
INNER JOIN online_product AS p ON o.online_pid = p.id
WHERE o.status =1
AND p.isTest =0
AND u.type = 1
AND DATE( FROM_UNIXTIME( o.created_at ) ) <  :date
)
AND p.is_xs = 1
";
        return self::getDbRead()->createCommand($sql, ['date' => $date])->queryScalar();
    }

    //新手标当日总投人数
    public function getXsInvCount($date)
    {
        $sql = "SELECT COUNT(DISTINCT o.uid)
FROM online_order AS o
INNER JOIN user AS u ON o.uid = u.id
INNER JOIN online_product AS p ON o.online_pid = p.id
WHERE o.status =1
AND p.isTest =0
AND u.type = 1
AND DATE( FROM_UNIXTIME( o.created_at ) ) = :date
AND p.is_xs = 1
";
        return self::getDbRead()->createCommand($sql, ['date' => $date])->queryScalar();
    }
    //新手标当日总投资金额
    public function getXsInvSum($date)
    {
        $sql = "SELECT SUM(o.order_money)
FROM online_order AS o
INNER JOIN user AS u ON o.uid = u.id
INNER JOIN online_product AS p ON o.online_pid = p.id
WHERE o.status =1
AND p.isTest =0
AND u.type = 1
AND DATE( FROM_UNIXTIME( o.created_at ) ) = :date
AND p.is_xs = 1
";
        return self::getDbRead()->createCommand($sql, ['date' => $date])->queryScalar();
    }


    //POS充值
    public function getChargeViaPos($date)
    {
        return self::getDbRead()->createCommand('SELECT SUM(r.fund) FROM recharge_record r LEFT JOIN user u ON r.uid=u.id WHERE r.status=1 AND r.pay_type=3 AND u.type=1 AND DATE(FROM_UNIXTIME(r.created_at))=:date')
            ->bindValue('date', $date, \PDO::PARAM_STR)
            ->queryScalar();
    }

    //线上充值
    public function getChargeViaEpay($date)
    {
        return self::getDbRead()->createCommand('SELECT SUM(r.fund) FROM recharge_record r LEFT JOIN user u ON r.uid=u.id WHERE r.status=1 AND r.pay_type<>3 AND u.type=1 AND DATE(FROM_UNIXTIME(r.created_at))=:date')
            ->bindValue('date', $date, \PDO::PARAM_STR)
            ->queryScalar();
    }

    //提现
    public function getDrawAmount($date)
    {
        return self::getDbRead()->createCommand('SELECT SUM(r.money) FROM draw_record r LEFT JOIN user u on r.uid=u.id WHERE r.status=2 and u.type=1 and DATE(FROM_UNIXTIME(r.created_at))=:date')
            ->bindValue('date', $date, \PDO::PARAM_STR)
            ->queryScalar();
    }

    //温盈金
    public function getInvestmentInWyj($date)
    {
        return self::getDbRead()->createCommand('select sum(o.order_money) from online_order o left join online_product l on o.online_pid=l.id where l.cid=1 and l.isTest=0 and o.status=1 and DATE(FROM_UNIXTIME(o.created_at))=:date')
            ->bindValue('date', $date, \PDO::PARAM_STR)
            ->queryScalar();
    }

    //温盈宝
    public function getInvestmentInWyb($date)
    {
        return self::getDbRead()->createCommand('select sum(o.order_money) from online_order o left join online_product l on o.online_pid=l.id where l.cid=2 and l.isTest = 0 and o.status=1 and DATE(FROM_UNIXTIME(o.created_at))=:date')
            ->bindValue('date', $date, \PDO::PARAM_STR)
            ->queryScalar();
    }

    //线上交易额
    public function getOnlineInvestment($date)
    {

        return self::getDbRead()->createCommand('select sum(o.order_money) from online_order o INNER JOIN online_product AS p ON o.`online_pid` = p.`id` where o.status=1 AND p.isTest = 0 and DATE(FROM_UNIXTIME(o.created_at))=:date')
            ->bindValue('date', $date, \PDO::PARAM_STR)
            ->queryScalar();
    }

    //线下交易额(以元为单位)
    public function getOfflineInvestment($date)
    {
        return self::getDbRead()->createCommand('SELECT SUM( money ) * 10000 FROM offline_order WHERE orderDate =:date AND `isDeleted` = 0')
            ->bindValue('date', $date, \PDO::PARAM_STR)
            ->queryScalar();
    }

    //线上+线下累计投资金额
    public function getTotalInvestment($date)
    {
        return bcadd($this->getOnlineInvestment($date), $this->getOfflineInvestment($date), 2);
    }

    //融资项目,成立及之后的项目，status [5,6,7],项目成立时间按照 full_time
    public function getSuccessFound($date)
    {
        $sql = 'SELECT COUNT(*) FROM online_product WHERE `status` IN (5,6,7) AND isTest = 0 AND DATE(FROM_UNIXTIME(full_time))=:date;';
        return self::getDbRead()->createCommand($sql, ['date' => $date])->queryScalar();
    }

    //贷后余额,募集了但是还没有还款的那部分资金(只是已经成立但是还没有还款的项目,不包含募集中)，status [5,7]
    public static function getRemainMoney()
    {
        $sql = 'SELECT SUM(funded_money) FROM online_product WHERE `status` IN(5,7) AND isTest = 0';
            return self::getDbRead()->createCommand($sql)->queryScalar();
    }

    /**
     * 线上年化累计交易额
     * 到期本息  交易额×项目期限/365
     * 非到期本息：交易额×项目期限/12
     */
    public static function getOnlineAnnualTotalInvestment()
    {
        $sql = 'SELECT SUM(o.order_money * p.expires / if (p.refund_method = 1, 365, 12)) 
              FROM online_order o 
              INNER JOIN online_product p 
              ON o.online_pid = p.id 
              WHERE o.status = 1 
              AND p.isTest = 0';
        return self::getDbRead()->createCommand($sql)->queryScalar();
    }

    /**
     * 线下年化累计交易额
     * 以天为单位： 交易额×项目期限/365
     * 以月为单位：交易额×项目期限/12
     */
    public static function getOfflineAnnualTotalInvestment()
    {
        $sql = 'SELECT SUM(o.money * l.expires / if (l.unit = "天", 365 , 12)) * 10000 
            FROM offline_order o 
            INNER JOIN offline_loan l 
            ON o.loan_id = l.id 
            WHERE o.isDeleted = 0';
        return self::getDbRead()->createCommand($sql)->queryScalar();
    }

    /**
     * 贷后年化余额，募集结束和收益中正式标的的贷后余额 总值为=到期本息年化余额+非到期本息年化余额
     * 到期本息贷后年化余额=投资金额×项目期限/365
     * 非到期本息贷后年化余额=投资金额×项目期限/12
     */
    public static function getAnnualInvestment()
    {
        $productCond = 'CASE refund_method WHEN 1 THEN 365 ELSE 12 END'; //还款方式为到期本息时为365天，非到期本息时为12个月
        $sql = 'SELECT 
            SUM(funded_money * expires / ' . $productCond . ' ) 
            FROM online_product WHERE `status` IN(5,7) AND isTest = 0';
        return self::getDbRead()->createCommand($sql)->queryScalar();
    }


    //可用余额,网站所有用户的可用余额总和
    public static function getUsableMoney()
    {
        $sql = 'SELECT SUM(a.available_balance) FROM user_account AS a LEFT JOIN `user` AS u ON a.uid = u.id WHERE u.type = 1';
        return self::getDbRead()->createCommand($sql)->queryScalar();
    }

    //充值金额，先下+线上
    public function getRechargeMoney($date)
    {
        return floatval($this->chargeViaPos) + floatval($this->chargeViaEpay);
    }

    //充值手续费，快捷千分之1.2，网银千分之1.8，线下pos充值手续费为1.25%，80封顶
    public function getRechargeCost($date)
    {
        //快捷充值
        $sql = "SELECT SUM(r.fund * 0.0012) FROM recharge_record AS r LEFT JOIN `user` AS u ON r.uid = u.id WHERE r.status = 1 AND r.pay_type = 1 AND u.type = 1 AND DATE(FROM_UNIXTIME(r.created_at)) = :date";
        $k = self::getDbRead()->createCommand($sql, ['date' => $date])->queryScalar();
        //网银充值
        $sql = "SELECT SUM(r.fund * 0.0018) FROM recharge_record AS r LEFT JOIN `user` AS u ON r.uid = u.id WHERE r.status = 1 AND r.pay_type = 2 AND u.type = 1 AND DATE(FROM_UNIXTIME(r.created_at)) = :date";
        $w = self::getDbRead()->createCommand($sql, ['date' => $date])->queryScalar();
        //POS充值
        $sql = "SELECT SUM(LEAST(r.fund * 0.0125,80))  FROM recharge_record r LEFT JOIN user u ON r.uid=u.id WHERE r.status=1 AND r.pay_type=3 AND u.type=1 AND DATE(FROM_UNIXTIME(r.created_at))=:date";
        $pos = self::getDbRead()->createCommand($sql, ['date' => $date])->queryScalar();
        return floatval($k) + floatval($w) + floatval($pos);
    }

    //提现
    public function getDraw($date)
    {
        $sql = 'SELECT SUM(money) FROM draw_record AS r LEFT JOIN `user` AS u ON r.uid = u.id WHERE r.`status` = 2 AND u.type = 1 AND DATE(FROM_UNIXTIME(r.created_at)) = :date';
        return self::getDbRead()->createCommand($sql, ['date' => $date])->queryScalar();
    }

    //获取已投用户登录
    public function getInvestAndLogin($date)
    {
        if (is_null($this->loanOrderUids) || !isset($this->loanOrderUids[$date])) {
            $sql = "select distinct uid from online_order as o where o.status = 1 and date(from_unixtime(o.order_time)) <= :date";
            $res = self::getDbRead()->createCommand($sql, [
                'date' => $date,
            ])->queryAll();
            $this->loanOrderUids[$date] = array_column($res, 'uid');
        }
        $orderUids = $this->loanOrderUids[$date];
        if (is_null($this->visitorUids) || !isset($this->visitorUids[$date])) {
            $this->visitorUids[$date] = Piwik::getVisitorId($date);
        }
        $visitorIds = $this->visitorUids[$date];

        return count(array_intersect($visitorIds, $orderUids));

    }

    //获取未投有用户登录
    public function getNotInvestAndLogin($date)
    {
        if (is_null($this->loanOrderUids) || !isset($this->loanOrderUids[$date])) {
            $sql = "select distinct uid from online_order as o where o.status = 1 and date(from_unixtime(o.order_time)) <= :date";
            $res = self::getDbRead()->createCommand($sql, [
                'date' => $date,
            ])->queryAll();
            $this->loanOrderUids[$date] = array_column($res, 'uid');
        }
        $orderUids = $this->loanOrderUids[$date];
        if (is_null($this->visitorUids) || !isset($this->visitorUids[$date])) {
            $this->visitorUids[$date] = Piwik::getVisitorId($date);
        }
        $visitorIds = $this->visitorUids[$date];

        return count(array_diff($visitorIds, $orderUids));
    }

    //获取代金券
    /**
     * @param integer $type 1表示已使用，0表示未使用，null 表示已发放
     * @return int
     */
    public static function getCoupon($type = null)
    {
        if (null !== $type && in_array($type, [0, 1])) {
            $sql = "SELECT couponType_id AS cid,COUNT(couponType_id) AS cou FROM user_coupon WHERE isUsed = " . $type . " GROUP BY couponType_id";
        } else {
            $sql = "SELECT couponType_id AS cid,COUNT(couponType_id) AS cou FROM user_coupon GROUP BY couponType_id";
        }
        $result = self::getDbRead()->createCommand($sql)->queryAll();
        if (count($result) > 0) {
            $num = ArrayHelper::map($result, 'cid', 'cou');
        } else {
            return 0;
        }
        $cids = '(' . implode(',', ArrayHelper::getColumn($result, 'cid')) . ')';
        $sql = "SELECT id,amount FROM coupon_type WHERE id in " . $cids;
        $result = self::getDbRead()->createCommand($sql)->queryAll();
        if (count($result) > 0) {
            $amount = ArrayHelper::map($result, 'id', 'amount');
        }
        $money = 0;
        foreach ($num as $k => $v) {
            if (isset($amount[$k])) {
                $money += intval($v) * floatval($amount[$k]);
            }
        }
        return $money;
    }

    //实时获取当日实时数据
    public static function getTodayCount()
    {
        $startDate = date('Y-m-d');
        $today = [];
        $model = new Perf();
        $today['bizDate'] = $startDate;
        $funList = ['reg', 'idVerified', 'qpayEnabled', 'investor', 'newRegisterAndInvestor', 'newInvestor', 'newRegAndNewInveAmount', 'preRegAndNewInveAmount', 'chargeViaPos', 'chargeViaEpay', 'drawAmount', 'investmentInWyj', 'investmentInWyb', 'onlineInvestment', 'offlineInvestment', 'totalInvestment', 'successFound', 'rechargeMoney', 'rechargeCost', 'draw', 'investAndLogin', 'notInvestAndLogin', 'repayMoney', 'repayLoanCount', 'repayUserCount', 'licaiNewInvCount', 'licaiNewInvSum', 'licaiInvCount', 'licaiInvSum', 'xsNewInvCount', 'xsNewInvSum', 'xsInvCount', 'xsInvSum', 'checkIn'];
        foreach ($funList as $field) {
            $method = 'get' . ucfirst($field);
            $model->$field = $model->{$method}($startDate);
            $today[$field] = $model->{$method}($startDate);
        }
        return $today;
    }

    //获取当月实时数据
    public static function getThisMonthCount()
    {
        //当月数据，排除当天
        $month = [
            'bizDate' => date('Y-m'),
            'totalInvestment' => 0.00,
            'onlineInvestment' => 0.00,
            'offlineInvestment' => 0.00,
            'rechargeMoney' => 0.00,
            'drawAmount' => 0.00,
            'rechargeCost' => 0.00,
            'reg' => 0,
            'idVerified' => 0,
            'successFound' => 0,
            'qpayEnabled' => 0,
            'investor' => 0,
            'newRegisterAndInvestor' => 0,
            'newInvestor' => 0,
            'newRegAndNewInveAmount' => 0.00,
            'preRegAndNewInveAmount' => 0.00,
            'investmentInWyb' => 0.00,
            'investmentInWyj' => 0.00,
            'licaiNewInvCount' => 0,
            'licaiNewInvSum' => 0.00,
            'licaiInvCount' => 0,
            'licaiInvSum' => 0.00,
            'xsNewInvCount' => 0,
            'xsNewInvSum' => 0.00,
            'xsInvCount' => 0,
            'xsInvSum' => 0.00,
            'checkIn' => 0,
            'repayMoney' => 0.00,
            'repayLoanCount' => 0,
        ];
        $monthBySql = self::getDbRead()->createCommand("
SELECT DATE_FORMAT(bizDate,'%Y-%m') as bizDate,
SUM(totalInvestment) AS totalInvestment,
SUM(onlineInvestment) AS onlineInvestment,
SUM(offlineInvestment) AS offlineInvestment,
SUM(rechargeMoney) AS rechargeMoney,
SUM(drawAmount) AS drawAmount,
SUM(rechargeCost) AS rechargeCost,
SUM(reg) AS reg,
SUM(idVerified) AS idVerified,
SUM(successFound) AS successFound,
SUM(qpayEnabled) AS qpayEnabled,
SUM(investor) AS investor,
SUM(newRegisterAndInvestor) AS newRegisterAndInvestor,
SUM(newInvestor) AS newInvestor,
SUM(newRegAndNewInveAmount) AS newRegAndNewInveAmount,
SUM(preRegAndNewInveAmount) AS preRegAndNewInveAmount,
SUM(investmentInWyb) AS investmentInWyb,
SUM(investmentInWyj) AS investmentInWyj,
SUM(licaiNewInvCount) AS licaiNewInvCount,
SUM(licaiNewInvSum) AS licaiNewInvSum,
SUM(licaiInvCount) AS licaiInvCount,
SUM(licaiInvSum) AS licaiInvSum,
SUM(xsNewInvCount) AS xsNewInvCount,
SUM(xsNewInvSum) AS xsNewInvSum,
SUM(xsInvCount) AS xsInvCount,
SUM(xsInvSum) AS xsInvSum,
SUM(checkIn) as checkIn,
SUM(repayMoney) as repayMoney,
SUM(repayLoanCount) as repayLoanCount
FROM perf WHERE DATE_FORMAT(bizDate,'%Y-%m-%d') < DATE_FORMAT(NOW(),'%Y-%m-%d') AND DATE_FORMAT(bizDate,'%Y-%m')=DATE_FORMAT(NOW(),'%Y-%m') GROUP BY DATE_FORMAT(bizDate,'%Y-%m')")->queryOne();
        if (false !== $monthBySql) {
            $month = $monthBySql;
        }
        //当天数据
        $today = Perf::getTodayCount();
        //获取当月实时数据
        $month['bizDate'] = $month['bizDate']?:$today['bizDate'];
        $month['totalInvestment'] = $month['totalInvestment'] + $today['totalInvestment'];
        $month['onlineInvestment'] = $month['onlineInvestment'] + $today['onlineInvestment'];
        $month['offlineInvestment'] = $month['offlineInvestment'] + $today['offlineInvestment'];
        $month['rechargeMoney'] = $month['rechargeMoney'] + $today['rechargeMoney'];
        $month['drawAmount'] = $month['drawAmount'] + $today['drawAmount'];
        $month['rechargeCost'] = $month['rechargeCost'] + $today['rechargeCost'];
        $month['reg'] = $month['reg'] + $today['reg'];
        $month['idVerified'] = $month['idVerified'] + $today['idVerified'];
        $month['successFound'] = $month['successFound'] + $today['successFound'];
        $month['qpayEnabled'] = $month['qpayEnabled'] + $today['qpayEnabled'];
        $month['investor'] = $month['investor'] + $today['investor'];
        $month['newRegisterAndInvestor'] = $month['newRegisterAndInvestor'] + $today['newRegisterAndInvestor'];
        $month['newInvestor'] = $month['newInvestor'] + $today['newInvestor'];
        $month['newRegAndNewInveAmount'] = $month['newRegAndNewInveAmount'] + $today['newRegAndNewInveAmount'];
        $month['preRegAndNewInveAmount'] = $month['preRegAndNewInveAmount'] + $today['preRegAndNewInveAmount'];
        $month['investmentInWyb'] = $month['investmentInWyb'] + $today['investmentInWyb'];
        $month['investmentInWyj'] = $month['investmentInWyj'] + $today['investmentInWyj'];
        $month['licaiNewInvCount'] = $month['licaiNewInvCount'] + $today['licaiNewInvCount'];
        $month['licaiNewInvSum'] = $month['licaiNewInvSum'] + $today['licaiNewInvSum'];
        $month['licaiInvCount'] = $month['licaiInvCount'] + $today['licaiInvCount'];
        $month['licaiInvSum'] = $month['licaiInvSum'] + $today['licaiInvSum'];
        $month['xsNewInvCount'] = $month['xsNewInvCount'] + $today['xsNewInvCount'];
        $month['xsNewInvSum'] = $month['xsNewInvSum'] + $today['xsNewInvSum'];
        $month['xsInvCount'] = $month['xsInvCount'] + $today['xsInvCount'];
        $month['xsInvSum'] = $month['xsInvSum'] + $today['xsInvSum'];
        $month['checkIn'] = $month['checkIn'] + $today['checkIn'];
        $month['repayMoney'] = $month['repayMoney'] + $today['repayMoney'];
        $month['repayLoanCount'] = $month['repayLoanCount'] + $today['repayLoanCount'];
        return $month;
    }

    //获取当天投资用户
    public function getDayInvestor($date)
    {
        $investor = self::getDbRead()->createCommand('SELECT DISTINCT(o.uid) FROM online_order AS o INNER JOIN online_product AS p ON o.`online_pid` = p.`id` WHERE o.status=1 AND p.isTest=0 AND DATE(FROM_UNIXTIME(o.created_at))=:date')
            ->bindValue('date', $date, \PDO::PARAM_STR)
            ->queryAll();
        return ArrayHelper::getColumn($investor, 'uid');
    }

    //当日注册当日投资用户
    public function getDayNewRegisterAndInvestor($date)
    {
        $investor = self::getDbRead()->createCommand('SELECT DISTINCT(o.uid) FROM online_order AS o LEFT JOIN `user` AS u ON o.uid = u.id INNER JOIN online_product AS p ON o.`online_pid` = p.`id` WHERE o.`status`= 1 AND p.isTest = 0 AND DATE(FROM_UNIXTIME(u.`created_at`)) = :date AND DATE(FROM_UNIXTIME(o.created_at)) = :date')
            ->bindValue('date', $date, \PDO::PARAM_STR)
            ->queryAll();
        return ArrayHelper::getColumn($investor, 'uid');
    }

    //新增投资用户（以前注册未投资，但今日投资了的）
    public function getDayNewInvestor($date)
    {
        $totalInvestor = self::getDbRead()->createCommand('SELECT DISTINCT(o.uid) FROM online_order AS o LEFT JOIN `user` AS u ON o.uid = u.id INNER JOIN online_product AS p ON o.`online_pid` = p.`id` WHERE o.`status`= 1 AND p.isTest=0 AND DATE(FROM_UNIXTIME(u.`created_at`)) < :date AND DATE(FROM_UNIXTIME(o.created_at))<=:date')
            ->bindValue('date', $date, \PDO::PARAM_STR)
            ->queryAll();
        $totalInvestor = ArrayHelper::getColumn($totalInvestor, 'uid');
        $investor = self::getDbRead()->createCommand('SELECT DISTINCT(o.uid) FROM online_order AS o LEFT JOIN `user` AS u ON o.uid = u.id INNER JOIN online_product AS p ON o.`online_pid` = p.`id` WHERE o.`status`= 1 AND p.isTest = 0 AND DATE(FROM_UNIXTIME(u.`created_at`)) < :date AND DATE(FROM_UNIXTIME(o.created_at)) <:date')
            ->bindValue('date', $date, \PDO::PARAM_STR)
            ->queryAll();
        $investor = ArrayHelper::getColumn($investor, 'uid');
        return array_diff($totalInvestor, $investor);
    }

    //获取已投用户登录的用户
    public function getDayInvestAndLogin($date)
    {
        $sql = "SELECT id FROM `user` WHERE `type` = 1 AND DATE(FROM_UNIXTIME(last_login)) = :date AND id  IN (SELECT DISTINCT o.uid FROM online_order AS o INNER JOIN online_product AS p ON o.`online_pid` = p.`id` WHERE o.`status` = 1 AND p.isTest = 0)";
        $res = self::getDbRead()->createCommand($sql, ['date' => $date])->queryAll();
        return ArrayHelper::getColumn($res, 'id');
    }

    //获取未投有用户登录的用户
    public function getDayNotInvestAndLogin($date)
    {
        $sql = "SELECT id FROM `user` WHERE `type` = 1 AND DATE(FROM_UNIXTIME(last_login)) = :date AND id NOT IN (SELECT DISTINCT o.uid FROM online_order AS o INNER JOIN online_product AS p ON o.`online_pid` = p.`id` WHERE o.`status` = 1 and p.isTest = 0)";
        $res = self::getDbRead()->createCommand($sql, ['date' => $date])->queryAll();
        return ArrayHelper::getColumn($res, 'id');
    }

    //获取月统计的统计人数
    public static function getMonthInvestor()
    {
        $startDate = Perf::getStartDate();
        $startDate = date('Y-m', strtotime($startDate));
        $date = date('Y-m');
        $result = [];
        while ($startDate <= $date) {
            $sql = "SELECT COUNT(DISTINCT o.uid) FROM online_order AS o INNER JOIN online_product AS p ON o.`online_pid` = p.`id` WHERE o.`status`=1  AND DATE_FORMAT(FROM_UNIXTIME(o.created_at),'%Y-%m')= :date";
            $res = self::getDbRead()->createCommand($sql, ['date' => $startDate])->queryScalar();
            $result[$startDate] = $res;
            $startDate = (new \DateTime($startDate))->add(new \DateInterval('P1M'))->format('Y-m');
        }
        return $result;
    }

    //统计每天回款总金额
    public function getRepayMoney($date)
    {
        $sql = "SELECT SUM( op.`benxi` ) FROM  `online_repayment_plan` AS op LEFT JOIN online_product AS p ON p.id = op.online_pid WHERE p.isTest =0 AND op.status IN ( 1, 2 ) AND DATE_FORMAT( op.`actualRefundTime` ,  '%Y-%m-%d' ) =  :date";
        return self::getDbRead()->createCommand($sql,['date' => $date])->queryScalar();
    }

    //统计每天回款项目
    public function getRepayLoanCount($date)
    {
        $sql = "SELECT COUNT( DISTINCT  op.`online_pid` ) FROM  `online_repayment_plan` AS op LEFT JOIN online_product AS p ON p.id = op.online_pid WHERE p.isTest =0 AND op.status IN ( 1, 2 ) AND DATE_FORMAT( op.`actualRefundTime` ,  '%Y-%m-%d' ) =  :date";
        return self::getDbRead()->createCommand($sql,['date' => $date])->queryScalar();
    }

    //统计每天回款用户数
    public function getRepayUserCount($date)
    {
        $sql = "SELECT COUNT( DISTINCT  op.`uid` ) FROM  `online_repayment_plan` AS op LEFT JOIN online_product AS p ON p.id = op.online_pid WHERE p.isTest =0  AND op.status IN ( 1, 2 ) AND DATE_FORMAT( op.`actualRefundTime` ,  '%Y-%m-%d' ) =  :date";
        return self::getDbRead()->createCommand($sql,['date' => $date])->queryScalar();
    }

    //获取每日已经还款的用户ID
    public function getDayRepayUser($date)
    {
        $sql = "SELECT DISTINCT  op.`uid` FROM  `online_repayment_plan` AS op LEFT JOIN online_product AS p ON p.id = op.online_pid WHERE p.isTest =0 AND op.status IN ( 1, 2 ) AND DATE_FORMAT( op.`actualRefundTime` ,  '%Y-%m-%d' ) =  :date";
        $data = self::getDbRead()->createCommand($sql,['date' => $date])->queryAll();
        return ArrayHelper::getColumn($data, 'uid');
    }


    /**
     * 获取首页相关统计
     * @return array
     */
    public static function getStatsForIndex()
    {
        $totalTradeAmount = OnlineProduct::find()
            ->where([
                'del_status' => false,
                'online_status' => true,
                'isTest' => false,
            ])
            ->andWhere(['>', 'status', OnlineProduct::STATUS_PRE])
            ->sum('funded_money');

        $plan = Repayment::find()
            ->where(['isRefunded' => true])
            ->select("sum(amount) as totalAmount, sum(interest) as totalInterest")
            ->asArray()
            ->one();

        $offlineStats = OfflineStats::findOne(1);

        $tradedAmount = 0;
        $refundedPrincipal = 0;
        $refundedInterest = 0;

        if (null !== $offlineStats) {
            $tradedAmount = $offlineStats->tradedAmount;
            $refundedPrincipal = $offlineStats->refundedPrincipal;
            $refundedInterest = $offlineStats->refundedInterest;
        }

        $totalFundedAmount = OnlineProduct::find()
            ->where(['>', 'status', 1])
            ->andWhere(['like', 'tags', '慈善专属'])
            ->sum('funded_money');

        $totalCharityAount = bcdiv($totalFundedAmount, 10000, 2);    //暂时只计算投资慈善项目计算所得的金额
        $donationTotal = AppMeta::getValue('donation_total');
        $donationTotal = is_numeric($donationTotal) ? $donationTotal : 0; //获取应用信息自愿捐赠金额
        $statsData = [
            'totalTradeAmount' => bcadd($totalTradeAmount, $tradedAmount, 2),//平台累计交易额
            'totalRefundAmount' => bcadd($plan['totalAmount'], bcadd($refundedPrincipal, $refundedInterest, 2), 2),//累计兑付金额
            'totalRefundInterest' => bcadd($plan['totalInterest'], $refundedInterest, 2),//累计带来收益
            'totalCharityAount' => bcadd($totalCharityAount, $donationTotal, 2),
        ];

        return $statsData;
    }

    //统计签到人数
    public function getCheckIn($date)
    {
        $sql = "select count(distinct user_id) from check_in where checkDate = :date";
        return self::getDbRead()->createCommand($sql, ['date' => $date])->queryScalar();
    }

    //统计每月还款用户数
    public static function getMonthRepayUserCount()
    {
        $sql = "SELECT date_format(op.actualRefundTime, '%Y-%m') as m,count(distinct op.uid) as c FROM `online_repayment_plan` as op left join online_product as p on p.id = op.online_pid where p.isTest = 0 and op.status in (1,2) and op.actualRefundTime is not null group by m";
        return self::getDbRead()->createCommand($sql)->queryAll();
    }
}
