<?php

namespace common\models\promo;

class PromoMillion extends BasePromo
{
    public function getAwardPool($user, \DateTime $dateTime)
    {
        return [
            'mil_t_0.6' => '0.01',
            'mil_c_5' => '0.24',
            'mil_c_8' => '0.19',
            'mil_c_10' => '0.05',
            'mil_c_20' => '0.05',
            'mil_t_0.8' => '0.01',
            'mil_p_6' => '0.15',
            'mil_p_8' => '0.15',
            'mil_p_11' => '0.1',
            'mil_p_16' => '0.05',
        ];
    }

    public function addUserTicket($user, $source)
    {
        $expireTime = new \DateTime($this->promo->endTime);
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $key = $this->promo->id . '-' . $user->id . '-' . $source;
            if (null !== TicketToken::findOne(['key' => $key])) {
                throw new \Exception('重复插入', 23000);
            }
            TicketToken::initNew($key)->save(false);
            PromoLotteryTicket::initNew($user, $this->promo, $source, $expireTime)->save(false);
            $transaction->commit();
        } catch (\Exception $ex) {
            $transaction->rollBack();
            if (23000 !== (int)$ex->getCode()) {
                throw $ex;
            }
        }
    }
}