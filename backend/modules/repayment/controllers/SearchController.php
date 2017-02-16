<?php

namespace backend\modules\repayment\controllers;


use backend\controllers\BaseController;
use common\lib\user\UserStats;
use common\models\order\OnlineRepaymentPlan;
use common\models\payment\RepaymentSearch;
use common\models\product\OnlineProduct as Plan;
use yii\data\ActiveDataProvider;
use Yii;
use common\view\LoanHelper;
use common\utils\StringUtils;

class SearchController extends BaseController
{
    public function actionIndex()
    {
        $searchModel = new RepaymentSearch();
        $query = $searchModel->search(\Yii::$app->request->get());
        //待回款数据
        $noPaidQuery = clone  $query;
        $noPaidData = $noPaidQuery->andWhere(["repayment.isRefunded" => false])->select('repayment.amount, repayment.loan_id')->asArray()->all();
        $noPaidLoanCount = count(array_unique(array_column($noPaidData, 'loan_id')));
        $noPaidMoney = array_sum(array_column($noPaidData, 'amount'));
        //已回款数据
        $paidQuery = clone $query;
        $paidData = $paidQuery->andWhere(["repayment.isRefunded" => true])->select('repayment.amount, repayment.loan_id')->asArray()->all();
        $paidLoanCount = count(array_unique(array_column($paidData, 'loan_id')));
        $paidMoney = array_sum(array_column($paidData, 'amount'));

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
            '回款状态',
        ];
        $searchModel = new RepaymentSearch();
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
                $plan->term,
                $plan->dueDate,
                floatval($plan->amount),
                $plan->isRefunded ? '已回款' : '未回款',
            ];
        }

        UserStats::exportAsXlsx($exportData, '回款信息.xlsx');
    }
}