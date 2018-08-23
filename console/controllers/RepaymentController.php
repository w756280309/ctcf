<?php

namespace console\controllers;

use common\lib\user\UserStats;
use common\models\order\OnlineOrder;
use common\models\order\OnlineRepaymentPlan;
use common\models\payment\Repayment;
use common\models\product\OnlineProduct;
use common\models\user\MoneyRecord;
use yii\console\Controller;

class RepaymentController extends Controller
{
    /**
     * 补充部分用户还款的资金流水
     * @param $pid 标的id
     * @param $qishu 期数
     */
    public function actionAddMoneyRecord($pid, $qishu, $isExecute = false)
    {
        //判断参数是否错误
        if ($pid <= 0 || $qishu <= 0) {
            die('还款参数错误');
        }
        $loan = OnlineProduct::findOne($pid);
        if (null === $loan) {
            die('标的不存在');
        }
        //检查还款计划是否存在
        $plans = OnlineRepaymentPlan::find()
            ->where(['online_pid' => $pid])
            ->andWhere(['qishu' => $qishu])
            ->all();
        if (0 === count($plans)) {
            die('还款计划不存在');
        }
        //检查还款记录
        $repayment = Repayment::find()
            ->where(['loan_id' => $pid, 'term' => $qishu])
            ->one();
        if (null === $repayment) {
            die('还款信息不存在');
        }

        try {
            foreach ($plans as $plan) {

                $user = $plan->user;
                $lendAccount = $user->lendAccount;

                if (!in_array($plan->status, [OnlineRepaymentPlan::STATUS_TIQIAM, OnlineRepaymentPlan::STATUS_YIHUAN])
                    || empty($plan->actualRefundTime)) {
                    echo $loan->title.'第'.$plan->qishu.'期'.$user->real_name.'还未还款'.PHP_EOL;
                    continue;
                }

                $mr = MoneyRecord::find()
                    ->where([
                        'account_id' => $lendAccount->id,
                        'type' => null !== $plan->asset_id ? MoneyRecord::TYPE_CREDIT_HUIKUAN : MoneyRecord::TYPE_HUIKUAN,
                        'osn' => null !== $plan->asset_id ? $plan->asset_id : $plan->sn,
                        'uid' => $plan->uid,
                    ])
                    ->one();
                if (!is_null($mr)) {
                    echo $loan->title.'第'.$plan->qishu.'期'.$user->real_name.'回款流水已存在'.PHP_EOL;
                    continue;
                }

                $refundTime = strtotime($plan->actualRefundTime);

                $balance = $plan->benxi;
                $moneyRecord = MoneyRecord::find()
                    ->where(['uid' => $plan->uid])
                    ->andWhere(['<=', 'created_at', $refundTime])
                    ->orderBy(['created_at' => SORT_DESC, 'id' => SORT_DESC])
                    ->one();
                if (!is_null($moneyRecord)) {
                    $balance += $moneyRecord->balance;
                }

                $sn = $this->createSn($refundTime);

                //添加投资人流水更新
                if ($isExecute) {
                    $lenderMoneyRecord = new MoneyRecord([
                        'account_id' => $lendAccount->id,
                        'sn' => $sn,
                        'type' => null !== $plan->asset_id ? MoneyRecord::TYPE_CREDIT_HUIKUAN : MoneyRecord::TYPE_HUIKUAN,
                        'osn' => null !== $plan->asset_id ? $plan->asset_id : $plan->sn,
                        'uid' => $plan->uid,
                        'in_money' => $plan->benxi,
                        'balance' => $balance,
                        'remark' => '第'.$plan->qishu.'期'.'本金:'.$plan->benjin.'元;利息:'.$plan->lixi.'元;',
                    ]);
                    $lenderMoneyRecord->save(false);

                    $lenderMoneyRecord->created_at = $refundTime;
                    $lenderMoneyRecord->save(false);

                    echo '已添加'.$loan->title.'第'.$plan->qishu.'期'.$user->real_name.'的回款流水'.PHP_EOL;
                } else {
                    echo '要添加'.$loan->title.'第'.$plan->qishu.'期'.$user->real_name.'的回款流水'.PHP_EOL;
                }
            }
        } catch (\Exception $ex) {
            die($ex->getMessage());
        }
    }

    private function createSn($time)
    {
        list($sec, $usec) = explode('.', sprintf('%.6f', microtime(true)));

        return 'MR'
            .date('ymdHis', $time)
            .$usec
            .mt_rand(1000, 9999);
    }

    /**按照一定时间导出深圳居莫愁物业管理有限公司、北京居莫愁物业管理有限公司、上海浦壹电子科技有限公司、 深圳立合旺通商业保理有限公司的还款数据
     * @param $startDate 开始时间
     * @param $endDate 结束时间
     */
    public function actionGetRepaymentRecord($startDate, $endDate)
    {
        $sql = "select op.id from money_record mr inner join user u on mr.uid=u.id left join online_fangkuan of on mr.osn=of.sn left join online_product op on op.id=of.online_product_id where u.id in (64655,73030,58536,55868,73026,15) and date(from_unixtime(mr.created_at)) between :startDate and :endDate and mr.type=3 and op.isTest=0";
        $productIds = \Yii::$app->db->createCommand($sql, [
            'startDate' => $startDate,
            'endDate' => $endDate
        ])->queryAll();
        $record = [];
        $k = 0;
        foreach ($productIds as $productId) {
            $orders = OnlineOrder::find()
                ->where(['status' => OnlineOrder::STATUS_SUCCESS])
                ->andWhere(['online_pid' => $productId])
                ->all();
            foreach ($orders as $order) {
                $data = OnlineRepaymentPlan::calcBenxi($order);
                foreach ($data as $key => $value) {
                    if ($value['date'] >= $startDate && $value['date'] <= $endDate) {
                        $k++;
                        $record[$k]['org_name'] = $order->loan->borrower->org_name;
                        $record[$k]['type'] = '还款';
                        $record[$k]['sn'] = '';
                        $record[$k]['date'] = $value['date'];
                        $record[$k]['in_money'] = 0;
                        $orderBonusProfit = $order->getBonusProfit();
                        $record[$k]['out_money'] = bcadd(bcadd($value['principal'], $value['interest'], 2), $orderBonusProfit, 2);
                        $record[$k]['title'] = $order->loan->title;
                    }
                }
            }
        }
        $file = \Yii::getAlias('@app/runtime/Retention_'.$startDate.'_'.$endDate .'_'. date('YmdHis').'.xlsx');
        $exportData[] = ['融资方', '交易类型', '流水号', '交易日期', '入账金额', '出帐金额', '资金流向'];
        $exportData = array_merge($exportData, $record);
        $objPHPExcel = UserStats::initPhpExcelObject($exportData);
        $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($file);
        exit();
    }
}
