<?php
namespace backend\modules\datatj\controllers;

use backend\controllers\BaseController;
use common\lib\bchelp\BcRound;
use common\models\checkaccount\CheckaccountHz;
use common\models\checkaccount\CheckaccountWdjf;
use common\models\stats\Perf;
use common\models\user\RechargeRecord;
use common\models\user\User;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\data\Pagination;
use yii\db\ActiveQuery;
use yii\db\Query;
use Yii;

class DatatjController extends BaseController
{

    /**
     * desc 对账单
     * view creator zyw
     * code creator zhy
     * @return type
     */
    public function actionAccountsta($start = null, $end = null, $check = null)
    {
        $fail_data = CheckaccountWdjf::find()->where(['is_checked' => 1, 'is_auto_okay' => 2, 'is_okay' => 0])->select('tx_amount')->asArray()->all();
        $fail_count = count($fail_data);
        $fail_sum = 0;
        bcscale(14);
        $bcround = new BcRound();
        foreach ($fail_data as $dat) {
            $fail_sum = bcadd($fail_sum, $dat['tx_amount']);
        }
        $params = [
            'start' => $start,
            'end' => $end,
            'check' => $check,
            'fail_count' => $fail_count,
            'fail_sum' => $bcround->bcround($fail_sum, 2)
        ];

        $data = CheckaccountWdjf::find()->where(['is_checked' => 1]);
        if (!empty($start) && !empty($end)) {
            $data->andFilterWhere(['between', 'tx_date', $start, $end]);
        }
        if ($check) {
            $data->andWhere(['is_auto_okay' => $check]);
        }
        $pages = new Pagination(['totalCount' => $data->count(), 'pageSize' => '15']);
        $model = $data->offset($pages->offset)->limit($pages->limit)->all();
        return $this->render('accountsta', ['params' => $params, 'model' => $model, 'pages' => $pages]);
    }

    /**
     * desc 统计
     * view creator zyw
     * code creator zhy
     * @return type
     */
    public function actionTjbydays($start = null, $end = null)
    {
        $data = CheckaccountHz::find();
        if (!empty($start) && !empty($end)) {
            $data->andFilterWhere(['between', 'tx_date', $start, $end]);
        }
        $tjdata = $data->all();
        $rcount = $rcsum = $jcount = $jsum = 0;
        bcscale(14);
        $bcround = new BcRound();
        foreach ($tjdata as $tj) {
            $rcount += intval($tj->recharge_count);
            $jcount += intval($tj->jiesuan_count);
            $rcsum = bcadd($rcsum, $tj->recharge_sum);
            $jsum = bcadd($jsum, $tj->jiesuan_sum);
        }
        $params = [
            'start' => $start,
            'end' => $end,
            'rcount' => $rcount,
            'rcsum' => $bcround->bcround($rcsum, 2),
            'jcount' => $jcount,
            'jsum' => $bcround->bcround($jsum, 2),
        ];
        $pages = new Pagination(['totalCount' => $data->count(), 'pageSize' => '15']);
        $model = $data->offset($pages->offset)->limit($pages->limit)->all();
        return $this->render('tjbydays', ['params' => $params, 'model' => $model, 'pages' => $pages]);
    }

    /**
     * desc 充值结算记录
     * view creator zyw
     * code creator zyw
     * @return type
     */
    public function actionRechargejs()
    {
        $start = Yii::$app->request->get('start');
        $end = Yii::$app->request->get('end');
        $status = Yii::$app->request->get('status');
        $name = Yii::$app->request->get('name');

        $r = RechargeRecord::tableName();
        $u = User::tableName();
        $recharge = RechargeRecord::find()->leftJoin($u, "$r.uid=$u.id")->select("$r.*,$u.real_name");
        $recharge->where(['pay_type' => [RechargeRecord::PAY_TYPE_QUICK, RechargeRecord::PAY_TYPE_NET]]);
        $data = clone $recharge;
        if (!empty($start)) {
            $data->andFilterWhere(['>=', "$r.created_at", strtotime($start . " 0:00:00")]);
        }
        if (!empty($end)) {
            $data->andFilterWhere(['<=', "$r.created_at", strtotime($end . " 23:59:59")]);
        }
        if (!empty($status)) {
            if (in_array($status, [RechargeRecord::SETTLE_NO, RechargeRecord::SETTLE_ACCEPT, RechargeRecord::SETTLE_IN, RechargeRecord::SETTLE_YES, RechargeRecord::SETTLE_FAULT])) {
                $data->andWhere(['settlement' => $status]);
            } else {
                $data->andWhere(["$r.status" => $status - 3]);
            }
        }
        if (!empty($name)) {
            $data->andFilterWhere(['like', 'real_name', $name]);
        }
        $pages = new Pagination(['totalCount' => $data->count(), 'pageSize' => '15']);
        $model = $data->offset($pages->offset)->limit($pages->limit)->orderBy('id desc')->asArray()->all();

        $recharge = $recharge->asArray()->all();
        //统计冲值成功笔数，冲值失败笔数，充值金额总计，待结算笔数，待结算金额总计
        $arr = array('succ_num' => 0, 'fail_num' => 0, 'fund_sum' => 0, 'djs_num' => 0, 'djs_sum' => 0);
        $bc = new BcRound();
        bcscale(14);
        foreach ($recharge as $val) {
            if ($val['status'] == RechargeRecord::STATUS_YES) {
                $arr['succ_num']++;
                if ($val['settlement'] == RechargeRecord::SETTLE_NO) {   //统计充值成功下，未结算时的数量
                    $arr['djs_num']++;
                    $arr['djs_sum'] = bcadd($arr['djs_sum'], $val['fund']);
                }
            }
            if ($val['status'] == RechargeRecord::STATUS_FAULT) {
                $arr['fail_num']++;
            }
            $arr['fund_sum'] = bcadd($arr['fund_sum'], $val['fund']);
        }

        $arr['djs_sum'] = $bc->bcround($arr['djs_sum'], 2);
        $arr['fund_sum'] = $bc->bcround($arr['fund_sum'], 2);

        return $this->render('rechargejs', ['model' => $model, 'pages' => $pages, 'arr' => $arr]);
    }

