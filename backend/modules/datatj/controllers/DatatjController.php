<?php
namespace backend\modules\datatj\controllers;

use backend\controllers\BaseController;
use common\lib\bchelp\BcRound;
use common\lib\user\UserStats;
use common\models\checkaccount\CheckaccountHz;
use common\models\checkaccount\CheckaccountWdjf;
use common\models\stats\Perf;
use common\models\user\RechargeRecord;
use common\models\user\User;
use yii\bootstrap\Html;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\data\Pagination;
use Yii;
use yii\web\NotFoundHttpException;

class DatatjController extends BaseController
{
    /**
     * 汇总统计页面
     */
    public function actionHuizongtj()
    {
        $count_time = Perf::getLastTime();
        //统计累计数据，不含今日
        $total = Yii::$app->db->createCommand('SELECT SUM(onlineInvestment) as onlineInvestment, SUM(offlineInvestment) as offlineInvestment, SUM(totalInvestment) AS totalTotalInve, SUM(rechargeCost) AS totalRechargeCost, SUM(reg) AS totalReg,SUM(idVerified) AS totalIdVerified,SUM(successFound) AS totalSuccessFound,sum(qpayEnabled) as totalQpayEnabled, sum(newRegisterAndInvestor) as newRegisterAndInvestor, sum(newInvestor) as newInvestor, sum(investmentInWyb) as totalInvestmentInWyb, sum(investmentInWyj) as totalInvestmentInWyj FROM perf WHERE DATE_FORMAT(bizDate,\'%Y-%m-%d\') < DATE_FORMAT(NOW(),\'%Y-%m-%d\')')->queryOne();
        //今日统计数据
        $today = Perf::getTodayCount();
        //本月统计,不包含今天数据
        $month = Yii::$app->db->createCommand("SELECT SUM(onlineInvestment) as onlineInvestment, SUM(offlineInvestment) as offlineInvestment, SUM(totalInvestment) AS monthTotalInvestment,SUM(successFound) AS monthSuccessFound FROM perf WHERE DATE_FORMAT(bizDate,'%Y-%m-%d') < DATE_FORMAT(NOW(),'%Y-%m-%d') AND DATE_FORMAT(bizDate,'%Y-%m')=DATE_FORMAT(NOW(),'%Y-%m')")->queryOne();
        //贷后余额、平台可用余额
        $remainMoney = Perf::getRemainMoney();
        $usableMoney = Perf::getUsableMoney();

        //代金券统计
        $totalCoupon = Perf::getCoupon();
        $usedCoupon = Perf::getCoupon(1);
        $unusedCoupon = Perf::getCoupon(0);

        //统计，PC、wap、APP、微信的注册人数、购买人数、购买金额
        $registerData = Yii::$app->db->createCommand("SELECT COUNT(*) AS c,regFrom AS f FROM `user` WHERE `type` = 1  GROUP BY f ORDER BY f ASC")->queryAll();//不同来源的注册数
        $investorData = Yii::$app->db->createCommand("SELECT COUNT(DISTINCT uid) AS c ,SUM(o.order_money) AS m ,o.investFrom AS f FROM online_order AS o INNER JOIN online_product AS p ON o.online_pid = p.id WHERE o.status = 1 AND p.isTest = 0
 GROUP BY f ORDER BY f ASC")->queryAll();//不同来源的购买人数、购买金额
        return $this->render('huizongtj', [
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
        ]);
    }

    public function actionMonthtj()
    {
        //获取月投资人数
        $monthInvestor = Perf::getMonthInvestor();
        //获取当月数据
        $month = Perf::getThisMonthCount();
        //历史数据，不包含当月
        $sql = "SELECT DATE_FORMAT(bizDate,'%Y-%m') as bizDate, SUM(totalInvestment) AS totalInvestment, SUM(onlineInvestment) AS onlineInvestment,SUM(offlineInvestment) AS offlineInvestment,SUM(rechargeMoney) AS rechargeMoney,SUM(drawAmount) AS drawAmount,SUM(rechargeCost) AS rechargeCost ,SUM(reg) AS reg,SUM(idVerified) AS idVerified,SUM(successFound) AS successFound, SUM(qpayEnabled) AS qpayEnabled, SUM(investor) AS investor, SUM(newRegisterAndInvestor) AS newRegisterAndInvestor, SUM(newInvestor) AS newInvestor,SUM(investmentInWyb) AS investmentInWyb, SUM(investmentInWyj) AS investmentInWyj
FROM perf WHERE DATE_FORMAT(bizDate,'%Y-%m') < DATE_FORMAT(NOW(),'%Y-%m')  GROUP BY DATE_FORMAT(bizDate,'%Y-%m') ORDER BY DATE_FORMAT(bizDate,'%Y-%m') DESC";
        $data = Yii::$app->db->createCommand($sql)->queryAll();
        $allData = array_merge([$month], $data);
        $dataProvider = new ArrayDataProvider([
            'allModels' => $allData,
        ]);
        return $this->render('monthtj', [
            'dataProvider' => $dataProvider,
            'monthInvestor' => $monthInvestor
        ]);
    }

    public function actionDaytj()
    {
        //获取历史数据
        $query = Perf::find()->where(['<', 'bizDate', date('Y-m-d')]);
        $pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => 20]);
        $query = $query->orderBy(['bizDate' => SORT_DESC])->offset($pages->offset)->limit($pages->limit);
        $data = $query->asArray()->all();
        $page = Yii::$app->request->get('page');
        if (!isset($page) || 1 === intval($page)) {
            //获取今日数据
            $today = Perf::getTodayCount();
            $allData = array_merge([$today], $data);
        } else {
            $allData = $data;
        }
        $dataProvider = new ArrayDataProvider([
            'allModels' => $allData,
        ]);
        return $this->render('daytj', [
            'pages' => $pages,
            'dataProvider' => $dataProvider,
        ]);
    }

    //日历史数据导出
    public function actionDayExport()
    {
        //获取历史数据
        $history = Perf::find()->where(['<', 'bizDate', date('Y-m-d')])->orderBy(['bizDate' => SORT_DESC])->asArray()->all();
        $today = Perf::getTodayCount();
        $allData = array_merge([$today], $history);
        $record = implode(',', [
                '日期',
                '交易总额',
                '线上交易额',
                '线下交易额',
                '充值金额',
                '提现金额',
                '充值手续费',
                Yii::$app->params['pc_cat'][2] . '销售额',
                Yii::$app->params['pc_cat'][1] . '销售额',
                '注册用户',
                '实名认证',
                '绑卡用户数',
                '投资人数',
                '当日注册当日投资人数',
                '新增投资人数',
                '当日注册当日投资金额',
                '非当日注册当日投资金额',
                '已投用户登录数',
                '未投用户登录数',
                '融资项目',
                '理财计划新增投资人数',
                '理财计划新增投资用户的投资金额',
                '理财计划总投资人数',
                '理财计划总投资金额',
                '新手标新增投资人数',
                '新手标新增用户的投资金额',
                '新手标总投资人数',
                '新手标总投资金额',
            ]) . "\n";
        foreach ($allData as $k => $data) {
            $array = [
                $data['bizDate'],
                floatval($data['totalInvestment']),
                floatval($data['onlineInvestment']),
                floatval($data['offlineInvestment']),
                floatval($data['rechargeMoney']),
                floatval($data['drawAmount']),
                floatval($data['rechargeCost']),
                floatval($data['investmentInWyb']),
                floatval($data['investmentInWyj']),
                intval($data['reg']),
                intval($data['idVerified']),
                intval($data['qpayEnabled']),
                intval($data['investor']),
                intval($data['newRegisterAndInvestor']),
                intval($data['newInvestor']),
                floatval($data['newRegAndNewInveAmount']),
                floatval($data['preRegAndNewInveAmount']),
                intval($data['investAndLogin']),
                intval($data['notInvestAndLogin']),
                intval($data['successFound']),
                intval($data['licaiNewInvCount']),
                floatval($data['licaiNewInvSum']),
                intval($data['licaiInvCount']),
                floatval($data['licaiInvSum']),
                intval($data['xsNewInvCount']),
                floatval($data['xsNewInvSum']),
                intval($data['xsInvCount']),
                floatval($data['xsInvSum']),
            ];
            $record .= implode(',', $array) . "\n";
        }
        if (null !== $record) {
            $record = iconv('UTF-8', 'GB18030', $record);//转换编码
            header('Content-Disposition: attachment; filename="day-count(' . date('Y-m-d') . ').csv"');
            header('Content-Length: ' . strlen($record)); // 内容的字节数
            echo $record;
        }
    }

    //月数据导出
    public function actionMonthExport()
    {
        //获取当月数据
        $month = Perf::getThisMonthCount();
        //历史数据，不包含当月
        $sql = "SELECT DATE_FORMAT(bizDate,'%Y-%m') as bizDate,
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
SUM(xsInvSum) AS xsInvSum
FROM perf WHERE DATE_FORMAT(bizDate,'%Y-%m') < DATE_FORMAT(NOW(),'%Y-%m')  GROUP BY DATE_FORMAT(bizDate,'%Y-%m') ORDER BY DATE_FORMAT(bizDate,'%Y-%m') DESC";
        $history = Yii::$app->db->createCommand($sql)->queryAll();
        $allData = array_merge([$month], $history);
        $record = implode(',', [
                '日期',
                '交易总额',
                '线上交易额',
                '线下交易额',
                '充值金额',
                '提现金额',
                '充值手续费',
                Yii::$app->params['pc_cat'][2] . '销售额',
                Yii::$app->params['pc_cat'][1] . '销售额',
                '注册用户',
                '实名认证',
                '绑卡用户数',
                '投资人数',
                '当日注册当日投资人数',
                '新增投资人数',
                '当日注册当日投资金额',
                '非当日注册当日投资金额',
                '融资项目',
                '理财计划新增投资人数',
                '理财计划新增投资用户的投资金额',
                '理财计划总投资人数',
                '理财计划总投资金额',
                '新手标新增投资人数',
                '新手标新增用户的投资金额',
                '新手标总投资人数',
                '新手标总投资金额',
            ]) . "\n";
        foreach ($allData as $k => $data) {
            $array = [
                $data['bizDate'],
                floatval($data['totalInvestment']),
                floatval($data['onlineInvestment']),
                floatval($data['offlineInvestment']),
                floatval($data['rechargeMoney']),
                floatval($data['drawAmount']),
                floatval($data['rechargeCost']),
                floatval($data['investmentInWyb']),
                floatval($data['investmentInWyj']),
                intval($data['reg']),
                intval($data['idVerified']),
                intval($data['qpayEnabled']),
                intval($data['investor']),
                intval($data['newRegisterAndInvestor']),
                intval($data['newInvestor']),
                floatval($data['newRegAndNewInveAmount']),
                floatval($data['preRegAndNewInveAmount']),
                intval($data['successFound']),
                intval($data['licaiNewInvCount']),
                floatval($data['licaiNewInvSum']),
                intval($data['licaiInvCount']),
                floatval($data['licaiInvSum']),
                intval($data['xsNewInvCount']),
                floatval($data['xsNewInvSum']),
                intval($data['xsInvCount']),
                floatval($data['xsInvSum']),
            ];
            $record .= implode(',', $array) . "\n";
        }
        if (null !== $record) {
            $record = iconv('UTF-8', 'GB18030', $record);//转换编码
            header('Content-Disposition: attachment; filename="month-count(' . date('Y-m') . ').csv"');
            header('Content-Length: ' . strlen($record)); // 内容的字节数
            echo $record;
        }
    }

    /**
     * 根据待筛选类型、数据项、日期获取筛选到的用户ID
     * @param string $type  筛选类型，day:日筛选，month:月筛选
     * @param string $field 筛选项
     * @param string $date  日期字符串   YYYY-mm-dd|YYYY-mm
     * @return array
     * @throws NotFoundHttpException
     */
    private function getUserIdsByDataField($type, $field, $date)
    {
        if (!in_array($type, ['day', 'month'])) {
            throw new NotFoundHttpException('type 参数错误');
        }
        $ids = [];
        $perf = new Perf();
        $fun = 'getDay' . ucfirst($field);
        if ('day' === $type) {
            if (!in_array($field, ['investor', 'newRegisterAndInvestor', 'newInvestor', 'investAndLogin', 'notInvestAndLogin', 'repayUser'])) {
                throw new NotFoundHttpException("日统计的 field 参数目前只支持 'investor', 'newRegisterAndInvestor', 'newInvestor', 'investAndLogin', 'notInvestAndLogin', 'repayUser'");
            }
            $ids = $perf->{$fun}($date);
        } else {
            if (!in_array($field, ['investor', 'newRegisterAndInvestor', 'newInvestor'])) {
                throw new NotFoundHttpException("月统计的 field 参数目前只支持 'investor', 'newRegisterAndInvestor', 'newInvestor', 'investAndLogin', 'notInvestAndLogin'");
            }
            $num = date('t', strtotime($date));
            $ids = [];
            for ($i = 1; $i <= $num; $i++) {
                $newDate = $date . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
                $res = $perf->{$fun}($newDate);
                $ids = array_merge($ids, $res);
            }
            $ids = array_unique($ids);
        }
        return $ids;
    }

    public function actionList($type, $field, $date, $result = 0)
    {
        $ids = $this->getUserIdsByDataField($type, $field, $date);

        $query = User::find()->where(['type' => 1])->andWhere(['in', 'id', $ids]);
        $pages = new Pagination(['totalCount' => $query->count(), 'pageSize' => 20]);
        $query = $query->orderBy(['id' => SORT_DESC])->offset($pages->offset)->limit($pages->limit);
        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);
        $dataProvider->sort = false;
        return $this->render('list', [
            'dataProvider' => $dataProvider,
            'pages' => $pages,
            'type' => $type,
            'date' => $date,
            'field' => $field
        ]);
    }

    public function actionListExport($type, $field, $date)
    {
        $ids = $this->getUserIdsByDataField($type, $field, $date);

        $data = UserStats::collectLenderData(['in', '`user`.id', $ids]);
        UserStats::exportAsXlsx($data);
    }

    /**
     * 分销商数据统计(注册人数、购买人数、购买金额).
     *
     * 1. 可以根据输入的时间区间做统计;
     */
    public function actionAffiliation($start = null, $end = null)
    {
        $start = $start ? strtotime($start) : null;
        $end = $end ? strtotime($end.' 23:59:59') : null;

        $data = $this->affiliationStats($start, $end);
        $dataProvider = new ArrayDataProvider([
            'allModels' => $data,
        ]);
        $pages = new Pagination([
            'totalCount' => count($data),
        ]);
        return $this->render('affiliation', [
            'dataProvider' => $dataProvider,
            'pages' => $pages,
            'start' => $start,
            'end' => $end,
        ]);
    }

    /**
     * 分销商数据统计导出(注册人数、购买人数、购买金额).
     *
     * 1. 可以根据输入的时间区间做统计;
     */
    public function actionAffiliationExport($start, $end)
    {
        if (!preg_match('/^[0-9]+$/', $start)) {
            $start = null;
        }

        if (!preg_match('/^[0-9]+$/', $end)) {
            $end = null;
        }

        $data = $this->affiliationStats($start, $end);

        $record = implode(',', ['分销商ID', '分销商名称', '注册人数（人）', '投资人数（人）', '投资金额（元）']) . "\n";
        foreach ($data as $v) {
            $array = [$v['id'], Html::encode($v['name']), intval($v['uc']), intval($v['oc']), floatval($v['m'])];
            $record .= implode(',', $array) . "\n";
        }
        if (null !== $record) {
            $fileName = 'day-count(';

            if ($start) {
                $fileName .= date('Y-m-d', $start);
            }

            if ($end) {
                $fileName .= '到'.date('Y-m-d', $end);
            } else {
                $fileName .= '到'.date('Y-m-d');
            }

            $fileName .= ').csv';

            $record = iconv('UTF-8', 'GB18030', $record);//转换编码
            header('Content-Disposition: attachment; filename="'.$fileName.'"');
            header('Content-Length: ' . strlen($record)); // 内容的字节数
            echo $record;
        }
    }

    /**
     * 根据订单日期区间查询分销商统计数据.
     */
    private function affiliationStats($start, $end)
    {
        $userCond = "u.id = ua.`user_id` AND u.type = 1";
        $orderCond = "o.`uid` = ua.`user_id` AND o.status = 1";

        if ($start) {
            $userCond .= " AND u.created_at >= $start";
            $orderCond .= " AND o.created_at >= $start";
        }

        if ($end) {
            $userCond .= " AND u.created_at <= $end";
            $orderCond .= " AND o.created_at <= $end";
        }

        $sql = "SELECT a.id, a.name, COUNT(DISTINCT u.id) AS uc, COUNT(DISTINCT o.`uid`) AS oc, SUM(o.`order_money`) AS m
FROM affiliator AS a
LEFT JOIN user_affiliation AS ua ON a.id = ua.`affiliator_id`
LEFT JOIN `user` AS u ON ".$userCond."
LEFT JOIN online_order AS o ON ".$orderCond." GROUP BY a.id;";

        return Yii::$app->db->createCommand($sql)->queryAll();
    }
}
