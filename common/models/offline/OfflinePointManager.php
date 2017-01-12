<?php

namespace common\models\offline;

use common\models\mall\PointRecord;
use common\utils\TxUtils;

class OfflinePointManager
{
    /**
     * 根据订单对象更新对应用户的积分和积分流水
     *
     * @param  order        $order 订单
     * @param  string       $type  积分类型
     * @throws \Exception
     */
    public function updatePoints($order, $type)
    {
        $user = OfflineUser::findOne($order->user_id);
        $record = PointRecord::find()->where([
            'user_id' => $user->id,
            'ref_type' => $type,
            'ref_id' => $order->id,
            'isOffline' => true,
        ])->one();
        if (empty($record)) {
            if ($type === PointRecord::TYPE_OFFLINE_POINT_ORDER) {
                $points = $order->points;
            } else {
                $points = max(1, ceil(bcdiv(bcmul($order->annualInvestment, 6, 14), 1000, 2)));
            }
            if (in_array($type, PointRecord::getDecrType())) {
                $points = 0 - $points;
            }
            $level = $order->user->level;
            $res = \Yii::$app->db->createCommand("UPDATE `offline_user` SET `points` = `points` + :points WHERE `id` = :userId", ['points' => $points, 'userId' => $user->id])->execute();
            if (!$res) {
                throw new \Exception('积分更新失败');
            }
            $user->refresh();
            $finalPoints = $user->points;
            $record = new PointRecord([
                'sn' => TxUtils::generateSn('OFF'),
                'user_id' => $user->id,
                'ref_type' => $type,
                'ref_id' => $order->id,
                'final_points' => $finalPoints,
                'recordTime' => date('Y-m-d H:i:s'),
                'userLevel' => $level,
                'isOffline' => true,
                'offGoodsName' => isset($order->offGoodsName) ? $order->offGoodsName : $order->loan->title,
            ]);
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
}