    /**
     * 汇总统计页面
     */
    public function actionHuizongtj()
    {
        $count_time = Perf::getLastTime();
        //统计累计数据，不含今日
        $total = Yii::$app->db->createCommand('SELECT SUM(totalInvestment) AS totalTotalInve, SUM(rechargeCost) AS totalRechargeCost, SUM(reg) AS totalReg,SUM(idVerified) AS totalIdVerified,SUM(successFound) AS totalSuccessFound,sum(qpayEnabled) as totalQpayEnabled, sum(newRegisterAndInvestor) as newRegisterAndInvestor, sum(newInvestor) as newInvestor, sum(investmentInWyb) as totalInvestmentInWyb, sum(investmentInWyj) as totalInvestmentInWyj FROM perf WHERE DATE_FORMAT(bizDate,\'%Y-%m-%d\') < DATE_FORMAT(NOW(),\'%Y-%m-%d\')')->queryOne();
        //今日统计数据
        $today = Perf::getTodayCount();
        //本月统计,不包含今天数据
        $month = Yii::$app->db->createCommand("SELECT SUM(totalInvestment) AS monthTotalInvestment,SUM(successFound) AS monthSuccessFound FROM perf WHERE DATE_FORMAT(bizDate,'%Y-%m-%d') < DATE_FORMAT(NOW(),'%Y-%m-%d') AND DATE_FORMAT(bizDate,'%Y-%m')=DATE_FORMAT(NOW(),'%Y-%m')")->queryOne();
        //贷后余额、平台可用余额
        $remainMoney = Perf::getRemainMoney();
        $usableMoney = Perf::getUsableMoney();

        return $this->render('huizongtj', [
            'totalTotalInve' => $total['totalTotalInve'] + $today['totalInvestment'],//平台累计交易额
            'totalRechargeCost' => $total['totalRechargeCost'] + $today['rechargeCost'],//累计充值手续费
            'totalReg' => $total['totalReg'] + $today['reg'],//累计注册用户
            'totalIdVerified' => $total['totalIdVerified'] + $today['idVerified'],//累计实名认证
            'totalSuccessFound' => $total['totalSuccessFound'] + $today['successFound'],//累计项目数
            'totalQpayEnabled' => $total['totalQpayEnabled'] + $today['qpayEnabled'],//累计绑卡人数
            'totalInvestor' => $total['newRegisterAndInvestor'] + $today['newRegisterAndInvestor'] + $total['newInvestor'] + $today['newInvestor'],//累计投资人数
            'totalInvestmentInWyb' => $total['totalInvestmentInWyb'] + $today['investmentInWyb'],//温盈宝累计销售额
            'totalInvestmentInWyj' => $total['totalInvestmentInWyj'] + $today['investmentInWyj'],//温盈金累计销售额
            'countDate' => date('Y年m月d日 H:i', $count_time),
            'todayTotalInve' => $today['totalInvestment'],//今日交易额
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
            'monthTotalInvestment' => $month['monthTotalInvestment'] + $today['totalInvestment'],//本月交易额
            'monthSuccessFound' => $month['monthSuccessFound'] + $today['successFound'],//本月融资项目
            'remainMoney' => $remainMoney,//贷后余额
            'usableMoney' => $usableMoney,//可用余额
        ]);
    }

    public function actionMonthtj()
    {
        //获取当月数据
        $month = Perf::getThisMonthCount();
        //历史数据，不包含当月
        $sql = "SELECT bizDate, SUM(totalInvestment) AS totalInvestment,SUM(rechargeMoney) AS rechargeMoney,SUM(drawAmount) AS drawAmount,SUM(rechargeCost) AS rechargeCost ,SUM(reg) AS reg,SUM(idVerified) AS idVerified,SUM(successFound) AS successFound, SUM(qpayEnabled) AS qpayEnabled, SUM(investor) AS investor, SUM(newRegisterAndInvestor) AS newRegisterAndInvestor, SUM(newInvestor) AS newInvestor,SUM(investmentInWyb) AS investmentInWyb, SUM(investmentInWyj) AS investmentInWyj
FROM perf WHERE DATE_FORMAT(bizDate,'%Y-%m') < DATE_FORMAT(NOW(),'%Y-%m')  GROUP BY DATE_FORMAT(bizDate,'%Y-%m') ORDER BY DATE_FORMAT(bizDate,'%Y-%m') DESC";
        $data = Yii::$app->db->createCommand($sql)->queryAll();
        $allData = array_merge([$month], $data);
        $dataProvider = new ArrayDataProvider([
            'allModels' => $allData,
        ]);
        return $this->render('monthtj', [
            'dataProvider' => $dataProvider,
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
}
