<?php
namespace common\models\promo;

use common\event\OrderEvent;
use common\jobs\OrderQueueJob;
use common\models\order\OnlineOrder;
use wap\modules\promotion\models\RankingPromo;
use Yii;

class Promo180618 extends BasePromo
{
    //订单完成后根据年化金额发送相应的奖励，发奖操作在OrderQueueJob中实现
    public static function onOrderSuccess(OrderEvent $event)
    {
        $order = $event->order;
        $user = $order->user;
        $promo = RankingPromo::findOne(['key' => 'promo_180618']);
        if (null === $promo) {
            return ;
        }
        $promo180618 = new self($promo);
        if ($order->status !== OnlineOrder::STATUS_SUCCESS || !$promo->isActiveInEvent($user, $order->order_time)) {
            return ;
        }
        $userAnnualInvest = $promo180618->calcUserAmount($user);
        $rewards = $promo180618->getRewards($userAnnualInvest);
        foreach ($rewards as $key => $value) {
            if (!$value) {
                continue;
            }
            $reward = Reward::fetchOneBySn($key);
            $award = Award::findByPromoUser($promo, $user)
                ->andWhere(['reward_id' => $reward->id])
                ->one();
            if ($award === null) {
                Yii::$app->queue2->push(new OrderQueueJob([
                    'sn' => $key,
                    'userId' => $user->id,
                    'promoId' => $promo->id,
                ]));
            }
        }
    }

    //根据用户的年化金额判断是否发送某种奖励
    private function getRewards($userAnnualInvest)
    {
        return [
            '180618_P66' => $userAnnualInvest >= 10000 ? true : false,
            '180618_P266' => $userAnnualInvest >= 20000 ? true : false,
            '180618_P466' => $userAnnualInvest >= 100000 ? true : false,
            '180618_RP38' => $userAnnualInvest >= 200000 ? true : false,
            '180618_G100' => $userAnnualInvest >= 500000 ? true : false,
        ];
    }
}
