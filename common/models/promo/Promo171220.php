<?php

namespace common\models\promo;

use common\models\mall\PointRecord;
use common\models\user\User;
use common\service\PointsService;

class Promo171220 extends BasePromo
{
    public function secKill(User $user, $rewardSn)
    {
        $reward = Reward::fetchOneBySn($rewardSn);
        if (null === $reward) {
            throw new \Exception('商品不存在', 6);
        }
        if (null !== $reward->limit && $reward->limit <= 0) {
            throw new \Exception('商品已售罄', 7);
        }
        if (bccomp($user->points, $reward->ref_amount, 2) < 0) {
            throw new \Exception('积分不足', 5);
        }
        $isSecKilled = null !== Award::find()
            ->where(['promo_id' => $this->promo->id])
            ->andWhere(['user_id' => $user->id])
            ->andWhere(['reward_id' => $reward->id])
            ->one();
        if ($isSecKilled) {
            throw new \Exception('您已经完成秒杀了', 4);
        }

        $pointRecord = new PointRecord([
            'ref_type' => PointRecord::TYPE_PROMO,
            'decr_points' => $reward->ref_amount,
        ]);
        PointsService::addUserPoints($pointRecord, false, $user);

        return PromoService::award($user, $reward, $this->promo);
    }
}
