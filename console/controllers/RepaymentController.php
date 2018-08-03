<?php

namespace console\controllers;

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
                        'in_money' => $plan->benxi,
                        'balance' => $lendAccount->available_balance,
                        'remark' => '第'.$plan->qishu.'期'.'本金:'.$plan->benjin.'元;利息:'.$plan->lixi.'元;',
                    ])
                    ->one();
                if (!is_null($mr)) {
                    echo $loan->title.'第'.$plan->qishu.'期'.$user->real_name.'回款流水已存在'.PHP_EOL;
                    continue;
                }

                $refundTime = strtotime($plan->actualRefundTime);
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
                        'balance' => $lendAccount->available_balance,
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
}
