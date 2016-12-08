<?php

namespace common\models\promo;

use common\models\coupon\CouponType;
use common\models\coupon\UserCoupon;
use common\models\user\MoneyRecord;
use common\models\user\User;
use common\service\AccountService;
use common\service\SmsService;
use wap\modules\promotion\models\RankingPromo;
use yii\web\Request;

/**
 * 抽奖拉新活动：幸运双十二，疯抢Ipad
 */
class Promo1212
{
    const COUPON_FIVE_WEITOU = 1; //奖品-未投资-用户代金券-面值5元
    const COUPON_TEN_WEITOU = 2; //奖品-未投资-用户代金券-面值10元
    const COUPON_TWOFIVE_WEITOU = 3; //奖品-未投资-用户代金券-面值25元
    const COUPON_TEN_YITOU = 4; //奖品-已投资-用户代金券-面值10元
    const COUPON_TWOZERO_YITOU = 5; //奖品-已投资-用户代金券-面值20元
    const COUPON_FIVEZERO_YITOU = 6; //奖品-已投资-用户代金券-面值50元
    const CASH_TWO = 7; //奖品-未投资-现金红包-面值2元
    const CASH_THREE = 8; //奖品-未投资-现金红包-面值3元
    const CASH_FOUR = 9; //奖品-未投资-现金红包-面值4元
    const CASH_FIVE = 10; //奖品-未投资-现金红包-面值5元
    const GIFT_RING_BUKLE = 11; //奖品-实物-指环扣
    const GIFT_COOKING_OIL = 12; //奖品-实物-食用油
    const GIFT_RICE = 13; //奖品-实物-水晶大米

    const TOTALINVESTMENT_LEVEL_MONEY = 20000;

    public $promo;

    public function __construct(RankingPromo $promo)
    {
        $this->promo = $promo;
    }

    /**
     * 根据ticket对象获得对应的奖品配置信息
     *
     * @param  object PromoLotteryTicket $lottery
     *
     * @return array
     *
     * @throws \Exception
     */
    public static function getPrizeMessageConfig(PromoLotteryTicket $lottery)
    {
        $config = [
            1 => ['data' => '5元代金券一张', 'pic' => ''],
            2 => ['data' => '10元代金券一张', 'pic' => ''],
            3 => ['data' => '25元代金券一张', 'pic' => ''],
            4 => ['data' => '10元代金券一张', 'pic' => ''],
            5 => ['data' => '20元代金券一张', 'pic' => ''],
            6 => ['data' => '50元代金券一张', 'pic' => ''],
            7 => ['data' => '2元现金红包一个', 'pic' => ''],
            8 => ['data' => '3元现金红包一个', 'pic' => ''],
            9 => ['data' => '4元现金红包一个', 'pic' => ''],
            10 => ['data' => '5元现金红包一个', 'pic' => ''],
            11 => ['data' => '手机指环扣一个', 'pic' => ''],
            12 => ['data' => '俄罗斯原装进口食用油一桶', 'pic' => ''],
            13 => ['data' => '福临门水晶米一袋', 'pic' => ''],
        ];

        if (!isset($config[$lottery->reward_id])) {
            throw new \Exception('没有该奖品信息');
        }

        return $config[$lottery->reward_id];
    }

    /**
     * 获得代金券的配置信息-key
     *
     * @return array
     */
    public static function getCouponConfig()
    {
        $config = [
            1 => '0018:1000-5',
            2 => '0018:2000-10',
            3 => '0018:5000-25',
            4 => '0018:10000-10',
            5 => '0018:20000-20',
            6 => '0018:50000-50',
        ];

        return $config;
    }

    /**
     * 获得现金红包的金额配置信息-金额面值
     *
     * @return array
     */
    public static function getCashConfig()
    {
        $config = [
            7 => 2,
            8 => 3,
            9 => 4,
            10 => 5,
        ];

        return $config;
    }

    /**
     * 抽奖
     *
     * 调用方式：
     *
     * try {
     *    (new PromoTemp($promo))->draw($user)
     * } catch (\Exception $ex) {
     *    //message = $ex->getMessage();
     * }
     *
     *
     * @param  User   $user
     *
     * @return object $lottery
     *
     * @throws \Exception
     */
    public function draw(User $user)
    {
        $promo = $this->promo;

        $lottery = PromoLotteryTicket::find()->where(['user_id' => $user->id, 'isDrawn' => false, 'promo_id' => $promo->id])->one();

        if (null === $lottery) {
            throw new \Exception('没有抽奖机会了');
        }

        $config = [
            'totalInvestMent' => $user->getTotalInvestment(),
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
                throw new \Exception('发奖失败，请联系客服，客服电话' . \Yii::$app->params['contact_tel']);
            }

        }

