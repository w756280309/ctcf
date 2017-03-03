<?php
/**
 * 统计定时任务
 */
namespace console\controllers;

use yii\console\Controller;
use common\models\stats\Perf;

class CountController extends Controller
{
    //每小时更新
    public function actionIndex()
    {
        $startDate = Perf::getStartDate();
        $date = date('Y-m-d');
        $time = time();
        //每次更新从第一条用户数据时间到今天的所有数据
        while ($startDate < $date) {
            $model = Perf::find()->where(['bizDate' => $startDate])->one();
            //没有找到，标示统计表中无数据；找到之后就全局更新。
            if (null === $model) {
                $model = new Perf();
            }
            $model->bizDate = $startDate;
            $model->created_at = $time;
            $funList = ['reg', 'idVerified', 'qpayEnabled', 'investor', 'newRegisterAndInvestor', 'newInvestor', 'chargeViaPos', 'chargeViaEpay', 'drawAmount', 'investmentInWyj', 'investmentInWyb', 'onlineInvestment', 'offlineInvestment', 'totalInvestment', 'successFound', 'rechargeMoney', 'rechargeCost', 'draw', 'investAndLogin', 'notInvestAndLogin', 'repayMoney', 'repayLoanCount', 'repayUserCount'];
            foreach ($funList as $field) {
                $method = 'get' . ucfirst($field);
                $model->{$field} = $model->{$method}($startDate);
            }
            $model->save();
            $startDate = (new \DateTime($startDate))->add(new \DateInterval('P1D'))->format('Y-m-d');
        }
    }
}