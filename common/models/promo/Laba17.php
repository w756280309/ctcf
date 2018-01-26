<?php

namespace common\models\promo;

class Laba17 extends BasePromo
{
    private $pointRewardSn = 'Laba_P600';
    private $annualLimit = 100000;

    public function doAfterSuccessLoanOrder($order)
    {
        $user = $order->onlineUser;
        $this->sendRewardByConfig($user, $this->pointRewardSn, $this->annualLimit);
    }
}