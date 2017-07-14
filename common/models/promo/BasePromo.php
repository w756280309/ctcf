<?php

namespace common\models\promo;

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
}
