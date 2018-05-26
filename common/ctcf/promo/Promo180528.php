<?php

namespace common\ctcf\promo;

use common\models\user\User;
use common\models\promo\BasePromo;
use common\models\promo\Reward;
use common\models\promo\Award;
use common\models\promo\PromoLotteryTicket;

class Promo180528 extends BasePromo
{
    //奖池配置
    public function getAwardPool($user, $joinTime)
    {
        return [
            '180528_C3' => '0.3',  //3元代金券 2000起投
            '180528_C5' => '0.3',  //5元代金券 5000起投
            '180528_C8' => '0.15',   //8元代金券 10000起投
            '180528_C10' => '0.1',  //10元代金券 10000起投
            '1180528_C15' => '0.15',    //15元代金券  20000起投
        ];
    }

    public function checkDraw(User $user, $startTime, $endTime)
    {
        $startTime =strtotime($startTime);
        $endTime = strtotime($endTime);

        return PromoLotteryTicket::find()
            ->where(['promo_id' => $this->promo->id])
            ->andWhere(['user_id' => $user->id])
            ->andWhere(['isDrawn' => true])
            ->andWhere(['source' => 'red_packet'])
            ->andWhere(['between', 'created_at', $startTime, $endTime])
            ->one();
    }

    //判断红包雨活动状态
    public function getActiveStatus(User $user = null, $type)
    {
        $hour = date('H');
        $result = [];
        $date = strtotime('now');
        $startTime = strtotime($this->promo->startTime);
        $endTime = strtotime($this->promo->endTime);
        if ($date < $startTime) {
            return [
                'code' => 26,
                'message' => '活动未开始',
            ];
        } elseif ($date > $endTime) {
            return [
                'code' => 27,
                'message' => '活动已结束',
            ];
        }
        if ($hour >= 0 && $hour < 10) {
            $result['code'] = 20;
            $result['message'] = '10点场即将开启';
        } elseif ($hour >= 10 && $hour < 12) {
            $result['code'] = 0;
            $result['message'] = '红包雨进行中';
            if ($user && $type === 'click') {
                $isDrawn = $this->checkDraw($user, '10:00:00', '12:00:00');
                if ($isDrawn !== null) {
                    $result['code'] = 25;
                    $result['message'] = '您已玩过本场游戏，请等待16点场开启';
                }
            }
        } elseif ($hour >= 12 && $hour < 16) {
            $result['code'] = 22;
            $result['message'] = '16点场即将开启';
        } elseif ($hour >= 16 && $hour < 18) {
            $result['code'] = 0;
            $result['message'] = '红包雨进行中';
            if ($user && $type === 'click') {
                $isDrawn = $this->checkDraw($user, '16:00:00', '18:00:00');
                if ($isDrawn !== null) {
                    $result['code'] = 24;
                    $result['message'] = '您已玩过本场游戏，请等待明日10点场开启';
                }
            }
        } elseif ($hour >= 18 && $hour < 24) {
            $result['code'] = 23;
            $result['message'] = '今日红包雨已结束';
        }

        return $result;
    }

    /**
     * 根据来源获取用户剩余的抽奖信息
     * @param User $user    用户
     * @param $source    来源
     * @return array|null|\yii\db\ActiveRecord
     */
    public function getOneActiveTicket($promo_id, $user, $source)
    {
        return PromoLotteryTicket::find()
            ->where([
                'promo_id' =>$promo_id,
                'user_id' => $user->id,
                'isDrawn' => false,
                'source' => $source
            ])->andWhere(['>=', 'expiryTime', date('Y-m-d H:i:s')])
            ->one();
    }

    //红包雨奖品记录接口
    public function getAwardList(User $user)
    {
        $r = Reward::tableName();
        $a = Award::tableName();
        $awardSn = ['180528_C3', '180528_C5', '180528_C8', '180528_C10', '180528_C15'];

        return Award::find()
            ->select("$r.sn,$r.name,$r.ref_type,$r.ref_id,$r.path,$a.id as awardId, $a.amount as ref_amount,$a.createTime as awardTime")
            ->innerJoin($r, "$a.reward_id = $r.id")
            ->where(["$a.user_id" => $user->id])
            ->andWhere(["$a.promo_id" => $this->promo->id])
            ->andWhere(["$r.sn" => $awardSn])
            ->orderBy(["$a.createTime" => SORT_DESC])
            ->asArray()
            ->all();
    }
}
