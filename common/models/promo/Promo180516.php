<?php
namespace common\models\promo;

use common\models\user\User;
use wap\modules\promotion\models\RankingPromo;

class Promo180516 extends BasePromo
{
    //奖池配置
    public function getAwardPool($user, $joinTime)
    {
        return [
            '180516_C50' => '0.15',  //50元代金券 100000起投
            '180516_C20' => '0.15',  //20元代金券 20000起投
            '180516_C15' => '0.2',   //15元代金券 20000起投
            '180516_C10' => '0.15',  //10元代金券 10000起投
            '180516_C8' => '0.2',    //8元代金券  10000起投
            '180516_C5' => '0.15',    //5元代金券  5000起投
        ];
    }

    /*
     * 获取本轮该阶段抽奖结果
     * @param $user
     * @return int 2：抽奖时间错误
     * */
    public function getDrawDuration(User $user)
    {
        $now = date('H');
        $minute = date('i');
        $second = date('s');
        $isLimit = (($now == 12 || $now == 18) && $minute == '00' && $second >= 0 && $second <= 30) ? true : false;//时间临界点限制 临界时间点30s之内均可以
        $duration = 2*60*60;
        if ($now >= 10 && $now < 12 || $isLimit) {
            $startTime = strtotime(date('Y-m-d 10:00:00'));
            $endTime = $startTime + $duration;
        } elseif ($now >= 16 && $now < 18 || $isLimit) {
            $startTime = strtotime(date('Y-m-d 16:00:00'));
            $endTime = $startTime + $duration;
        } else {
            return 2;
        }
        $count = PromoLotteryTicket::find()
            ->where(['promo_id' => $this->promo->id])
            ->andWhere(['user_id' => $user->id])
            ->andWhere(['isDrawn' => 1])
            ->andWhere(['>=', 'created_at', $startTime])
            ->andWhere(['<=', 'created_at', $endTime])
            ->count();
        return $count;
    }

    public function checkDraw(User $user)
    {
        $getDrawDuration = $this->getDrawDuration($user);
        if (2 == $getDrawDuration) {
            throw new \Exception('未在本轮红包雨时间段内', 11);
        } elseif ($getDrawDuration > 0) {
            throw new \Exception('本轮已抽奖', 10);
        }
    }

    /**
     * 获取用户活动中所有的中奖记录
     *
     * @param User $user 用户对象
     *
     * @return array
     */
    public function getAwardList(User $user)
    {
        $r = Reward::tableName();
        $a = Award::tableName();
        $charityId = RankingPromo::findOne(['key' => 'promo_180508'])->id;
        return Award::find()
            ->select("$r.sn,$r.name,$r.ref_type,$r.ref_id,$r.path,$a.id as awardId, $a.amount as ref_amount,$a.createTime as awardTime")
            ->innerJoin($r, "$a.reward_id = $r.id")
            ->where(["$a.user_id" => $user->id])
            ->andWhere(['or', "$a.promo_id =". $this->promo->id, "$a.promo_id = $charityId"])
            ->orderBy(["$a.createTime" => SORT_DESC])
            ->asArray()
            ->all();
    }
}
