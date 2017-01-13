<?php

namespace wap\modules\promotion\promo;

use common\models\order\OnlineOrder;
use common\models\product\OnlineProduct;
use common\models\user\User;
use wap\modules\promotion\models\RankingPromo as Promo;
use yii\web\NotFoundHttpException;
use common\models\promo\UserPromo;
use common\models\promo\PromoLotteryTicket;

class Promo160707
{
    private $reward_config = [
        1 => [1, 3],
        2 => [4, 6],
        3 => [7, 50],
        4 => [51, 70],
        5 => [71, 80],
        6 => [81, 85],
        7 => [86, 97],
        8 => [98, 98],
        9 => [99, 100],
    ];

    private $reward_yitou_config = [
        3 => [1, 50],
        4 => [51, 70],
        5 => [71, 80],
        6 => [81, 85],
        7 => [86, 97],
        8 => [98, 98],
        9 => [99, 100],
    ];

    public $promo_key;
    public $startAt;
    public $endAt;

    public function __construct(Promo $promo)
    {
        if ($promo->key && $promo->startTime && $promo->endTime) {
            $this->promo_key = $promo->key;
            $this->startAt = strtotime($promo->startTime);
            $this->endAt = strtotime($promo->endTime);
        } else {
            throw new NotFoundHttpException();
        }
    }

    /**
     * 种类           中奖概率%（未投、已投）     起投金额
     * 1、28元代金券   3      0                  1000
     * 2、50元代金券   3      0                  10000
     * 3、90元代金券   44    50                  50000
     * 4、120元代金券  20    20                  100000
     * 5、180元代金券  10    10                  200000
     * 6、888元礼包    5      5                  28元1张，50元1张，90元3张，120元3张，180元1张
     * 7、再抽一次     12    12
     * 8、再接再励     1      1
     * 9、京东卡       2      2                  100元
     * @param User $user
     * @return int|void
     */
    public function drawReward(User $user)
    {
        $config = $user->userIsInvested ? $this->reward_yitou_config : $this->reward_config;
        $pool = range(1, 100, 1);
        shuffle($pool);
        $number_sub = mt_rand(0, 99);
        $number = $pool[$number_sub];
        foreach ($config as $key => $value) {
            if ($number >= $value[0] && $number <= $value[1]) {
                return $key;
            }
        }
    }

    /**
     * 初始化三次抽奖机会
     * 从 user_promo 表判断用户是否有过初始化记录
     * @param User $user
     */
    public function initTicket(User $user)
    {
        //判断用户是否已经初始化过抽奖机会
        $user_promo = UserPromo::find()->where(['user_id' => $user->id, 'promo_key' => $this->promo_key])->one();
        if (null === $user_promo) {
            //如果用户没有初始化过抽奖机会，添加三次抽奖机会
            for ($i = 0; $i < 3; $i++) {
                $ticket = new PromoLotteryTicket([
                    'user_id' => $user->id,
                    'source' => 0,
                ]);
                $ticket->save();
            }
            $user_promo = new UserPromo([
                'user_id' => $user->id,
                'promo_key' => $this->promo_key,
            ]);
            $user_promo->save();
        }
    }

    /**
     * 投资完成之后获取给用户新增抽奖机会
     * @param OnlineOrder $order
     */
    public function onInvested(OnlineOrder $order)
    {
        //判断新增资产 新增资产每50000获得一次抽奖机会
        $newIncreasedAssert = OnlineProduct::getInvestmentIncreaseBetween($order->uid, date('Y-m-d H:i:s', $this->startAt), date('Y-m-d H:i:s', $this->endAt));
        if ($newIncreasedAssert >= 50000) {
            $num = intval($newIncreasedAssert / 50000);
            $count = PromoLotteryTicket::find()->where(['user_id' => $order->uid, 'source' => 1])->count();
            $chance = $num - $count;
            if ($chance > 0) {
                for ($i = 0; $i < $chance; $i++) {
                    $ticket = new PromoLotteryTicket([
                        'user_id' => $order->uid,
                        'source' => 1,
                    ]);
                    $ticket->save();
                }
            }
        }
        //判断累计资产
        $totalMoney = OnlineOrder::find()
            ->where(['>=', 'order_time', $this->startAt])
            ->andWhere(['<=', 'order_time', $this->endAt])
            ->andWhere(['status' => 1])
            ->andWhere(['uid' => $order->uid])
            ->sum('order_money');
        if ($totalMoney >= 200000) {
            $num = intval($totalMoney / 200000);
            $count = PromoLotteryTicket::find()->where(['user_id' => $order->uid, 'source' => 2])->count();
            $chance = $num - $count;
            if ($chance > 0) {
                for ($i = 0; $i < $chance; $i++) {
                    $ticket = new PromoLotteryTicket([
                        'user_id' => $order->uid,
                        'source' => 2,
                    ]);
                    $ticket->save();
                }
            }
        }
    }

    public static function getCouponConfig()
    {
        $config = [
            1 => '0009:1000-28',
            2 => '0010:10000-50',
            3 => '0010:50000-90',
            4 => '0010:100000-120',
            5 => '0010:200000-180',
        ];

        return $config;
    }

    public static function getDrawConfig()
    {
        $config = [
            1 => '28元代金券',
            2 => '50元代金券',
            3 => '90元代金券',
            4 => '120元代金券',
            5 => '180元代金券',
            6 => '888元礼包',
            7 => '再砸一次',
            8 => '再接再励',
            9 => '100元京东卡',
        ];

        return $config;
    }
}
