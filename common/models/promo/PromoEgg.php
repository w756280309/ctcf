<?php

namespace common\models\promo;
use common\models\coupon\CouponType;
use common\models\coupon\UserCoupon;
use common\models\mall\PointRecord;
use common\models\order\OnlineOrder;
use common\models\user\User;
use common\utils\TxUtils;
use wap\modules\promotion\models\RankingPromo;
use yii\helpers\ArrayHelper;
use yii\web\Request;

class PromoEgg
{
    public $promo;
    private $orderMoneyLimit = 30000;//累计订单金额超过此金额之后发送机会
    const SOURCE_INIT = 'init'; //每人一次的机会
    const SOURCE_ORDER = 'order'; //购买

    public function __construct(RankingPromo $promo)
    {
        $this->promo = $promo;
    }

    /**
     * 一次性奖品，抽过后不允许再抽了
     */
    public static function oncePrize()
    {
        return [
            'iphone7s',
            'mini4',
            'jdEcard',
        ];
    }

    //获取活动奖品列表
    public static function getAwardList()
    {
        $list = [];
        $promo = RankingPromo::findOne(['key' => 'promo_070410_egg']);
        if (null !== $promo) {
            $list = Reward::find()
                ->where(['promo_id' => $promo->id])
                ->indexBy('id')
                ->all();
        }
        return $list;
    }

    //获取某个奖品信息
    public static function getAward($awardId)
    {
        $awardList = self::getAwardList();
        return isset($awardList[$awardId]) ? $awardList[$awardId] : '';
    }

    /**
     * 获得奖品概率配置
     *
     * @param integer $type 1未投资用户首投奖池、2已投资用户首投、3用户复投、4用户复投且已抽到过一次iPhone7/ipad/jdECard
     *
     * @return array
     */
    public static function getPoolConfig($type)
    {
        $config = [];
        if (1 === $type) {
            $config = [
                'point10' => 1,
            ];
        } elseif (2 === $type) {
            $config = [
                'point50' => 0.25,
                'coupon20' => 0.75,
            ];
        } elseif (3 === $type) {

            $config = [
                'iphone7s' => 0.0001,
                'mini4' => 0.0001,
                'jdEcard' => 0.001,
                'dsnCup' => 0.08,
                'woema50' => 0.12,
                'yanmai' => 0.1988,
                'toothpaste' => 0.25,
                'point50' => 0.25,
                'coupon20' => 0.1,
            ];
        } else if (4 === $type) {
            $config = [
                'dsnCup' => 0.08,
                'woema50' => 0.12,
                'yanmai' => 0.2,
                'toothpaste' => 0.25,
                'point50' => 0.25,
                'coupon20' => 0.1,
            ];
        }

        return $config;
    }

    /**
     * 根据概率配置数组生成指定奖品的奖池
     */
    private function createPool(array $gailv)
    {
        $pool = [];
        $base = 10000;
        foreach ($gailv as $sn => $gv) {
            $num = $base * $gv;
            for ($i = 0; $i < $num; $i++) {
                array_push($pool, $sn);
            }
        }
        shuffle($pool);

        return $pool;
    }

    /**
     * 获得所有可发的奖品
     */
    private function getUsefulReward()
    {
        $reward = Reward::find()
            ->where(['promo_id' => $this->promo->id])
            ->andWhere(['>', 'limit', 0])
            ->all();

        return $reward;
    }

