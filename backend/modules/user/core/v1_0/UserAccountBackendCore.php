<?php

namespace backend\modules\user\core\v1_0;

use common\lib\bchelp\BcRound;
use common\models\user\RechargeRecord;
use common\models\user\DrawRecord;
use common\models\order\OnlineOrder;
use common\models\product\OnlineProduct;

/**
 * Desc 主要用于实时读取用户资金信息
 * Created by zhy.
 * User: zhy
 * Date: 15-11-19
 * Time: 下午4:02.
 */
class UserAccountBackendCore
{
    /**
     * 获取充值成功
     * return [
     *      count  次数
     *      sum    充值总额
     * ];.
     */
    public function getRechargeSuccess($uid)
    {
        bcscale(14);
        $data = RechargeRecord::find()
            ->select('count(id) as count, sum(fund) as sumRecharge')
            ->where(['status' => RechargeRecord::STATUS_YES, 'uid' => $uid])
            ->asArray()
            ->one();
        $sum_recharge = $data['sumRecharge'];
        $count = $data['count'];
        $bcround = new BcRound();
        $sum_recharge = $bcround->bcround($sum_recharge, 2);

        return ['count' => $count, 'sum' => $sum_recharge];
    }

    /**
     * 获取提现成功
     * return [
     *      count  次数
     *      sum    总额
     * ];.
     */
    public function getDrawSuccess($uid)
    {
        bcscale(14);
        $data = DrawRecord::find()
            ->select('count(id) as count, sum(money) as sum_draw')
            ->where(['status' => DrawRecord::STATUS_SUCCESS, 'uid' => $uid])
            ->asArray()
            ->one();
        $count = $data['count'];
        $sum_draw = $data['sum_draw'];
        $bcround = new BcRound();
        $sum_draw = $bcround->bcround($sum_draw, 2);

        return ['count' => $count, 'sum' => $sum_draw];
    }

    /**
     * 获取订单成功
     * return [
     *      count  次数
     *      sum    总额
     * ];.
     */
    public function getOrderSuccess($uid)
    {
        bcscale(14);
        $data = OnlineOrder::find()
            ->select('count(id) as count, sum(order_money) as sum_pay')
            ->where(['status' => OnlineOrder::STATUS_SUCCESS, 'uid' => $uid])
            ->asArray()
            ->one();
        $count = $data['count'];
        $sum_pay = $data['sum_pay'];
        $bcround = new BcRound();
        $sum_pay = $bcround->bcround($sum_pay, 2);

        return ['count' => $count, 'sum' => $sum_pay];
    }

    /**
     * 融资方项目信息 只统计满标成立还款中已还款的
     * return [
     *      count  次数
     *      sum    总额
     * ];.
     */
    public function getProduct($uid)
    {
        bcscale(14);
        $data = OnlineProduct::find()
            ->select('count(id) as count, sum(funded_money) as sum_pay')
            ->where(['del_status' => OnlineProduct::STATUS_USE, 'borrow_uid' => $uid, 'status' => [3, 5, 6, 7]])
            ->asArray()
            ->one();

        $count = $data['count'];
        $sum_pay = $data['sum_pay'];
        $bcround = new BcRound();
        $sum_pay = $bcround->bcround($sum_pay, 2);

        return ['count' => $count, 'sum' => $sum_pay];
    }


    /**
     * 还款金额信息
     * return [
     *      count  次数
     *      sum    总额
     * ];.
     */
    public function getReturnInfo($uid)
    {
        $sum_huan = (new \yii\db\Query())
                ->select('repayment.benxi')
                ->from(['online_repayment_record repayment'])
                ->innerJoin('online_product p', 'repayment.online_pid=p.id')
                ->where(['repayment.status' => [1, 2], 'p.borrow_uid' => $uid])->sum('benxi');

        $sum_wei = (new \yii\db\Query())
                ->select('plan.benxi')
                ->from(['online_repayment_plan plan'])
                ->innerJoin('online_product p', 'plan.online_pid=p.id')
                ->where(['plan.status' => 0, 'p.borrow_uid' => $uid])->sum('benxi');

        return ['yihuan' => (empty($sum_huan) ? '0.00' : $sum_huan), 'wait' => (empty($sum_wei) ? '0.00' : $sum_wei)];
    }
}
