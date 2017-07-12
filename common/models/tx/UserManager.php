<?php

namespace common\models\tx;

use common\models\user\User;

class UserManager
{
    /**
     * 获得某人当前时间下持有某一标的金额（包括理财及债权）-在定时任务订单成功后调用该方法
     * 用于credit_order的buyerAmount与sellerAmount
     *
     * @param  string $loan_id 标的id
     * @param  User   $user    User对象
     *
     * @return string $amount 单位（分）
     */
    public function getLoanAmountByUser($loan_id, User $user)
    {
        //当前持有金额 = 所有订单金额之和 + 转让订单已买入之和 - 转让订单已转出之和
        $creditAmount = 0;
        $n = CreditNote::tableName();
        $co = CreditOrder::tableName();
        $user_id = (int) $user->id;
        $creditOrders = CreditOrder::find()->innerJoinWith('note')->where(["$co.user_id" => $user->id])->orWhere(["$n.user_id" => $user->id])->andWhere(["$co.status" => CreditOrder::STATUS_SUCCESS, "$n.loan_id" => $loan_id])->all();

        foreach ($creditOrders as $creditOrder) {
            if ($creditOrder->user_id === $user_id) {
                $creditAmount = bcadd($creditAmount, $creditOrder->principal, 0);
            } else if ($creditOrder->note->user_id === $user_id) {
                $creditAmount = bcsub($creditAmount, $creditOrder->principal, 0);
            }
        }

        $orderAmount = 0;
        $orders = Order::find()->where(['online_pid' => $loan_id, 'status' => Order::STATUS_SUCCESS, 'uid' => $user_id])->all();

        foreach ($orders as $order) {
            $orderAmount = bcadd($orderAmount, $order->order_money, 2);
        }

        //当前最终持有该标的的金额
        $amount = bcadd(bcmul($orderAmount, 100, 0), $creditAmount, 0);

        return $amount;
    }
}
