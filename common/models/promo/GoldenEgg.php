<?php

namespace common\models\promo;

use common\models\coupon\CouponType;
use common\models\coupon\UserCoupon;
use common\models\order\OnlineOrder;
use common\models\user\User;
use wap\modules\promotion\models\RankingPromo;
use Yii;
use yii\web\Request;

class GoldenEgg
{
    public $promo;

    private $orderMoneyLimit = 50000;//累计订单金额超过此金额之后发送机会

    const SOURCE_INIT = 'init';//每人一次的机会
    const SOURCE_ORDER = 'order';//购买

    //奖品id设置
    const AWARD_1 = 1; //未投资用户首投奖品 1000-10元代金券
    const AWARD_2 = 2; //已投资用户首抽必中奖品 50000-50元代金券 以后每次概率为50%
    const AWARD_3 = 3; //第二次及以后每次 手机指环扣 20%
    const AWARD_4 = 4; //第二次及以后每次 食用油 10%
    const AWARD_5 = 5; //第二次及以后每次 888元大礼包（1000-28*1 10000-50*1 50000-90*3 100000-120*3 200000-180*1） 2%
    const AWARD_6 = 6; //第二次及以后每次 大米 12%
    const AWARD_7 = 7;  //第二次及以后每次 锅 1%
    const AWARD_8 = 8; //第二次以后每次 碗筷套装 5%

    //第二次抽奖奖池概率配置
    private $reward_yitou_pool = [
        2 => [1, 50],
        3 => [51, 70],
        4 => [71, 80],
        5 => [81, 82],
        6 => [83, 94],
        7 => [95, 95],
        8 => [96, 100],
    ];

    //获取活动奖品列表
    public static function getAwardList()
    {
        return [
            self::AWARD_1 => ['name' => '10元代金券', 'couponSn' => self::getCouponConfig(self::AWARD_1)],
            self::AWARD_2 => ['name' => '50元代金券', 'couponSn' => self::getCouponConfig(self::AWARD_2)],
            self::AWARD_3 => ['name' => '手机指环扣', 'couponSn' => ''],
            self::AWARD_4 => ['name' => '食用油', 'couponSn' => ''],
            self::AWARD_5 => ['name' => '888元大礼包', 'couponSn' => ''],
            self::AWARD_6 => ['name' => '水晶米', 'couponSn' => ''],
            self::AWARD_7 => ['name' => '不粘锅', 'couponSn' => ''],
            self::AWARD_8 => ['name' => '碗筷套装', 'couponSn' => ''],
        ];
    }

    //获取某个奖品信息
    public static function getAward($awardId)
    {
        $awardList = self::getAwardList();
        return isset($awardList[$awardId]) ? $awardList[$awardId] : '';
    }

    /**
     * 获取中奖信息
     *
     * 用于前台返回
     *
     * @return array
     */
    public static function getReturnList()
    {
        return [
            self::AWARD_1 => ['msg' => '获得10元代金券一张', 'isCoupon' => true],
            self::AWARD_2 => ['msg' => '获得50元代金券一张', 'isCoupon' => true],
            self::AWARD_3 => ['msg' => '获得手机指环扣一个', 'isCoupon' => false],
            self::AWARD_4 => ['msg' => '获得食用油一桶', 'isCoupon' => false],
            self::AWARD_5 => ['msg' => '获得888元大礼包一个', 'isCoupon' => true],
            self::AWARD_6 => ['msg' => '获得水晶米一袋', 'isCoupon' => false],
            self::AWARD_7 => ['msg' => '获得不粘锅一个', 'isCoupon' => false],
            self::AWARD_8 => ['msg' => '获得碗筷套装一套', 'isCoupon' => false],
        ];
    }

    //获取某个奖品信息-用于前台返回
    public static function getPrize($awardId)
    {
        $awardList = self::getReturnList();
        return isset($awardList[$awardId]) ? $awardList[$awardId] : '';
    }

    public function __construct(RankingPromo $promo)
    {
        $this->promo = $promo;
    }

    //给用户发放机会
    public function addTicket(User $user, $ticketSource, Request $request)
    {
        $promo = $this->promo;
        if ($promo->isActive($user)) {
            switch ($ticketSource) {
                case self::SOURCE_INIT :
                    $this->initUserTicket($user, $request);
                    break;
                default:
                    break;
            }
        }
    }

    //给用户初始化机会
    private function initUserTicket(User $user, Request $request)
    {
        $ticket = PromoLotteryTicket::findOne([
            'user_id' => $user->id,
            'source' => self::SOURCE_INIT,
            'promo_id' => $this->promo->id,
        ]);
        if (empty($ticket)) {
            $ticket = new PromoLotteryTicket([
                'user_id' => $user->id,
                'source' => self::SOURCE_INIT,
                'promo_id' => $this->promo->id,
                'ip' => $request->getUserIP(),
            ]);
            $ticket->save();
        }
    }

