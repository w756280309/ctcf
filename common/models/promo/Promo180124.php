<?php

namespace common\models\promo;

use common\models\offline\OfflineOrder;
use common\models\order\OnlineOrder;
use common\models\user\User;

class Promo180124 extends BasePromo
{
    /**
     * 订单成功后的调用，无返回值
     *
     * @param OnlineOrder|OfflineOrder $order 订单对象
     */
    public function doAfterSuccessLoanOrder($order)
    {
        $user = $order->onlineUser;

        //根据当前累计年化金额及最大次数为用户添加抽奖机会
        $annualAmount = $this->calcUserAmount($user);
        $maxTicketCount = $this->getMaxTicketCount($annualAmount);
        $this->sendTicketsByConfig($user, $annualAmount, null, $maxTicketCount);
    }

    private function getMaxTicketCount($annualAmount)
    {
        if ($annualAmount < 200000) {
            $maxTicketCount = 1;
        } elseif ($annualAmount >= 200000 && $annualAmount < 500000) {
            $maxTicketCount = 2;
        } else {
            $maxTicketCount = 3;
        }

        return $maxTicketCount;
    }

    /**
     * 获得奖池
     *
     * @param User $user 用户对象
     * @param \DateTime $dateTime 参与时间
     *
     * @return array
     * @throws \Exception
     */
    public function getAwardPool(User $user, \DateTime $dateTime)
    {
        $isDrawnCount = $this->getDrawnCount($user);
        if ($isDrawnCount >= 3) {
            throw new \Exception('已抽奖次数异常');
        } elseif ($isDrawnCount < 2) {
            return [
                '180124_C3' => '0.2',
                '180124_C5' => '0.2',
                '180124_C10' => '0.1',
                '180124_P16' => '0.15',
                '180124_P18' => '0.15',
                '180124_C8' => '0.2',
            ];
        }

        return [
            '180124_G50' => '1',
        ];
    }
}
