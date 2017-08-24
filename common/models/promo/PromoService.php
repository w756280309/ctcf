<?php

namespace common\models\promo;

use common\exception\NotActivePromoException;
use common\models\code\GoodsType;
use common\models\coupon\CouponType;
use common\models\coupon\UserCoupon;
use common\models\mall\PointRecord;
use common\models\order\OnlineOrder;
use common\models\product\OnlineProduct;
use common\models\transfer\Transfer;
use common\models\user\CheckIn;
use common\models\user\User;
use common\utils\TxUtils;
use wap\modules\promotion\models\RankingPromo;
use Yii;

class PromoService
{
    /**
     * 获取对指定用户有效的活动[查找所有活动，活动是否有效在具体调用方法中自行判断]
     * @return array
     * @throws \Exception
     */
    private static function getActivePromo()
    {
        $promos = RankingPromo::find()->where('`key` is not null')->andWhere('promoClass is not null')->all();
        $data = [];
        foreach ($promos as $promo) {
            if (class_exists($promo->promoClass)) {
                $data[] = $promo;
            }
        }
        return $data;
    }

    /**
     * @param User $user 用户对象
     * @param $ticketSource string  抽奖机会来源
     * @throws \Exception
     */
    public static function addTicket(User $user, $ticketSource)
    {
        $promos = self::getActivePromo();
        foreach ($promos as $promo) {
            $model = new $promo->promoClass($promo);
            if (method_exists($model, 'addTicket')) {
                try {
                    $model->addTicket($user, $ticketSource, \Yii::$app->request);
                } catch (\Exception $ex) {

                }
            }
        }
    }

    /**
     * 给被被邀请者送代金券
     * @param User $user
     * @throws \Exception
     */
    public static function addInviteeCoupon(User $user)
    {
        $promos = self::getActivePromo();
        foreach ($promos as $promo) {
            $model = new $promo->promoClass($promo);
            if (method_exists($model, 'addInviteeCoupon')) {
                try {
                    $model->addInviteeCoupon($user);
                } catch (\Exception $ex) {

                }
            }
        }
    }

    /**
     * 标的订单完成之后的活动逻辑
     * @param OnlineOrder $order
     * @throws \Exception
     */
    public static function doAfterSuccessLoanOrder(OnlineOrder $order)
    {
        $promos = self::getActivePromo();
        foreach ($promos as $promo) {
            $model = new $promo->promoClass($promo);
            if (method_exists($model, 'doAfterSuccessLoanOrder') && self::orderIsInPromo($order, $promo)) {
                try {
                    $model->doAfterSuccessLoanOrder($order);
                } catch (\Exception $ex) {

                }
            }
        }
    }

    /**
     * 标的确认计息之后的活动统一调用逻辑
     * @param OnlineOrder $order
     * @throws \Exception
     */
    public static function doAfterLoanJixi(OnlineProduct $loan)
    {
        $promos = self::getActivePromo();
        foreach ($promos as $promo) {
            $model = new $promo->promoClass($promo);
            if (method_exists($model, 'doAfterLoanJixi')) {
                try {
                    $model->doAfterLoanJixi($loan);
                } catch (\Exception $ex) {

                }
            }
        }
    }

    /**
     * 绑卡后统一调用逻辑
     * @param User $user
     * @throws \Exception
     */
    public static function doAfterBindCard(User $user)
    {
        $promos = self::getActivePromo();
        foreach ($promos as $promo) {
            $model = new $promo->promoClass($promo);
            if (method_exists($model, 'doAfterBindCard')) {
                try {
                    $model->doAfterBindCard($user);
                } catch (\Exception $ex) {

                }
            }
        }
    }

    /**
     * 签到后统一调用逻辑
     * @param User $user
     * @throws \Exception
     */
    public static function doAfterCheckIn(CheckIn $checkIn)
    {
        $promos = self::getActivePromo();
        foreach ($promos as $promo) {
            $model = new $promo->promoClass($promo);
            if (method_exists($model, 'doAfterCheckIn')) {
                try {
                    $model->doAfterCheckIn($checkIn);
                } catch (\Exception $ex) {

                }
            }
        }
    }

