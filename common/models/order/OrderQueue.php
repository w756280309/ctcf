<?php

namespace common\models\order;

use yii\behaviors\TimestampBehavior;

class OrderQueue extends \yii\db\ActiveRecord
{
    public static function tableName()
    {
        return 'orderqueue';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    public static function initForQueue(OnlineOrder $order)
    {
        if (OnlineOrder::STATUS_FALSE != $order->status) {
            throw new \Exception('订单状态不正确');
        }

        return new self([
            'orderSn' => $order->sn,
            'status' => 0,
        ]);
    }

    /**
     * 对应订单.
     *
     * @return order
     */
    public function getOrder()
    {
        return $this->hasOne(OnlineOrder::className(), ['sn' => 'orderSn']);
    }

    /**
     * 根据标的分组查找未处理的订单队列信息.
     */
    public static function findQueue()
    {
        bcscale(14);
        $rets = (new \yii\db\Query())
                ->select('q.orderSn,o.*,loan.*')
                ->from(['orderqueue q'])
                ->innerJoin('online_order o', 'o.sn=q.orderSn')
                ->innerJoin('online_product loan', 'loan.id=o.online_pid')
                ->where('q.status=0')->orderBy('o.id asc')->all();
        $loans = [];
        foreach ($rets as $ret) {
            $loans[$ret['online_pid']]['money'] = bcsub($ret['money'], $ret['funded_money']);//剩余可投的金额
            $loans[$ret['online_pid']]['data'][] = $ret;
        }

        return $loans;
    }
}
