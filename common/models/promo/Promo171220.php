<?php

namespace common\models\promo;

use common\models\mall\PointRecord;
use common\models\user\User;
use common\service\PointsService;
use Yii;

class Promo171220 extends BasePromo
{
    public function secKill(User $user, $rewardSn, $killTime)
    {
        $reward = Reward::fetchOneBySn($rewardSn);
        if (null === $reward) {
            throw new \Exception('商品不存在', 6);
        }
        if ($killTime < (new \DateTime($reward->createTime))) {
            throw new \Exception('秒杀未开始', 8);
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
            'ref_type' => PointRecord::TYPE_POINT_ORDER,
            'decr_points' => $reward->ref_amount,
        ]);

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $ticketToken = $this->promo->id . '-' . $user->id . '-' . $rewardSn;
            TicketToken::initNew($ticketToken)->save(false);
            PointsService::addUserPoints($pointRecord, false, $user);
            PromoService::award($user, $reward, $this->promo);
            $transaction->commit();

            return $reward;
        } catch (\Exception $ex) {
            $transaction->rollback();
            throw $ex;
        }
    }
}
