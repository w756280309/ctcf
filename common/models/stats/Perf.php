<?php

namespace common\models\stats;

use yii\db\ActiveRecord;
use Yii;

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
 * @property integer $newInvestor   新增投资人数
 * @property string $chargeViaPos   POS充值
 * @property string $chargeViaEpay  线上充值
 * @property string $drawAmount 提现
 * @property string $investmentInWyj    温盈金
 * @property string $investmentInWyb    温盈宝
 * @property string $totalInvestment    投资金额
 * @property integer $successFound    融资项目
 * @property string $remainMoney    贷后余额
 * @property string $usableMoney    可用余额
 * @property string $rechargeMoney    充值金额
 * @property string $rechargeCost    充值手续费
 * @property string $draw    提现
 * @property integer $created_at    统计时间
 */
class Perf extends ActiveRecord
{
    //获取统计开始时间
    public static function getStartDate()
    {
        $date = Yii::$app->db->createCommand('SELECT MIN(DATE(FROM_UNIXTIME(created_at))) FROM user WHERE type=1')
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
        $time = Yii::$app->db->createCommand('SELECT MAX(created_at) FROM perf')->queryScalar();
        if (null === $time) {
            return time();
        } else {
            return $time;
        }
    }

    //注册数
    public function getReg($date)
    {
        return Yii::$app->db->createCommand('SELECT COUNT(id) FROM user WHERE type=1 AND DATE(FROM_UNIXTIME(created_at))=:date')
            ->bindValue('date', $date, \PDO::PARAM_STR)
            ->queryScalar();
    }

    //实名认证
    public function getIdVerified($date)
    {
        return Yii::$app->db->createCommand('SELECT COUNT(id) FROM EpayUser WHERE regDate=:date')
            ->bindValue('date', $date, \PDO::PARAM_STR)
            ->queryScalar();
    }

    //绑卡
    public function getQpayEnabled($date)
    {
        return Yii::$app->db->createCommand('SELECT COUNT(id) FROM user_bank WHERE DATE(FROM_UNIXTIME(created_at))=:date')
            ->bindValue('date', $date, \PDO::PARAM_STR)
            ->queryScalar();
    }

    //投资人数
    public function getInvestor($date)
    {
        return Yii::$app->db->createCommand('SELECT COUNT(DISTINCT(uid)) FROM online_order WHERE status=1 AND DATE(FROM_UNIXTIME(created_at))=:date')
            ->bindValue('date', $date, \PDO::PARAM_STR)
            ->queryScalar();
    }

    //新增投资人数
    public function getNewInvestor($date)
    {
        $totalInvestor = Yii::$app->db->createCommand('SELECT COUNT(DISTINCT(uid)) FROM online_order WHERE status=1 AND DATE(FROM_UNIXTIME(created_at))<=:date')
            ->bindValue('date', $date, \PDO::PARAM_STR)
            ->queryScalar();

        $investor = Yii::$app->db->createCommand('SELECT COUNT(DISTINCT(uid)) FROM online_order WHERE status=1 AND DATE(FROM_UNIXTIME(created_at))<:date')
            ->bindValue('date', $date, \PDO::PARAM_STR)
            ->queryScalar();

        return $totalInvestor - $investor;
    }

    //POS充值
    public function getChargeViaPos($date)
    {
        return Yii::$app->db->createCommand('SELECT SUM(r.fund) FROM recharge_record r LEFT JOIN user u ON r.uid=u.id WHERE r.status=1 AND r.pay_type=3 AND u.type=1 AND DATE(FROM_UNIXTIME(r.created_at))=:date')
            ->bindValue('date', $date, \PDO::PARAM_STR)
            ->queryScalar();
    }

    //线上充值
    public function getChargeViaEpay($date)
    {
        return Yii::$app->db->createCommand('SELECT SUM(r.fund) FROM recharge_record r LEFT JOIN user u ON r.uid=u.id WHERE r.status=1 AND r.pay_type<>3 AND u.type=1 AND DATE(FROM_UNIXTIME(r.created_at))=:date')
            ->bindValue('date', $date, \PDO::PARAM_STR)
            ->queryScalar();
    }

    //提现
    public function getDrawAmount($date)
    {
        return Yii::$app->db->createCommand('SELECT SUM(r.money) FROM draw_record r LEFT JOIN user u on r.uid=u.id WHERE r.status=2 and u.type=1 and DATE(FROM_UNIXTIME(r.created_at))=:date')
            ->bindValue('date', $date, \PDO::PARAM_STR)
            ->queryScalar();
    }

    //温盈金
    public function getInvestmentInWyj($date)
    {
        return Yii::$app->db->createCommand('select sum(o.order_money) from online_order o left join online_product l on o.online_pid=l.id where l.cid=1 and o.status=1 and DATE(FROM_UNIXTIME(o.created_at))=:date')
            ->bindValue('date', $date, \PDO::PARAM_STR)
            ->queryScalar();
    }

    //温盈宝
    public function getInvestmentInWyb($date)
    {
        return Yii::$app->db->createCommand('select sum(o.order_money) from online_order o left join online_product l on o.online_pid=l.id where l.cid=2 and o.status=1 and DATE(FROM_UNIXTIME(o.created_at))=:date')
            ->bindValue('date', $date, \PDO::PARAM_STR)
            ->queryScalar();
    }

