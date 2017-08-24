<?php

namespace common\models\promo;

use common\models\order\OnlineOrder;

class Fest77 extends BasePromo
{
    public function doAfterSuccessLoanOrder(OnlineOrder $order)
    {
        //$this->promo->isActive($order, $order->order_time);
        $waitTicket = new PromoLotteryTicket([
            'source' => 'order',
            'expiryTime' => $this->promo->endTime,
            'promo_id' => $this->promo->id,
        ]);
        $this->rewardTicketByOrderIsFirstInvest($order, $waitTicket, true);
    }
}