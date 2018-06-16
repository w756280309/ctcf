<?php
namespace common\ctcf\promo;

use common\event\OrderEvent;
use common\jobs\OrderQueueJob;
use common\models\order\OnlineOrder;
use common\models\promo\Reward;
use common\models\promo\TicketToken;
use wap\modules\promotion\models\RankingPromo;
use Yii;
use common\models\promo\BasePromo;
use common\models\promo\PromoLotteryTicket;
use common\models\promo\Award;

class Promo180618 extends BasePromo
{
    //订单完成后根据年化金额发送相应的奖励，发奖操作在OrderQueueJob中实现
    public static function onOrderSuccess(OrderEvent $event)
    {
        $order = $event->order;
        $user = $order->user;
        $promo = RankingPromo::findOne(['key' => 'promo_1806181']);
        if (null === $promo) {
            return ;
        }
        $promo180618 = new self($promo);
        if ($order->status !== OnlineOrder::STATUS_SUCCESS || !$promo->isActiveInEvent($user, $order->order_time)) {
            return ;
        }

        $hasInvested = PromoLotteryTicket::fetchOneActiveTicket($promo, $user);
        if ($hasInvested === null) {
            PromoLotteryTicket::initNew($user, $promo)->save(false);
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
            '180618_C20' => $userAnnualInvest >= 0 ? true : false,
            '180618_P88' => $userAnnualInvest >= 10000 ? true : false,
            '180618_P388' => $userAnnualInvest >= 50000 ? true : false,
            '180618_RP28' => $userAnnualInvest >= 100000 ? true : false,
            '180618_G50' => $userAnnualInvest >= 200000 ? true : false,
        ];
    }
}