    //订单完成之后统一调用逻辑
    public function doAfterSuccessLoanOrder(OnlineOrder $order)
    {
        $promo = $this->promo;
        $user = $order->user;
        if ($promo->isActive($user)) {
            $money = OnlineOrder::find()
                ->where([
                    'uid' => $user->id,
                    'status' => OnlineOrder::STATUS_SUCCESS,
                ])
                ->andWhere(['>', 'order_time', strtotime($promo->startTime)])
                ->sum('order_money');
            $tickets = (int)PromoLotteryTicket::find()
                ->where([
                    'user_id' => $user->id,
                    'source' => self::SOURCE_ORDER,
                    'promo_id' => $this->promo->id,
                ])
                ->count();
            $allTicket = intval($money / $this->orderMoneyLimit);
            $extraTicket = max($allTicket - $tickets, 0);
            for ($i = 1; $i <= $extraTicket; $i++) {
                $ticket = new PromoLotteryTicket([
                    'user_id' => $user->id,
                    'source' => self::SOURCE_ORDER,
                    'promo_id' => $this->promo->id,
                ]);
                $ticket->save();
            }
        }
    }

    /**
     * 获得代金券的配置信息-key
     *
     * @return array
     */
    public static function getCouponConfig()
    {
        $config = [
            1 => '0021:1000-10',
            2 => '0021:50000-50',
            3 => '0021:1000-28',
            4 => '0021:10000-50',
            5 => '0021:50000-90',
            6 => '0021:100000-120',
            7 => '0021:200000-180',
        ];

        return $config;
    }

    /**
     * 第二次及以后抽奖，按概率获得抽奖ID
     */
    private function getYiTouRewardId()
    {
        $pool = $this->reward_yitou_pool;
        $number = mt_rand(1, 100);
        foreach ($pool as $key => $value) {
            if ($number >= $value[0] && $number <= $value[1]) {
                return $key;
            }
        }
    }

    private function getRewardIdByConfig($config)
    {
        $userIsInvested = $config['userIsInvested'];
        $isFirstDraw = $config['isDrawn'];
        if ($userIsInvested) {
            if ($isFirstDraw) {
                return self::AWARD_2;
            } else {
                return $this->getYiTouRewardId();
            }
        } else {
            if ($isFirstDraw) {
                return self::AWARD_1;
            }
        }
    }

    /**
     * 抽奖
     */
    public function draw(User $user)
    {
        $promo = $this->promo;

        $lottery = PromoLotteryTicket::find()->where(['user_id' => $user->id, 'isDrawn' => false, 'promo_id' => $promo->id])->one();

        if (null === $lottery) {
            throw new \Exception('没有抽奖机会了');
        }

        $config = [
            'userIsInvested' => $user->getUserIsInvested(),
            'isDrawn' => $this->isFirstDraw($user),
        ];

        $lottery->reward_id = $this->getRewardIdByConfig($config);
        $lottery->isDrawn = true;
        $lottery->drawAt = time();
        if ($lottery->save()) {
            try {
                if ($this->reward($user, $lottery)) {
                    return $lottery;
                }
            } catch(\Exception $ex) {
                throw new \Exception('发奖失败，请联系客服，客服电话' . \Yii::$app->params['platform_info.contact_tel']);
            }
        }

        throw new \Exception($lottery->getFirstError());
    }

    /**
     * 发奖
     */
    public function reward(User $user, PromoLotteryTicket $lottery)
    {
        $rewardId = (int) $lottery->reward_id;
        $couponConfig = self::getCouponConfig();
        $coupons = [];
        if ($rewardId === self::AWARD_1 || $rewardId === self::AWARD_2) {
            array_push($coupons, $rewardId);
        } else if ($rewardId === self::AWARD_5) {
            //1000-28*1 10000-50*1 50000-90*3 100000-120*3 200000-180*1
            $coupons = [3, 4, 5, 5, 5, 6, 6, 6, 7];
        }

        if (isset($coupons) && !empty($coupons)) {
            if (!$lottery->isRewarded) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    foreach ($coupons as $ckey) {
                        $couponType = CouponType::findOne(['sn' => $couponConfig[$ckey]]);
                        UserCoupon::addUserCoupon($user, $couponType)->save();
                    }
                    $lottery->rewardedAt = time();
                    $lottery->isRewarded = true;
                    $lottery->save();
                    $transaction->commit();
                    return true;
                } catch(\Exception $ex) {
                    $transaction->rollBack();
                    throw new \Exception('发放代金券异常');
                }
            }
        }

        return true;
    }

    /**
     * 判断是否为第一次抽奖
     */
    public function isFirstDraw(User $user)
    {
        return (int) PromoLotteryTicket::find()->where(['user_id' => $user->id, 'isDrawn' => true, 'promo_id' => $this->promo->id])->count() === 0;
    }

    /**
     * 获取一个人剩余多少次抽奖机会

     * @param  object User $user
     *
     * @return int
     */
    public function getRestTicketCount(User $user)
    {
        return (int) PromoLotteryTicket::find()->where(['promo_id' => $this->promo->id, 'isDrawn' => false, 'user_id' => $user->id])->count();
    }
}