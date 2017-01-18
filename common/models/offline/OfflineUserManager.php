<?php

namespace common\models\offline;

use Yii;
use common\models\user\CoinsRecord;

class OfflineUserManager
{
    /**
     * 根据线下购买订单更新对应订单用户的累计年化投资金额及记录财富值流水
     *
     * @param  OfflineOrder $order 线下订单
     *
     * @throws \yii\db\Exception
     */
    public function updateAnnualInvestment(OfflineOrder $order)
    {
        $originalCoins = $order->user->coins;
        $annualInvestment = $order->isDeleted ? 0 - $order->annualInvestment : $order->annualInvestment;
        $res = Yii::$app->db->createCommand('update offline_user set annualInvestment = annualInvestment + '.$annualInvestment.' where id = '.$order->user->id)->execute();

        if ($res) {
            $user = OfflineUser::findOne($order->user->id);
            $currentCoins = $user->coins;

            if ($originalCoins !== $currentCoins) {
                $coins = new CoinsRecord([
                    'user_id' => $order->user->id,
                    'order_id' => $order->id,
                    'incrCoins' => bcsub($currentCoins, $originalCoins, 0),
                    'finalCoins' => $currentCoins,
                    'createTime' => date('Y-m-d H:i:s'),
                    'isOffline' => true,
                ]);

                $coins->save();
            }
        }
    }
}
