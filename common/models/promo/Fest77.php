<?php

namespace common\models\promo;

use common\models\order\OnlineOrder;
use common\models\user\User;

class Fest77 extends BasePromo
{
    const SOURCE_ORDER = 'order';
    public function doAfterSuccessLoanOrder(OnlineOrder $order)
    {
        if (!$order->isFirstInvestment()) {
            return;
        }
        $this->rewardTicket($order->user);
        $this->rewardTicket($order->user->fetchInviter());
    }

    private function rewardTicket(User $user, \DateTime $expireTime = null)
    {
        if (null === $user) {
            return;
        }
        if (null === $expireTime) {
            $expireTime = new \DateTime($this->promo->endTime);
        }
        try {
            $key = $this->promo->id . '-' . $user->id . '-' . self::SOURCE_ORDER;
            TicketToken::initNew($key)->save(false);
            PromoLotteryTicket::initNew($user, $this->promo, self::SOURCE_ORDER, $expireTime)->save(false);
        } catch (\yii\db\IntegrityException $ex) {
            if ('23000' === $ex->getCode()) {
                return;
            }
            throw $ex;
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function getAwardPool($user, \DateTime $dateTime)
    {
        return [
            '170828_card_50' => '0.03',
            '170828_fare_50' => '0.01',
            '170828_scales' => '0.01',
            '170828_coupon_20' => '0.25',
            '170828_coupon_50' => '0.2',
            '170828_p_520' => '0.15',
            '170828_p_77' => '0.35',
        ];
    }
}