        throw new \Exception($lottery->getFirstError());
    }

    /**
     * 判断某个人是否为第一次抽奖
     *
     * @param  object User $user
     *
     * @return bool
     */
    public function isFirstDraw(User $user)
    {
        return (int) PromoLotteryTicket::find()->where(['user_id' => $user->id, 'isDrawn' => true, 'promo_id' => $this->promo->id])->count() === 0;
    }

    /**
     * 获取一个人的奖品列表
     *
     * @param  object User $user
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getRewardList(User $user)
    {
        return PromoLotteryTicket::find()->where(['promo_id' => $this->promo->id, 'isDrawn' => true, 'user_id' => $user->id])->all();
    }

    public function getBoardList($limit = 10)
    {
        return PromoLotteryTicket::find()->where(['promo_id' => $this->promo->id, 'isDrawn' => true])->limit($limit)->orderBy(['drawAt' => SORT_DESC])->all();
    }

    /**
     * 获取一个人剩余多少次抽奖机会
     *
     * @param  object User $user
     *
     * @return int
     */
    public function getRestTicketCount(User $user)
    {
        return (int) PromoLotteryTicket::find()->where(['promo_id' => $this->promo->id, 'isDrawn' => false, 'user_id' => $user->id])->count();
    }

    /**
     * 根据是否抽奖、是否投资、累计投资额获得奖品的id
     *
     * @param  array $config
     *
     * @return int
     */
    private function getRewardIdByConfig($config)
    {
        $totalInvestMent = $config['totalInvestMent'];
        $isFirstDraw = $config['isDrawn'];

        if ($totalInvestMent > 0) {

            if ($totalInvestMent >= self::TOTALINVESTMENT_LEVEL_MONEY) {
                if ($isFirstDraw) {
                    $reward_id = mt_rand(self::GIFT_RING_BUKLE, self::GIFT_RICE);
                } else {
                    $reward_id = mt_rand(self::COUPON_TEN_YITOU, self::COUPON_FIVEZERO_YITOU);
                }
            } else {
                if ($isFirstDraw) {
                    $pool = array(
                        self::GIFT_RING_BUKLE,
                        self::GIFT_RING_BUKLE,
                        self::GIFT_COOKING_OIL,
                        self::GIFT_COOKING_OIL,
                        self::GIFT_RICE,
                    );
                    $reward_id = $pool[mt_rand(0, 4)];
                } else {
                    $reward_id = mt_rand(self::COUPON_TEN_YITOU, self::COUPON_TWOZERO_YITOU);
                }
            }
        } else {
            //未投资情况
            //判断该用户是否是第一次抽奖
            if ($isFirstDraw) {
                $reward_id = mt_rand(self::CASH_TWO, self::CASH_FIVE);
            } else {
                $reward_id = mt_rand(self::COUPON_FIVE_WEITOU, self::COUPON_TWOFIVE_WEITOU);
            }
        }

        return $reward_id;
    }

    /**
     * 发奖
     *
     * @param User $user
     * @param PromoLotteryTicket $lottery
     *
     * @return bool
     * @throws \Exception
     */
    public function reward(User $user, PromoLotteryTicket $lottery)
    {
        $rewardId = $lottery->reward_id;
        $couponConfig = self::getCouponConfig();
        $cashConfig = self::getCashConfig();
        $transaction = \Yii::$app->db->beginTransaction();

        if (isset($couponConfig[$rewardId]) && !empty($couponConfig[$rewardId])) {

            if (!$lottery->isRewarded) {
                $couponType = CouponType::findOne(['sn' => $couponConfig[$rewardId]]);
                try {
                    if (UserCoupon::addUserCoupon($user, $couponType)->save()) {
                        $lottery->rewardedAt = time();
                        $lottery->isRewarded = true;
                        if ($lottery->save()) {
                            $transaction->commit();
                            return true;
                        }
                    }
                } catch(\Exception $ex) {
                    $transaction->rollBack();
                    throw new \Exception('发放代金券异常');
                }
            }
        } elseif (isset($cashConfig[$rewardId]) && !empty($cashConfig[$rewardId])) {
            $cash = $cashConfig[$rewardId];
            $templateId = \Yii::$app->params['sms']['intro_redpacket'];
            $message = [
                $cash,
                \Yii::$app->params['contact_tel'],
            ];
            SmsService::send($user->mobile, $templateId, $message, $user);

            return true;
        }

        return true;
    }


    /**
     * 发红包
     *
     * @param User $user
     *
     * @throws \Exception
     */
    public function sendRedPacket(User $user)
    {
        $lottery = PromoLotteryTicket::find()->where(['promo_id' => $this->promo->id, 'isDrawn' => true, 'user_id' => $user->id, 'isRewarded' => false])->andWhere(['in', 'reward_id', array_keys(self::getCashConfig())])->one();

        $moneyRecord = (int) MoneyRecord::find()->where(['uid' => $user->id])->andWhere(['in', 'type', [MoneyRecord::TYPE_ORDER, MoneyRecord::TYPE_CREDIT_NOTE]])->count();

        //如果此人参加了该活动且现金红包未发，则该人只有一笔投资或转让的流水，即只发生一笔订单
        if (null !== $lottery && $moneyRecord === 1) {
            $db = \Yii::$app->db;
            $transaction = $db->beginTransaction();
            try {
                $cashConfig = self::getCashConfig();
                $cash = $cashConfig[$lottery->reward_id];
                $table = PromoLotteryTicket::tableName();

                $sql = 'update '.$table.' set isRewarded = true,rewardedAt = '.time().' where isRewarded = false and id = '.$lottery->id;

                $affected_rows = $db->createCommand($sql)->execute();
                if ($affected_rows > 0) {
                    if (AccountService::userTransfer($user, $cash)) {
                        $transaction->commit();
                        $sms = true;
                    } else {
                        $transaction->rollBack();
                    }
                }
            } catch (\Exception $ex) {
                $transaction->rollBack();
                throw new \Exception($ex->getMessage());
            }

            if (isset($sms) && $sms) {
                $templateId = \Yii::$app->params['sms']['roundabout_redpacket'];
                $message = [
                    $cash,
                    \Yii::$app->params['clientOption']['host']['wap'],
                ];
                SmsService::send($user->mobile, $templateId, $message, $user);
            }
        }
    }

    //给指定用户添加抽奖机会
    public function addTicket(User $user, $ticketSource, Request $request = null)
    {
        switch ($ticketSource) {
            case 'register'://新用户注册
                $this->addInviteTicketInternal($user, $request);//给邀请者送抽奖机会
                break;
            case 'init'://用户进入抽奖页面
                $this->addInitTicketInternal($user, $request);//用户进入抽奖页面给抽奖机会
                break;
        }
    }

    private function addInitTicketInternal(User $user, Request $request = null)
    {
        //获取用户初始化的抽奖机会
        $ticketCount = PromoLotteryTicket::find()->where(['user_id' => $user->id, 'source' => 'init', 'promo_id' => $this->promo->id])->count();
        if ($ticketCount === 0) {
            $this->addTicketInternal($user->id, 'init', $this->promo->id, empty($request) ? '' : $request->getUserIP());
        }
    }

    /**
     * 给邀请用户赠送抽奖机会
     * @param User $newUser 新注册用户
     * @param Request|null $request
     */
    private function addInviteTicketInternal(User $newUser, Request $request = null)
    {
        //获取邀请当前用户的人
        $inviteRecord = InviteRecord::find()->where(['invitee_id' => $newUser->id])->andWhere(['>=', 'created_at', $this->promo->startAt])->one();
        if (!empty($inviteRecord)) {
            $inviterId = $inviteRecord->user_id;
            //获取邀请者在活动期间邀请人数
            $inviteCount = InviteRecord::find()->where(['user_id' => $inviterId])->andWhere(['>=', 'created_at', $this->promo->startAt])->count();
            //获取当前用户因为邀请被赠送的抽奖机会
            $ticketCount = PromoLotteryTicket::find()->where(['user_id' => $inviterId, 'source' => 'invite', 'promo_id' => $this->promo->id])->count();
            //用户第一次邀请，给一次抽奖机会
            if ($inviteCount === 1 && $ticketCount === 0) {
                $this->addTicketInternal($inviterId, 'invite', $this->promo->id, empty($request) ? '' : $request->getUserIP());
            } elseif ($inviteCount > 1) {
                $deserveCount = $inviteCount * 2 - 1;//应该获取的机会 echo $deserveCount;die;
                $lastCount = $deserveCount - $ticketCount;//需要添加机会
                if ($lastCount > 0) {
                    for ($i = 1; $i <= $lastCount; $i++) {
                        $this->addTicketInternal($inviterId, 'invite', $this->promo->id, empty($request) ? '' : $request->getUserIP());
                    }
                }
            }
        }
    }

    private function addTicketInternal($userId, $source, $promoId, $ip = null)
    {
        $ticket = new PromoLotteryTicket([
            'user_id' => $userId,
            'source' => $source,
            'promo_id' => $promoId,
            'ip' => $ip,
        ]);
        $ticket->save(false);
    }
}
