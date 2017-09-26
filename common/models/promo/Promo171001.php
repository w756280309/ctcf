<?php

namespace common\models\promo;

use common\models\order\OnlineOrder;

class Promo171001 extends BasePromo
{
    const SOURCE_ORDER = 'order';
    public function doAfterSuccessLoanOrder(OnlineOrder $order)
    {
        $expireTime = new \DateTime($this->promo->endTime);
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $user = $order->user;
            $key = $this->promo->id . '-' . $user->id . '-' . self::SOURCE_ORDER;
            TicketToken::initNew($key)->save(false);
            PromoLotteryTicket::initNew($user, $this->promo, self::SOURCE_ORDER, $expireTime)->save(false);
            $transaction->commit();
        } catch (\Exception $ex) {
            $transaction->rollBack();
            throw $ex;
        }
    }

    public function getAwardPool($user, \DateTime $dateTime)
    {
        $pool = [
            '171001_c10' => '0.2',
            '171001_c20' => '0.2',
            '171001_c50' => '0.1',
            '171001_p18' => '0.2',
            '171001_p28' => '0.2',
            '171001_p68' => '0.05',
            '171001_xsy' => '0.05',
        ];

        return $pool;
    }
}
