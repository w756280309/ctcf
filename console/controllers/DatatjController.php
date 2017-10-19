<?php

namespace console\controllers;

use common\models\stats\Perf;
use common\models\product\OnlineProduct;
use Yii;
use yii\console\Controller;
use yii\helpers\ArrayHelper;

class DatatjController extends Controller
{
    public function actionTotal()
    {
        $dbRead = Yii::$app->db_read;
        $count_time = Perf::getLastTime();
        //统计累计数据，不含今日
        $total = $dbRead->createCommand('SELECT SUM(onlineInvestment) as onlineInvestment, SUM(offlineInvestment) as offlineInvestment, SUM(totalInvestment) AS totalTotalInve, SUM(rechargeCost) AS totalRechargeCost, SUM(reg) AS totalReg,SUM(idVerified) AS totalIdVerified,SUM(successFound) AS totalSuccessFound,sum(qpayEnabled) as totalQpayEnabled, sum(newRegisterAndInvestor) as newRegisterAndInvestor, sum(newInvestor) as newInvestor, sum(investmentInWyb) as totalInvestmentInWyb, sum(investmentInWyj) as totalInvestmentInWyj FROM perf WHERE DATE_FORMAT(bizDate,\'%Y-%m-%d\') < DATE_FORMAT(NOW(),\'%Y-%m-%d\')')->queryOne();
        //今日统计数据
        $today = Perf::getTodayCount();
        //本月统计,不包含今天数据
        $month = $dbRead->createCommand("SELECT SUM(onlineInvestment) as onlineInvestment, SUM(offlineInvestment) as offlineInvestment, SUM(totalInvestment) AS monthTotalInvestment,SUM(successFound) AS monthSuccessFound FROM perf WHERE DATE_FORMAT(bizDate,'%Y-%m-%d') < DATE_FORMAT(NOW(),'%Y-%m-%d') AND DATE_FORMAT(bizDate,'%Y-%m')=DATE_FORMAT(NOW(),'%Y-%m')")->queryOne();
        //贷后余额、平台可用余额
        $remainMoney = Perf::getRemainMoney();
        $usableMoney = Perf::getUsableMoney();

        //代金券统计
        $totalCoupon = Perf::getCoupon();
        $usedCoupon = Perf::getCoupon(1);
        $unusedCoupon = Perf::getCoupon(0);

        //统计，PC、wap、APP、微信的注册人数、购买人数、购买金额
        $registerData = $dbRead->createCommand("SELECT COUNT(*) AS c,regFrom AS f FROM `user` WHERE `type` = 1  GROUP BY f ORDER BY f ASC")->queryAll();//不同来源的注册数
        $investorData = $dbRead->createCommand("SELECT COUNT(DISTINCT uid) AS c ,SUM(o.order_money) AS m ,o.investFrom AS f FROM online_order AS o INNER JOIN online_product AS p ON o.online_pid = p.id WHERE o.status = 1 AND p.isTest = 0
 GROUP BY f ORDER BY f ASC")->queryAll();//不同来源的购买人数、购买金额
        //平台累计已还清项目数
        $onlineProPay = OnlineProduct::find()->where(['status' => 6, 'isTest' => false, 'del_status' => false])->count();
        $fileData = [
            'totalOnlineInve' => $total['onlineInvestment'] + $today['onlineInvestment'],//线上累计投资金额
            'totalOfflineInve' => $total['offlineInvestment'] + $today['offlineInvestment'],//线下累计投资金额
            'totalTotalInve' => $total['totalTotalInve'] + $today['totalInvestment'],//平台累计交易额 线上+线下
            'totalRechargeCost' => $total['totalRechargeCost'] + $today['rechargeCost'],//累计充值手续费
            'totalReg' => $total['totalReg'] + $today['reg'],//累计注册用户
            'totalIdVerified' => $total['totalIdVerified'] + $today['idVerified'],//累计实名认证
            'totalSuccessFound' => $total['totalSuccessFound'] + $today['successFound'],//累计项目数
            'totalQpayEnabled' => $total['totalQpayEnabled'] + $today['qpayEnabled'],//累计绑卡人数
            'totalInvestor' => $total['newRegisterAndInvestor'] + $today['newRegisterAndInvestor'] + $total['newInvestor'] + $today['newInvestor'],//累计投资人数
            'totalInvestmentInWyb' => $total['totalInvestmentInWyb'] + $today['investmentInWyb'],//温盈宝累计销售额
            'totalInvestmentInWyj' => $total['totalInvestmentInWyj'] + $today['investmentInWyj'],//温盈金累计销售额
            'countDate' => date('Y年m月d日 H:i', $count_time),
            'todayOnlineInvestment' => $today['onlineInvestment'],//今日线上交易额
            'toadyRechargeCost' => $today['rechargeCost'],//今日充值手续费
            'todayRechargeMoney' => $today['rechargeMoney'],//今日充值金额
            'todayDraw' => $today['draw'],//今日体现
            'todayReg' => $today['reg'],//今日注册
            'todayIdVerified' => $today['idVerified'],//今日实名认证
            'todaySuccessFound' => $today['successFound'],//今日项目
            'todayInvestmentInWyb' => $today['investmentInWyb'],//今日温盈宝销售额
            'todayInvestmentInWyj' => $today['investmentInWyj'],//今日温盈金销售额
            'qpayEnabled' => $today['qpayEnabled'],//今日绑卡用户数
            'newInvestor' => $today['newInvestor'],//今日新增投资人数
            'newRegisterAndInvestor' => $today['newRegisterAndInvestor'],//今日注册今日投资人数
            'investAndLogin' => $today['investAndLogin'],//今日已投用户登录数
            'notInvestAndLogin' => $today['notInvestAndLogin'],//今日未投用户登录数
            'monthOnlineInvestment' => $month['onlineInvestment'] + $today['onlineInvestment'],//本月交易额 线上
            'monthOfflineInvestment' => $month['offlineInvestment'] + $today['offlineInvestment'],//本月交易额 线下
            'monthTotalInvestment' => $month['monthTotalInvestment'] + $today['totalInvestment'],//本月交易额 线上 + 线下
            'monthSuccessFound' => $month['monthSuccessFound'] + $today['successFound'],//本月融资项目
            'remainMoney' => $remainMoney,//贷后余额
            'usableMoney' => $usableMoney,//可用余额
            'usedCoupon' => $usedCoupon,//已使用代金券
            'unusedCoupon' => $unusedCoupon,//未使用代金券
            'totalCoupon' => $totalCoupon,//已发放代金券
            'registerData' => $registerData,//不同来源的注册数
            'investorData' => $investorData,//不同来源的购买人数、购买金额
            'onlineProPay' => $onlineProPay,//平台累计已还清项目数
        ];
        $fileData['lastUpdateTime'] = date('Y-m-d H:i:s');

        //存入redis
        $redis = Yii::$app->redis;
        $redis->set('datatj.actionHuizongtj', json_encode($fileData));
        $redis->expire('datatj.actionHuizongtj', 7 * 24 * 3600);
    }

    public function actionMonth()
    {
        //获取月投资人数
        $monthInvestor = Perf::getMonthInvestor();

        //获取当月数据
        $month = Perf::getThisMonthCount();

        //存入redis
        $redis = Yii::$app->redis;
        $redis->hset('datatj.actionMonthtj', 'monthData', json_encode($month));
        $redis->hset('datatj.actionMonthtj', 'monthInvestor', json_encode($monthInvestor));
        $redis->hset('datatj.actionMonthtj', 'lastUpdateTime', date('Y-m-d H:i:s'));
        $redis->expire('datatj.actionMonthtj', 31 * 24 * 3600);
    }

    public function actionHistoryMonth()
    {
        $dbRead = Yii::$app->db_read;
        //历史数据，不包含当月
        $sql = "SELECT DATE_FORMAT(bizDate,'%Y-%m') as bizDate, SUM(totalInvestment) AS totalInvestment, SUM(onlineInvestment) AS onlineInvestment,SUM(offlineInvestment) AS offlineInvestment,SUM(rechargeMoney) AS rechargeMoney,SUM(drawAmount) AS drawAmount,SUM(rechargeCost) AS rechargeCost ,SUM(reg) AS reg,SUM(idVerified) AS idVerified,SUM(successFound) AS successFound, SUM(qpayEnabled) AS qpayEnabled, SUM(investor) AS investor, SUM(newRegisterAndInvestor) AS newRegisterAndInvestor, SUM(newInvestor) AS newInvestor,SUM(investmentInWyb) AS investmentInWyb, SUM(investmentInWyj) AS investmentInWyj
FROM perf WHERE DATE_FORMAT(bizDate,'%Y-%m') < DATE_FORMAT(NOW(),'%Y-%m')  GROUP BY DATE_FORMAT(bizDate,'%Y-%m') ORDER BY DATE_FORMAT(bizDate,'%Y-%m') DESC";
        $data = $dbRead->createCommand($sql)->queryAll();

        //存入redis
        $redis = Yii::$app->redis;
        $redis->hset('datatj.actionMonthtj', 'historyMonthData', json_encode($data));
        $redis->expire('datatj.actionMonthtj', 31 * 24 * 3600);
    }

    public function actionDay()
    {
        //获取当天数据
        $todayData = Perf::getTodayCount();

        //存入redis
        $redis = Yii::$app->redis;
        $redis->hset('datatj.actionDaytj', 'todayData', json_encode($todayData));
        $redis->hset('datatj.actionDaytj', 'lastUpdateTime', date('Y-m-d H:i:s'));
        $redis->expire('datatj.actionDaytj', 31 * 24 * 3600);
    }
}