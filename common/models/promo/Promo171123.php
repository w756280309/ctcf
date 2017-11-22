<?php

namespace common\models\promo;

use common\models\order\OnlineOrder;
use common\models\user\UserInfo;

class Promo171123 extends BasePromo
{
    private function getPromoConfig()
    {
        return json_decode($this->promo->config, true);
    }

    public function doAfterSuccessLoanOrder(OnlineOrder $order)
    {
        $config = $this->getPromoConfig();
        $user = $order->user;
        $reward = Reward::fetchOneBySn($config['rewardSn']);
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
        $allNum = intval($annualInvestment / $config['orderAnnualLimit']);
        $extraNum = max($allNum - $rewardNum, 0);
        for ($i = 1; $i <= $extraNum; $i++) {
            PromoService::award($user, $reward, $this->promo);
        }
    }
}
