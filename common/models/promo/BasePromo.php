<?php

namespace common\models\promo;

use common\models\order\OnlineOrder;
use common\models\user\User;
use wap\modules\promotion\models\RankingPromo;

class BasePromo
{
    public $promo;

    public function __construct(RankingPromo $promo)
    {
        $this->promo = $promo;
    }

    /**
     * 获取某个用户剩余抽奖机会
     *
     * @param User $user
     *
     * @return int
     */
    public function getActiveTicketCount(User $user)
    {
        return (int) PromoLotteryTicket::find()
            ->where([
                'promo_id' => $this->promo->id,
                'isDrawn' => false,
                'user_id' => $user->id,
            ])->andWhere(['>=', 'expiryTime', date('Y-m-d H:i:s')])
            ->count();
    }

    /**
     * 获得某个人已中奖列表
     *
     * @param User $user 无需格外校验
     *
     * @return array
     */
    public function getDrawnList(User $user)
    {
        return PromoLotteryTicket::find()
            ->where(['promo_id' => $this->promo->id])
            ->andWhere(['user_id' => $user->id])
            ->andWhere(['isDrawn' => true])
            ->orderBy('drawAt desc')
            ->all();
    }

    /**
     * 用户首投订单添加ticket，可以设置是否给订单用户的邀请者也发送ticket
     *
     * @param OnlineOrder        $order           订单
     * @param PromoLotteryTicket $waitTicket      ticket
     * @param bool               $includedInviter 是否送邀请者
     *
     * @return bool
     */
    public function rewardTicketByOrderIsFirstInvest(OnlineOrder $order, PromoLotteryTicket $waitTicket, $includedInviter)
    {

    }
}
