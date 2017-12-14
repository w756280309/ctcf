<?php

namespace common\models\promo;

use common\models\user\User;

class Promo171213 extends BasePromo
{
    public function getRewardList(User $user, $rewardSns)
    {
        $r = Reward::tableName();
        $a = Award::tableName();
        return Award::find()
            ->select("$r.sn,$r.name,$r.ref_type,$r.ref_id,$r.path,$a.id as awardId, $a.amount as ref_amount,$a.createTime as awardTime")
            ->innerJoin($r, "$r.id = $a.reward_id")
            ->where(["$r.sn" => $rewardSns])
            ->andWhere(["$a.promo_id" => $this->promo->id])
            ->andWhere(["$a.user_id" => $user->id])
            ->asArray()
            ->all();
    }
}
