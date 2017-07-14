<?php

namespace common\models\promo;

use common\models\user\User;
use wap\modules\promotion\models\RankingPromo;

class BasePromo
{
    public $promo;
    //todo - 后期promoKey会删除
    private static $promoKey;

    public function __construct(RankingPromo $promo)
    {
        $this->promo = $promo;
        self::$promoKey = $promo->key;
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
     * todo - 后期此代码会删除
     * 获取活动奖品列表(后台活动中奖记录)
     *
     * @return array
     */
    public static function getAwardList()
    {
        $list = [];
        $promo = RankingPromo::findOne(['key' => self::$promoKey]);
        if (null !== $promo) {
            $list = Reward::find()
                ->where(['promo_id' => $promo->id])
                ->indexBy('id')
                ->asArray()
                ->all();
        }

        return $list;
    }

    /**
     * todo - 后期此代码会删除
     * 获得某个奖品信息(后台活动中奖记录)
     *
     * @param int $awardId 不需额外校验
     *
     * @return array
     *
     * [
     *      $reward_id => ['name' => '']
     *      ......
     * ]
     */
    public static function getAward($awardId)
    {
        $awardList = self::getAwardList();
        return isset($awardList[$awardId]) ? $awardList[$awardId] : [];
    }
}
