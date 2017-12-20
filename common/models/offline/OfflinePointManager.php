<?php

namespace common\models\offline;

use common\models\mall\PointRecord;

class OfflinePointManager
{
    /**
     * 根据订单对象更新对应用户的积分和积分流水
     *
     * @param  object $order 订单
     * @param  string $type  积分类型
     *
     * @throws \Exception
     */
    public function updatePoints($order, $type)
    {
        $user = $order->user;
        $record = PointRecord::find()->where([
            'user_id' => $user->id,
            'ref_type' => $type,
            'ref_id' => $order->id,
            'isOffline' => true,
        ])->one();
        if (empty($record)) {
            $points = $this->getOrderPoints($order, $type);
            $res = \Yii::$app->db->createCommand("UPDATE `offline_user` SET `points` = `points` + :points WHERE `id` = :userId", ['points' => $points, 'userId' => $user->id])->execute();
            if (!$res) {
                throw new \Exception('积分更新失败');
            }
            $user->refresh();
            $record = PointRecord::initOfflineRecord($order, $type);
            if ($points > 0) {
                $record->incr_points = $points;
            } else {
                $record->decr_points = abs($points);
            }
            $res = $record->save();
            if (!$res) {
                throw new \Exception('积分流水更新失败');
            }
        }
    }

    /**
     * 根据订单对象和对应的积分流水类型获得该笔订单的积分
     *
     * @param  object $order 订单
     * @param  string $type  积分流水类型
     *
     * @return int    订单积分
     */
    private function getOrderPoints($order, $type)
    {
        if ($type === PointRecord::TYPE_OFFLINE_POINT_ORDER) {
            $points = $order->points;
        } else {
            $points = max(1, ceil(bcdiv(bcmul($order->annualInvestment, 6, 14), 1000, 2)));
            //新用户首投赠送1400积分
            $orders = OfflineOrder::find()->where(['user_id' => $order->user_id])->andWhere(['<', '	created_at', $order->created_at])->all();
            if (count($orders) == 0) {
                $points = bcadd($points, 1400, 2);
            }
        }
        if (in_array($type, PointRecord::getDecrType())) {
            $points = 0 - $points;
        }

        return (int) $points;
    }
}
