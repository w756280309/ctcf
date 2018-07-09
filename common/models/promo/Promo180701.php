<?php
namespace common\models\promo;

use common\models\mall\PointRecord;
use common\models\user\User;

class Promo180701 extends BasePromo
{
    public static $points = 200;//每次抽奖扣除积分
    //活动奖池配置
    public function getAwardPool()
    {
        return [
            '180701_C10' => '0.35',  //10元代金券
            '180701_P200' => '0.15',  //200积分
            '180701_PI_WEIDA' => '0.15',   //维达抽纸6连包
            '180701_PI_SHUKE' => '0.2',  //舒客炭丝牙刷
            '180701_PI_COCK' => '0.05',    //公鸡头清洁剂
            '180701_PI_DOVE' => '0.1',    //多芬洗漱套装
            '180701_PI_MARKET' => '0',    //50元超市卡
            '180701_PI_GOLD' => '0',    //周大福黄金手串
        ];
    }

    //积分抽奖
    public function pointDraw(User $user)
    {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            PointRecord::subtractUserPoints($user, self::$points);
            PromoLotteryTicket::initNew($user, $this->promo, 'pointDraw')->save(false);
            $ticket = PromoService::draw($this->promo, $user);
            $transaction->commit();

            return $ticket;
        } catch (\Exception $e) {
            $transaction->rollBack();

            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }
}