    /**
     * 根据配置获得要设置的奖池概率
     */
    private function getPoolByConfig($config)
    {
        $reward = $this->getUsefulReward();
        $usefulSn = ArrayHelper::getColumn($reward, 'sn');
        $userIsInvested = $config['userIsInvested'];
        $isFirstDraw = $config['isDrawn'];
        $isDrawOnce = $config['isDrawOnce'];
        if ($userIsInvested) {
            if ($isFirstDraw) {
                $gailv = self::getPoolConfig(2);
            } else {
                if ($isDrawOnce) {
                    $gailv = self::getPoolConfig(4);
                } else {
                    $gailv = self::getPoolConfig(3);
                }
            }
        } else {
            if ($isFirstDraw) {
                $gailv = self::getPoolConfig(1);
            }
        }

        $sns = array_keys($gailv);
        $finalSns = array_intersect($sns, $usefulSn);

        foreach ($gailv as $sn => $gv) {
            if (!in_array($sn, $finalSns)) {
                unset($gailv[$sn]);
            }
        }
        $pool = $this->createPool($gailv);

        return $pool;
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
                ->andWhere(['>=', 'order_time', strtotime($promo->startTime)])
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
                    'ip' => $user->registerIp,
                ]);
                $ticket->save();
            }
        }
    }

    /**
     * 抽奖
     */
    public function draw(User $user)
    {
        $promo = $this->promo;
        //判断抽奖机会
        $lottery = PromoLotteryTicket::find()->where(['user_id' => $user->id, 'isDrawn' => false, 'promo_id' => $promo->id])->one();
        if (null === $lottery) {
            throw new \Exception('没有抽奖机会了');
        }

        //获得当前可用奖池并抽奖得到奖品sn
        $config = [
            'userIsInvested' => $user->getUserIsInvested(),
            'isDrawn' => $this->isFirstDraw($user),
            'isDrawOnce' => $this->isDrawOnce($user),
        ];
        $pool = $this->getPoolByConfig($config);
        $zuobiao = count($pool) - 1;
        $number = mt_rand(0, $zuobiao);
        $rewardSn = $pool[$number];

        //获得奖品信息
        $reward = Reward::findOne(['sn' => $rewardSn]);

        //更新抽奖记录Ticket
        $db = \Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            $sql = "select id from promo_lottery_ticket where promo_id = {$promo->id} and user_id = {$user->id} and isDrawn = false limit 1 for update";
            $id = $db->createCommand($sql)->queryScalar();
            if (false === $id || null === $id) {
                throw new \Exception('没有抽奖机会了');
            }
            $db->createCommand("update promo_lottery_ticket set isDrawn=:isDrawn,drawAt=:drawAt,reward_id=:rewardId where id = :id", [
                'isDrawn' => true,
                'drawAt' => time(),
                'rewardId' => $reward->id,
                'id' => $id,
            ])->execute();
            $transaction->commit();
        } catch (\Exception $ex) {
            $transaction->rollBack();
            throw new \Exception($ex->getMessage());
        }

        //获得中奖记录
        $lottery = PromoLotteryTicket::findOne($id);
        if (null === $lottery) {
            throw new \Exception('发奖失败，未找到中奖记录');
        }

        //发奖
        try {
            if ($this->reward($user, $lottery)) {
                return $lottery;
            }
        } catch(\Exception $ex) {
            throw new \Exception('发奖失败，请联系客服，客服电话' . \Yii::$app->params['contact_tel']);
        }
    }

    /**
     * 发奖
     */
    public function reward(User $user, PromoLotteryTicket $lottery)
    {
        $reward = $lottery->reward;
        if (!isset($reward) || empty($reward)) {
            throw new \Exception('不存在奖品');
        }

        $db = \Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            //分两部分:减库存和更新ticket
            $sql = "select * from reward where id = :id and `limit` > 0 FOR UPDATE";
            $updateData = $db->createCommand($sql, ['id' => $reward->id])->queryOne();

            if (false !== $updateData) {
                $sqlUpdate = "update reward set `limit` = `limit` - 1 where id = :id ";
                $db->createCommand($sqlUpdate, ['id' => $reward->id])->execute();
            } else {
                $reward = Reward::findOne(['sn' => 'coupon20']);
                $sql1 = "update reward set `limit` = `limit` - 1 where id = :rewardId";
                $riceRes = $db->createCommand($sql1, ['rewardId' => $reward->id])->execute();
                if ($riceRes <= 0) {
                    throw new \Exception('发奖失败');
                }
                $lottery->reward_id = $reward->id;
            }
            //如果是实物奖品只需要更改库存的奖品的数量就可以了
            if (Reward::TYPE_PIKU === $reward->ref_type) {
                $transaction->commit();
                return true;
            }

            if (Reward::TYPE_POINT === $reward->ref_type) {
                $point = $reward->ref_amount;
                $pointSql = "update user set points = points + :points where id = :userId";
                $num = $db->createCommand($pointSql, [
                    'points' => $point,
                    'userId' => $user->id,
                ])->execute();
                if ($num <= 0) {
                    $transaction->rollBack();
                }
                $user->refresh();
                $pointRecord = new PointRecord([
                    'user_id' => $user->id,
                    'sn' => TxUtils::generateSn('PROMO'),
                    'final_points' => $user->points,
                    'recordTime' => date('Y-m-d H:i:s'),
                    'ref_type' => PointRecord::TYPE_PROMO,
                    'ref_id' => $lottery->id,
                    'incr_points' => $point,
                    'userLevel' => $user->getLevel(),
                    'remark' => '活动获得',
                ]);
                $pointRecord->save(false);
            }
            if (Reward::TYPE_COUPON === $reward->ref_type) {
                if (empty($reward->ref_id) || null === ($couponType = CouponType::findOne($reward->ref_id))) {
                    throw new \Exception('发奖失败，未找到指定的代金券类型');
                }
                UserCoupon::addUserCoupon($user, $couponType)->save(false);
            }
            $lottery->rewardedAt = time();
            $lottery->isRewarded = true;
            $lottery->save();
            $transaction->commit();
            return true;
        } catch(\Exception $ex) {
            $transaction->rollBack();
            throw new \Exception($ex->getMessage());
        }

        return true;
    }

    /**
     * 我的中奖记录
     */
    public function getRewardList(User $user)
    {
        return PromoLotteryTicket::find()->where(['user_id' => $user->id, 'isDrawn' => true, 'promo_id' => $this->promo->id])->orderBy(['drawAt' => SORT_DESC])->all();
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

    /**
     * 获取某个人是否获得过一次性奖品
     */
    public function isDrawOnce(User $user)
    {
        $rewardIds = Reward::find()
            ->select('id')
            ->where(['sn' => self::oncePrize()])
            ->column();

        return (int) PromoLotteryTicket::find()
            ->where(['user_id' => $user->id, 'isDrawn' => true, 'promo_id' => $this->promo->id])
            ->andWhere(['in', 'reward_id', $rewardIds])
            ->count() > 0;
    }
}
