<?php

namespace backend\modules\repayment\controllers;


use backend\controllers\BaseController;
use common\lib\user\UserStats;
use common\models\order\OnlineRepaymentPlan;
use common\models\payment\RepaymentPlanSearch;
use common\models\product\OnlineProduct as Plan;
use yii\data\ActiveDataProvider;
use Yii;
use common\view\LoanHelper;
use common\utils\StringUtils;

class SearchController extends BaseController
{
    public function actionIndex()
    {
        $searchModel = new RepaymentPlanSearch();
        $query = $searchModel->search(\Yii::$app->request->get());
        //待回款数据
        $noPaidQuery = clone  $query;
        $noPaidData = $noPaidQuery->andWhere(["online_repayment_plan.status" => OnlineRepaymentPlan::STATUS_WEIHUAN])->select('online_repayment_plan.benxi')->asArray()->all();
        $noPaidLoanCount = count($noPaidData);
        $noPaidMoney = array_sum(array_column($noPaidData, 'benxi'));
        //已回款数据
        $paidQuery = clone $query;
        $paidData = $paidQuery->andWhere(['in', "online_repayment_plan.status", [OnlineRepaymentPlan::STATUS_YIHUAN, OnlineRepaymentPlan::STATUS_TIQIAM]])->select('online_repayment_plan.benxi')->asArray()->all();
        $paidLoanCount = count($paidData);
        $paidMoney = array_sum(array_column($paidData, 'benxi'));

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 15,
            ]
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
            'noPaidLoanCount' => $noPaidLoanCount,
            'noPaidMoney' => $noPaidMoney,
            'paidLoanCount' => $paidLoanCount,
            'paidMoney' => $paidMoney,
        ]);
    }

    public function actionExport()
    {
        $exportData[] = [
            '序号',
            '项目名称',
            '项目类型',
            '期限',
            '利率',
            '实际募集金额',
            '起息日',
            '到期日',
            '宽限期',
            '回款期数',
            '回款时间',
            '回款金额',
            '标的状态',
        ];
        $searchModel = new RepaymentPlanSearch();
        $query = $searchModel->search(\Yii::$app->request->get());
        $allData = $query->all();
        foreach ($allData as $plan) {
            $loan = $plan->loan;
            $category = Yii::$app->params['pc_cat'];
            $refundMethod = intval($loan->refund_method);
            $status = Yii::$app->params['deal_status'];

            $exportData[] = [
                $loan->sn,
                $loan->title,
                isset($category[$loan->cid]) ? $category[$loan->cid] : '',
                $loan->expires . ($refundMethod === Plan::REFUND_METHOD_DAOQIBENXI ? '天' : '月'),
                LoanHelper::getDealRate($loan) . ($loan->jiaxi ? '+' . StringUtils::amountFormat2($loan->jiaxi) : ''),
                floatval($loan->funded_money),
                date('Y-m-d', $loan->jixi_time),
                date('Y-m-d', $loan->finish_date),
                $loan->kuanxianqi . '天',
                $plan->qishu,
                date('Y-m-d', $plan->refund_time),
                floatval($plan->benxi),
                isset($status[$loan->status]) ? $status[$loan->status] : '',
            ];
        }

        UserStats::exportAsXlsx($exportData, '回款信息.xlsx');
    }
}