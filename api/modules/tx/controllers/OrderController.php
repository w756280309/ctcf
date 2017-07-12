<?php

namespace api\modules\tx\controllers;

use common\models\tx\Order;

class OrderController extends Controller
{
    /**
     * 获取指定订单的还款计划(重新计算得到)
     * @param int $id       普通标的订单
     * @param int $amount   金额，以分为单位
     */
    public function actionRepayment($id, $amount = 0)
    {
        $order = Order::findOne($id);
        $loan = $order->loan;
        if (null !== $loan && null !== $order) {
            return $loan->getRepaymentPlan($amount > 0 ? $amount : $order->amount, $order->apr);
        } else {
            return;
        }
    }
}