    /**
     * 抽奖方法
     *
     * @param RankingPromo   $promo     活动
     * @param User           $user      用户
     * @param null|\DateTime $joinTime  活动参与时间
     *
     * @return PromoLotteryTicket
     * @throws \Exception
     */
    public static function draw(RankingPromo $promo, $user, \DateTime $joinTime = null)
    {
        if (null === $joinTime) {
            $joinTime = new \DateTime();
        }
        $promoClass = $promo->promoClass;

        if(!class_exists($promoClass)) {
            throw new \Exception('活动类未找到');
        }
        $promoModel = new $promoClass($promo);
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            //判断活动状态
            $promo->isActive($user, strtotime($joinTime->format('Y-m-d H:i:s')));

            //判断是否登录
            if(is_null($user)) {
                throw new NotActivePromoException($promo, '用户未登录', 3);
            }

            //判断抽奖机会
            $ticket = PromoLotteryTicket::fetchOneActiveTicket($promo, $user);
            if (is_null($ticket)) {
                throw new NotActivePromoException($promo, '无抽奖机会', 4);
            }

            //获取奖池
            $awardPool = $promoModel->getAwardPool($user, $joinTime);
            $awardSn = self::openLottery($awardPool);
            $reward = Reward::fetchOneBySn($awardSn);

            //标记抽奖机会为已抽奖
            self::markTicketDrawn($ticket, $reward);//sql 更新

            //发奖 - 暂不支持随机金额奖励的发放
            if (self::canAward($reward)) {
                $awardResult = self::award($user, $reward, $promo, $ticket);
                if (!$awardResult) {
                    throw new \Exception('发奖失败');
                }
            }

            //标记抽奖机会为已发奖
            self::markTicketAwarded($ticket, $reward);//sql 更新
            $transaction->commit();

            return $ticket;
        } catch (\Exception $ex) {
            $transaction->rollback();
            throw $ex;
        }
    }

    /**
     * @param User                    $user
     * @param Reward                  $reward
     * @param null|RankingPromo       $promo
     * @param null|PromoLotteryTicket $ticket
     *
     * @return bool
     * @throws \Exception
     */
    public static function award(User $user, Reward $reward, RankingPromo $promo = null, PromoLotteryTicket $ticket = null)
    {
        //减库存
        if (!reward::decStoreBySn($reward->sn)) {
            throw new \Exception('0002:抽奖失败，请联系客服!');
        }
        switch($reward->ref_type) {
            case Reward::TYPE_COUPON:
                $couponType = CouponType::findOne($reward->ref_id);
                if (null === $couponType) {
                    throw new \Exception('1001:未找到可发放的代金券');
                }
                $userCoupon = UserCoupon::addUserCoupon($user, $couponType);
                $userCoupon->save(false);
                Award::couponAward($user, $promo, $userCoupon, $ticket)->save(false);
                break;
            case Reward::TYPE_POINT:
                $point = $reward->ref_amount;
                if (!is_numeric($point) || $point <= 0) {
                    throw new \Exception('1003:积分应大于0');
                }
                $pointSql = "update user set points = points + :points where id = :userId";
                $num = Yii::$app->db->createCommand($pointSql, [
                    'points' => $point,
                    'userId' => $user->id,
                ])->execute();
                if ($num <= 0) {
                    throw new \Exception('1004:更新用户积分失败');
                }
                $user->refresh();
                $pointRecord = new PointRecord([
                    'user_id' => $user->id,
                    'sn' => TxUtils::generateSn('PROMO'),
                    'final_points' => $user->points,
                    'recordTime' => date('Y-m-d H:i:s'),
                    'ref_type' => PointRecord::TYPE_PROMO,
                    'ref_id' => $reward->id,
                    'incr_points' => $point,
                    'userLevel' => $user->getLevel(),
                    'remark' => '活动获得',
                ]);
                $pointRecord->save(false);
                Award::pointsAward($user, $promo, $pointRecord, $ticket)->save(false);
                break;
            case Reward::TYPE_PIKU:
                $goodsType = GoodsType::findOne($reward->ref_id);
                if (null !== $goodsType) {
                    Award::goodsAward($user, $promo, $goodsType, $ticket)->save(false);
                }
                break;
            case Reward::TYPE_RED_PACKET:
                $metadata['promo_id'] = $reward->promo_id;
                $transfer = Transfer::initNew($user, $reward->ref_amount, $metadata);
                $transfer->save(false);
                Award::transferAward($user, $promo, $transfer, $ticket)->save(false);
                break;
        }

        return true;
    }

    /**
     * 根据奖池获得获取一个奖品唯一标识
     *
     * @param array $awardPool
     *
     * @return bool|string
     */
    protected static function openLottery(array $awardPool)
    {
        //验证$awardPool是否为空，为空返回false
        if (empty($awardPool)) {
            return false;
        }

        //验证每抽奖概率的值必须为数值类型的字符串，且相加的和<=1，且过滤掉小于等于0的概率，否则返回false
        foreach ($awardPool as $k => $gaiLv) {
            if (!is_numeric($gaiLv) || !is_string($gaiLv)) {
                return false;
            }
            if ($gaiLv < 0) {
                return false;
            }
        }

        //验证当前的概率之和是否满足条件
        if (array_sum($awardPool) <= 0 || bccomp(array_sum($awardPool), 1, 4) > 0) {
            return false;
        }

        //构造奖池数组（若抽奖概率为0，不会出现在该奖池中）
        $pool = [];
        $minGaiLv = min($awardPool);
        $base = false !== strpos($minGaiLv, '.') ? strlen($minGaiLv) - (strpos($minGaiLv, '.') + 1) : 4;
        foreach ($awardPool as $item => $gv) {
            $num = $gv * pow(10, $base);
            for ($i = 0; $i < $num; $i++) {
                array_push($pool, $item);
            }
        }

        //获得一个key
        $poolLen = count($pool) - 1;
        if ($poolLen < 0) {
            return false;
        }

        return $pool[mt_rand(0, $poolLen)];
    }

    /**
     * 使用抽奖机会
     */
    protected static function markTicketDrawn(PromoLotteryTicket $ticket, Reward $reward)
    {
        $db = Yii::$app->db;
        $sql = "update promo_lottery_ticket set reward_id = :rewardId, isDrawn = :isDrawn, drawAt = :drawAt where id = :lotteryId and isDrawn = FALSE";
        $affectRows = $db->createCommand($sql, [
            'isDrawn' => true,
            'drawAt' => time(),
            'rewardId' => $reward->id,
            'lotteryId' => $ticket->id,
        ])->execute();
        $ticket->refresh();
        if (!$affectRows) {
            throw new \Exception('0001:抽奖失败，请联系客服!');
        }
    }

    protected static function markTicketAwarded($ticket, Reward $reward)
    {
        $db = Yii::$app->db;
        $sql = "update promo_lottery_ticket set isRewarded = :isRewarded, rewardedAt = :rewardedAt where id = :lotteryId and reward_id = :rewardId and isRewarded = FALSE";
        $affectRows = $db->createCommand($sql, [
            'isRewarded' => true,
            'rewardedAt' => time(),
            'rewardId' => $reward->id,
            'lotteryId' => $ticket->id,
        ])->execute();
        $ticket->refresh();
        if (!$affectRows) {
            throw new \Exception('0002:抽奖失败，请联系客服!');
        }
    }

    /**
     * 该奖品是否可以发 - 暂时没有限制，抽中就可发
     *
     * @param Reward $reward
     *
     * @return bool
     */
    protected static function canAward(Reward $reward)
    {
        return true;
    }


    /**
     * 是否为发生在活动中的订单
     *
     * @param OnlineOrder  $order 订单
     * @param RankingPromo $promo 活动
     *
     * @return bool
     * @throws \Exception
     */
    private static function orderIsInPromo(OnlineOrder $order, $promo)
    {
        if (OnlineOrder::STATUS_SUCCESS !== $order->status) {
            return false;
        }
        $user = $order->user;
        if (null === $user) {
            return false;
        }
        try {
            $isJoined = $promo->isActive($user, $order->order_time);
        } catch (NotActivePromoException $ex) {
            $isJoined = false;
        } catch (\Exception $ex) {
            $isJoined = false;
        }

        return $isJoined;
    }
}
