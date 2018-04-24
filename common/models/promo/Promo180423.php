<?php

namespace common\models\promo;

use common\models\user\User;
use yii\helpers\ArrayHelper;

class Promo180423 extends BasePromo
{
    //判断是否有过一次抽奖机会,
    public function isDrawnTicket($user)
    {
        return PromoLotteryTicket::findLotteryByPromoId($this->promo->id)
            ->andWhere(['user_id' => $user->id])
            ->andWhere(['isDrawn' => true])
            ->andWhere(['isRewarded' => true])
            ->one();
    }

    /**
     * 修改动态奖池
     * @param User $user 用户
     * @param \DateTime $dateTime 参数时间
     */
    public function getAwardPool(User $user, \DateTime $dateTime)
    {
        $pool = [
            '180423_DSH' => '0.0001',   //小米恒温电水壶
            '180423_G50' => '0.001',    //50元超市卡
            '180423_LH' => '0.0089',    //五谷杂粮礼盒
            '180423_TTS' => '0.03',     //天堂伞
            '180423_GJTZ' => '0.18',    //意大利公鸡头皂
            '180423_CZ' => '0.2',       //维达抽纸六连包
            '180423_P200' => '0.23',    //200积分
            '180423_C20' => '0.35'      //20元代金券
        ];

        //设置总库存的奖池概率，当总库存数量为0时，将其概率加到代金券上
        $pool = $this->reviseStocksRate($pool, '180423_C20');

        //抽奖次数500次后，小米恒温水壶有获奖概率，200次后，50元超市卡有获奖概率，100次后，五谷杂粮礼盒有获奖概率，没有概率时将概率加到代金券上
        $query = PromoLotteryTicket::findLotteryByPromoId($this->promo->id)
            ->andWhere(['isDrawn' => true])
            ->andWhere(['isRewarded' => true]);
        $allTicketCount = (int)$query->count();
        if ($allTicketCount > 0 && $allTicketCount < 100) {
            $pool['180423_DSH'] = '0';
            $pool['180423_G50'] = '0';
            $pool['180423_LH'] = '0';
            $pool['180423_C20'] = (string)($pool['180423_C20']+0.01);

        } elseif ($allTicketCount >= 100 && $allTicketCount < 200) {
            $pool['180423_DSH'] = '0';
            $pool['180423_G50'] = '0';
            $pool['180423_C20'] =  (string)($pool['180423_C20']+0.0011);
        } elseif ($allTicketCount >= 200 && $allTicketCount < 500) {
            $pool['180423_DSH'] = '0';
            $pool['180423_C20'] = (string)($pool['180423_C20']+0.0001);
        }

        //奖品达到日库存上线时，概率为0,概率加到代金券上
        $p = PromoLotteryTicket::tableName();
        $r = Reward::tableName();
        $cquery = PromoLotteryTicket::find()
            ->innerJoin($r, "$r.id = $p.reward_id")
            ->select("count($p.id) as count, $r.sn as sn")
            ->where(["$p.promo_id" => $this->promo->id])
            ->andWhere(["$p.isDrawn" => true])
            ->andWhere(["$p.isRewarded" => true]);
        $dayTickets = $cquery
            ->andWhere(["date(from_unixtime($p.created_at))" => date('Y-m-d', time())])
            ->groupBy("$p.reward_id")
            ->having(['>', 'count', 0])
            ->asArray()
            ->all();
        if (count($dayTickets) > 0) {
            $dayTickets = ArrayHelper::index($dayTickets, 'sn');
            $dailyRewardStock = $this->dailyRewardStock();
            foreach ($dayTickets as $key => $value) {
                if (in_array($key, array_keys($dailyRewardStock))) {
                    if ($value['count'] >= $dailyRewardStock[$key]) {
                        $pool['180423_C20'] = (string)($pool['180423_C20'] + $pool[$key]);
                        $pool[$key] = '0';
                    }
                }
            }
        }
        //活动期间，单人最多能获得【小米恒温电水壶、50元超市卡、五谷杂粮礼盒】中的1件；单人最多能获得2件天堂伞。到达要求数量后概率清0,加到代金券上
        $bquery = clone $cquery;
        $userAwardRecord = $bquery
            ->andWhere(["$p.user_id" => $user->id])
            ->andWhere(["$r.sn" => ['180423_DSH', '180423_G50', '180423_LH', '180423_TTS']])
            ->groupBy("$p.reward_id")
            ->asArray()
            ->all();
        if (count($userAwardRecord) > 0) {
            $userAwardRecord = ArrayHelper::index($userAwardRecord, 'sn');
            foreach ($userAwardRecord as $key => $value) {
                if (in_array($key, ['180423_DSH', '180423_G50', '180423_LH'])) {
                    if ($userAwardRecord[$key]['count'] >= 1) {
                        $pool['180423_C20'] = (string)($pool['180423_C20']+$pool[$key]);
                        $pool['180423_DSH'] = '0';
                        $pool['180423_G50'] = '0';
                        $pool['180423_LH'] = '0';
                    }
                }
                if ($key === '180423_TTS' && $value['count'] >= 2) {
                    $pool['180423_TTS'] = '0';
                    $pool['180423_C20'] = (string)($pool['180423_C20'] + 0.03);
                }
            }
        }

        return $pool;
    }

    //奖品的日库存
    private function dailyRewardStock()
    {
        return [
            '180423_DSH' => 1,
            '180423_G50' => 1,
            '180423_LH' => 3,
            '180423_TTS' => 50,
            '180423_GJTZ' => 300,
            '180423_CZ' => 300,
        ];
    }
}