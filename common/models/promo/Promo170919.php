<?php

namespace common\models\promo;

use common\models\order\OnlineOrder;
use common\models\user\UserInfo;

class Promo170919 extends BasePromo
{
    private $orderAnnualLimit = 200000;
    private $sn = '170919_point_815';

    public function doAfterSuccessLoanOrder(OnlineOrder $order)
    {
        $user = $order->user;
        $reward = Reward::fetchOneBySn($this->sn);
        if (null === $reward) {
            return false;
        }
        $startDate = (new \DateTime($this->promo->startTime))->format('Y-m-d');
        $endDate = (new \DateTime($this->promo->endTime))->format('Y-m-d');
        $annualInvestment = UserInfo::calcAnnualInvest($user->id, $startDate, $endDate);
        $rewardNum = (int) Award::find()
            ->where(['promo_id' => $this->promo->id])
            ->andWhere(['user_id' => $user->id])
            ->count();
        $allNum = intval($annualInvestment / $this->orderAnnualLimit);
        $extraNum = max($allNum - $rewardNum, 0);
        for ($i = 1; $i <= $extraNum; $i++) {
            PromoService::award($user, $reward, $this->promo);
        }
    }
}
