<?php
namespace common\ctcf\promo;

use common\models\user\User;
use common\models\promo\BasePromo;
use common\models\promo\Reward;
use common\models\promo\Award;

class Promo1805282 extends BasePromo
{
    //奖池配置
    public function getAwardPool($user, $joinTime)
    {
        return [
            '180528_C50' => '1',  //50元代金券 50000起投
        ];
    }

    /**
     * 秒杀记录接口
     *
     * @param User $user 用户对象
     *
     * @return array
     */
    public function getAwardList(User $user)
    {
        $r = Reward::tableName();
        $a = Award::tableName();

        return Award::find()
            ->select("$r.sn,$r.name,$r.ref_type,$r.ref_id,$r.path,$a.id as awardId, $a.amount as ref_amount,$a.createTime as awardTime")
            ->innerJoin($r, "$a.reward_id = $r.id")
            ->where(["$a.user_id" => $user->id])
            ->andWhere(["$a.promo_id" => $this->promo->id])
            ->orderBy(["$a.createTime" => SORT_DESC])
            ->asArray()
            ->all();
    }
}
