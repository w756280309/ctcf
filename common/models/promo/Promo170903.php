<?php

namespace common\models\promo;

use common\models\order\OnlineOrder;
use common\models\user\UserInfo;

class Promo170903 extends BasePromo
{
    const SOURCE_ORDER = 'order';
    public function doAfterSuccessLoanOrder(OnlineOrder $order)
    {
        $expireTime = new \DateTime($this->promo->endTime);
        try {
            $user = $order->user;
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
        $startTime = new \DateTime($this->promo->startTime);
        $annualInvest = UserInfo::calcAnnualInvest($user->id, $startTime->format('Y-m-d'), $dateTime->format('Y-m-d'));
        if ($annualInvest <= 20000) {
            $pool = [
                '170903_c10' => '0.3',
                '170903_c20' => '0.15',
                '170903_p28' => '0.05',
                '170903_p18' => '0.05',
                '170903_p10' => '0.3',
                '170903_mddmp' => '0.1',
                '170903_qwsh' => '0.03',
                '170903_cz' => '0.02',
            ];
        } elseif ($annualInvest > 20000 && $annualInvest <= 100000) {
            $pool = [
                '170903_c10' => '0.15',
                '170903_c20' => '0.2',
                '170903_p68' => '0.1',
                '170903_p28' => '0.1',
                '170903_p18' => '0.3',
                '170903_mddmp' => '0.1',
                '170903_qwsh' => '0.02',
                '170903_yg' => '0.03',
            ];
        } else {
            $pool = [
                '170903_c20' => '0.1',
                '170903_p128' => '0.16',
                '170903_p68' => '0.3',
                '170903_mddmp' => '0.2',
                '170903_oil4' => '0.01',
                '170903_oil1.8' => '0.02',
                '170903_card50' => '0.01',
                '170903_qwsh' => '0.1',
                '170903_yg' => '0.1',
            ];
        }

        return $pool;
    }
}
