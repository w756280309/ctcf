<?php

namespace common\ctcf\promo;

use common\jobs\OrderQueueJob;
use common\models\offline\OfflineOrder;
use common\models\order\OnlineOrder;
use common\models\promo\Award;
use common\models\promo\BasePromo;
use common\models\promo\Reward;
use common\models\promo\TicketToken;
use common\models\user\User;
use yii\db\Query;

class Promo180912 extends BasePromo
{
    //订单完成后根据年化金额发送相应的奖励，发奖操作在OrderQueueJob中实现
    public function doAfterOrderSuccess($order)
    {
        if (null === $order) {
            return false;
        }
        if ($order instanceof OfflineOrder) {
            return false;
        }
        if ($order->loan->is_xs) {
            return false;
        }
        $user = $order->user;
        if ($order->status !== OnlineOrder::STATUS_SUCCESS
            || !$this->promo->isActiveInEvent($user, $order->order_time)
        ) {
            return false;
        }
        $userAnnualInvest = $this->calcUserAmount($user);
        $rewards = $this->getRewards($userAnnualInvest);
        foreach ($rewards as $sn => $isReward) {
            if (!$isReward) {
                continue;
            }
            $reward = Reward::fetchOneBySn($sn);
            $award = Award::findByPromoUser($this->promo, $user)
                ->andWhere(['reward_id' => $reward->id])
                ->asArray()
                ->one();
            if (empty($award)) {
                $key = $this->promo->id . '-' . $user->id . '-' . $sn;
                try {
                    TicketToken::initNew($key)->save(false);
                    \Yii::$app->queue2->push(new OrderQueueJob([
                        'sn' => $sn,
                        'userId' => $user->id,
                        'promoId' => $this->promo->id,
                    ]));
                } catch (\Exception $e) {
                    \Yii::info($e, 'promo_log');
                    continue;
                }
            }
        }

        return true;
    }

    //根据用户的年化金额判断是否发送某种奖励
    private function getRewards($userAnnualInvest)
    {
        return [
            '180912_RP6' => $userAnnualInvest >= 5000,
            '180912_RP16' => $userAnnualInvest >= 20000,
            '180912_RP28' => $userAnnualInvest >= 50000,
            '180912_RP50' => $userAnnualInvest >= 100000,
        ];
    }

    //获取用户已返现金额
    public function getCashAmount(User $user)
    {
        $amount = (new Query())
            ->from('award')
            ->where([
                'promo_id' => $this->promo->id,
                'user_id' => $user->id,
            ])->sum('amount');

        return $amount === null ? 0 : $amount;
    }
}