    //投资金额
    public function getTotalInvestment($date)
    {
        return Yii::$app->db->createCommand('select sum(o.order_money) from online_order o where o.status=1 and DATE(FROM_UNIXTIME(o.created_at))=:date')
            ->bindValue('date', $date, \PDO::PARAM_STR)
            ->queryScalar();
    }

    //融资项目,成立及之后的项目，status [5,6,7],项目成立时间按照 full_time
    public function getSuccessFound($date)
    {
        $sql = 'SELECT COUNT(*) FROM online_product WHERE `status` IN (5,6,7) AND DATE(FROM_UNIXTIME(full_time))=:date;';
        return Yii::$app->db->createCommand($sql, ['date' => $date])->queryScalar();
    }

    //贷后余额,募集了但是还没有还款的那部分资金(只是已经成立但是还没有还款的项目,不包含募集中)，status [5,7]
    public static function getRemainMoney()
    {
        $sql = 'SELECT SUM(funded_money) FROM online_product WHERE `status` IN(5,7)';
        return Yii::$app->db->createCommand($sql)->queryScalar();
    }

    //可用余额,网站所有用户的可用余额总和
    public static function getUsableMoney()
    {
        $sql = 'SELECT SUM(a.available_balance) FROM user_account AS a LEFT JOIN `user` AS u ON a.uid = u.id WHERE u.type = 1';
        return Yii::$app->db->createCommand($sql)->queryScalar();
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
        $k = Yii::$app->db->createCommand($sql, ['date' => $date])->queryScalar();
        //网银充值
        $sql = "SELECT SUM(r.fund * 0.0018) FROM recharge_record AS r LEFT JOIN `user` AS u ON r.uid = u.id WHERE r.status = 1 AND r.pay_type = 2 AND u.type = 1 AND DATE(FROM_UNIXTIME(r.created_at)) = :date";
        $w = Yii::$app->db->createCommand($sql, ['date' => $date])->queryScalar();
        //POS充值
        $sql = "SELECT SUM(LEAST(r.fund * 0.0125,80))  FROM recharge_record r LEFT JOIN user u ON r.uid=u.id WHERE r.status=1 AND r.pay_type=3 AND u.type=1 AND DATE(FROM_UNIXTIME(r.created_at))=:date";
        $pos = Yii::$app->db->createCommand($sql, ['date' => $date])->queryScalar();
        return floatval($k) + floatval($w) + floatval($pos);
    }

    //提现
    public function getDraw($date)
    {
        $sql = 'SELECT SUM(money) FROM draw_record AS r LEFT JOIN `user` AS u ON r.uid = u.id WHERE r.`status` = 2 AND u.type = 1 AND DATE(FROM_UNIXTIME(r.created_at)) = :date';
        return Yii::$app->db->createCommand($sql, ['date' => $date])->queryScalar();
    }

    //实时获取当日实时数据
    public static function getTodayCount()
    {
        $startDate = date('Y-m-d');
        $today = [];
        $model = new Perf();
        $today['bizDate'] = $startDate;
        $funList = ['reg', 'idVerified', 'qpayEnabled', 'investor', 'newInvestor', 'chargeViaPos', 'chargeViaEpay', 'drawAmount', 'investmentInWyj', 'investmentInWyb', 'totalInvestment', 'successFound', 'rechargeMoney', 'rechargeCost', 'draw'];
        foreach ($funList as $field) {
            $method = 'get' . ucfirst($field);
            $today[$field] = $model->{$method}($startDate);
        }
        return $today;
    }

    //获取当月实时数据
    public static function getThisMonthCount()
    {
        //当月数据，排除当天
        $month = Yii::$app->db->createCommand("SELECT bizDate, SUM(totalInvestment) AS totalInvestment,SUM(rechargeMoney) AS rechargeMoney,SUM(drawAmount) AS drawAmount,SUM(rechargeCost) AS rechargeCost ,SUM(reg) AS reg,SUM(idVerified) AS idVerified,SUM(successFound) AS successFound, SUM(qpayEnabled) AS qpayEnabled, SUM(newInvestor) AS newInvestor,SUM(investmentInWyb) AS investmentInWyb, SUM(investmentInWyj) AS investmentInWyj FROM perf WHERE DATE_FORMAT(bizDate,'%Y-%m-%d') < DATE_FORMAT(NOW(),'%Y-%m-%d') AND DATE_FORMAT(bizDate,'%Y-%m')=DATE_FORMAT(NOW(),'%Y-%m')")->queryOne();
        //当天数据
        $today = Perf::getTodayCount();
        //获取当月实时数据
        $month['totalInvestment'] = $month['totalInvestment'] + $today['totalInvestment'];
        $month['rechargeMoney'] = $month['rechargeMoney'] + $today['rechargeMoney'];
        $month['drawAmount'] = $month['drawAmount'] + $today['drawAmount'];
        $month['rechargeCost'] = $month['rechargeCost'] + $today['rechargeCost'];
        $month['reg'] = $month['reg'] + $today['reg'];
        $month['idVerified'] = $month['idVerified'] + $today['idVerified'];
        $month['successFound'] = $month['successFound'] + $today['successFound'];
        $month['qpayEnabled'] = $month['qpayEnabled'] + $today['qpayEnabled'];
        $month['newInvestor'] = $month['newInvestor'] + $today['newInvestor'];
        $month['investmentInWyb'] = $month['investmentInWyb'] + $today['investmentInWyb'];
        $month['investmentInWyj'] = $month['investmentInWyj'] + $today['investmentInWyj'];
        return $month;
    }
}
