<?php

namespace common\models\promo;

use common\models\order\OnlineOrder;
use common\models\user\User;
use common\models\user\UserInfo;

class Promo180115 extends BasePromo
{
    private $annualInvestLimit = 50000;

    /**
     * 获得奖池
     *
     * @param User      $user     用户
     * @param \DateTime $joinTime 参与时间
     *
     * @return array
     */
    public function getAwardPool(User $user, \DateTime $dateTime)
    {
        $originPool = [
            '180115_G880' => '0.1',
            '180115_R5' => '0.15',
            '180115_P66' => '0.2',
            '180115_G50' => '0.01',
            '180115_R3' => '0.12',
            '180115_C36' => '0.12',
            '180115_P88' => '0.2',
            '180115_G680' => '0.1',
        ];
        $rewardSns = Reward::find()
            ->select('sn')
            ->where(['>', 'limit', 0])
            ->orWhere('`limit` is null')
            ->andWhere(['promo_id' => $this->promo->id])
            ->column();
        foreach ($originPool as $sn => $gv) {
            if (!in_array($sn, $rewardSns)) {
                unset($originPool[$sn]);
            }
        }

        return $originPool;
    }

    /**
     * 订单成功后执行方法
     *
     * @param OnlineOrder $order 订单对象
     *
     * @throws \Exception
     */
    public function doAfterSuccessLoanOrder(OnlineOrder $order)
    {
        //每5万年化添加一次抽奖机会
        //每次都会从活动开始时计算到当前时间的年化
        $user = $order->user;
        $startDate = (new \DateTime($this->promo->startTime))->format('Y-m-d');
        $endTime = new \DateTime($this->promo->endTime);
        $endDate = $endTime->format('Y-m-d');
        $annualInvestment = UserInfo::calcAnnualInvest($user->id, $startDate, $endDate);
        $rewardNum = (int) Award::find()
            ->where(['promo_id' => $this->promo->id])
            ->andWhere(['user_id' => $user->id])
            ->count();
        $allNum = intval($annualInvestment / $this->annualInvestLimit);
        $extraNum = max($allNum - $rewardNum, 0);
        for ($i = 1; $i <= $extraNum; $i++) {
            PromoLotteryTicket::initNew($user, $this->promo, 'order', $endTime)->save(false);
        }
    }
}
