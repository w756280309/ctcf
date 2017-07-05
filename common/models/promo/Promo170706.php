<?php

namespace common\models\promo;

use common\models\coupon\CouponType;
use common\models\coupon\UserCoupon;
use common\models\mall\PointRecord;
use common\models\transfer\Transfer;
use common\utils\TxUtils;
use wap\modules\promotion\models\RankingPromo;
use common\models\user\User;

class Promo170706
{
    public $promo;

    public function __construct(RankingPromo $promo)
    {
        $this->promo = $promo;
    }

    /**
     * @param User   $user
     * @param string $ticketSource
     *
     * @throws \Exception
     */
    public function addTicket(User $user, $ticketSource)
    {
        //判断被邀请用户是否参加了活动（有截止时间判断）
        $this->promo->isActive($user);

        //判断用户是否在活动时间被邀请
        if (!$user->isInvited($this->promo->startTime, $this->promo->endTime)) {
            throw new \Exception('非活动时间内被邀请客户');
        }

        //被邀请用户
        if (!$user->isIdVerified()) {
            throw new \Exception('还未实名认证');
        }

        //获取邀请者
        $inviteRecord = InviteRecord::find()
            ->where(['invitee_id' => $user->id])
            ->one();
        $inviteUser = $inviteRecord->user;

        //判断是否存在对应来源的抽奖机会，若存在，不应添加抽奖机会
        $ticketCount = (int) PromoLotteryTicket::findLotteryByPromoId($this->promo->id)
            ->andWhere(['user_id' => $inviteUser->id])
            ->andWhere(['source' => $ticketSource])
            ->count();

        //判断是否存在ticket
        if ($ticketCount > 0) {
            throw new \Exception('已添加过抽奖机会');
        }

        //添加抽奖机会
        PromoLotteryTicket::initNew($inviteUser, $this->promo, $ticketSource)->save();
    }

    private function getPool()
    {
        return [
            '0706_coupon_20' => '0.14',
            '0706_coupon_50' => '0.115',
            '0706_point_60' => '0.32',
            '0706_packet_1.88' => '0.34',
            '0706_lihe' => '0.07',
            '0706_humidifier' => '0.01',
            '0706_card_50' => '0.005',
            '0706_apple_watch' => '0',
            '0706_apple_phone' => '0',
        ];
    }

    public function draw(User $user)
    {
        //判断该用户是否参与了活动
        $this->promo->isActive($user);

        //判断是否抽过奖
        $drawnCount = (int) PromoLotteryTicket::findLotteryByPromoId($this->promo->id)
            ->andWhere(['user_id' => $user->id])
            ->andWhere(['isDrawn' => true])
            ->count();
        if ($drawnCount > 0) {
            throw new \Exception('3000:用户已抽奖!', 2);
        }

        //根据用户及已抽奖次数选择对应的奖池设置数组（private）
        $poolSetting = $this->getPool();

        //根据奖池设置数组获得一个奖品sn(reward::draw($poolSetting))
        $sn = Reward::draw($poolSetting);
        if (false === $sn) {
            throw new \Exception('2000:未抽到奖项，抽奖失败，请联系客服!');
        }
        $reward = Reward::findOne(['sn' => $sn]);
        if (null === $reward) {
            throw new \Exception('2001:无法获得奖品信息，抽奖失败，请联系客服!');
        }

        //抽奖环节 todo 可封装
        $db = \Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            //查询可用的LotteryTicket
            $sql = "select * from promo_lottery_ticket where user_id = :userId and promo_id = :promoId and isDrawn = :isDrawn limit 1";
            $lottery = $db->createCommand($sql, [
                'userId' => $user->id,
                'promoId' => $this->promo->id,
                'isDrawn' => false,
            ])->queryOne();
            if (empty($lottery)) {
                throw new \Exception('0000:无抽奖机会', 1);
            }

            //更新LotteryTicket
            $sql = "update promo_lottery_ticket set reward_id = :rewardId, isDrawn = :isDrawn, drawAt = :drawAt where id = :lotteryId and isDrawn = FALSE";
            $affectRows = $db->createCommand($sql, [
                'isDrawn' => true,
                'drawAt' => time(),
                'rewardId' => $reward->id,
                'lotteryId' => $lottery['id'],
            ])->execute();
            if (!$affectRows) {
                throw new \Exception('0001:抽奖失败，请联系客服!');
            }

            //减库存操作
            if (!reward::decStoreBySn($sn)) {
                $transaction->rollBack();
                throw new \Exception('0002:抽奖失败，请联系客服!');
            }

            $transaction->commit();
        } catch (\Exception $ex) {
            $transaction->rollBack();

            throw new \Exception($ex->getMessage(), $ex->getCode());
        }

        //发奖 todo 可封装
        $lottery = $this->reward($user, $reward, $lottery['id']);

        return $lottery;
    }

    public function reward($user, $reward, $lotteryId)
    {
        $lottery = PromoLotteryTicket::findOne($lotteryId);
        //如果非代金券及红包类型，直接返回Lottery
        if (!in_array($reward->ref_type, [Reward::TYPE_COUPON, Reward::TYPE_RED_PACKET, Reward::TYPE_POINT])) {
            return $lottery;
        }

        $db =  \Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            //更新抽奖机会的发奖状态
            if ($lottery->isRewarded) {
                throw new \Exception('1000:已发奖');
            }
            $lottery->isRewarded = true;
            $lottery->rewardedAt = time();
            $lottery->save(false);
            if (Reward::TYPE_RED_PACKET === $reward->ref_type) {
                //将记录写入红包队列transfer表队列
                $metadata['promo_id'] = $this->promo->id;
                Transfer::initNew($user, $reward->ref_amount, $metadata)->save();
            } elseif (Reward::TYPE_COUPON === $reward->ref_type) {
                //直接发放代金券
                $couponType = CouponType::findOne($reward->ref_id);
                if (null === $couponType) {
                    throw new \Exception('1001:未找到可发放的代金券');
                }
                UserCoupon::addUserCoupon($user, $couponType)->save();
            } elseif (Reward::TYPE_POINT === $reward->ref_type) {
                $point = $reward->ref_amount;
                if (!is_numeric($point) || $point <= 0) {
                    throw new \Exception('1003:积分应大于0');
                }
                $pointSql = "update user set points = points + :points where id = :userId";
                $num = $db->createCommand($pointSql, [
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
                    'ref_id' => $lottery->id,
                    'incr_points' => $point,
                    'userLevel' => $user->getLevel(),
                    'remark' => '活动获得',
                ]);
                $pointRecord->save(false);
            }
            $transaction->commit();
        } catch (\Exception $ex) {
            $transaction->rollBack();
        }

        return $lottery;
    }

    /**
     * @param User $user
     *
     * @return int
     */
    public function getRestTicketCount(User $user)
    {
        return (int) PromoLotteryTicket::find()->where(['promo_id' => $this->promo->id, 'isDrawn' => false, 'user_id' => $user->id])->count();
    }

    /**
     * 获取活动奖品列表(后台活动中奖记录)
     *
     * @return array
     */
    public static function getAwardList()
    {
        $list = [];
        $promo = RankingPromo::findOne(['key' => 'promo_170706']);
        if (null !== $promo) {
            $list = Reward::find()
                ->where(['promo_id' => $promo->id])
                ->indexBy('id')
                ->asArray()
                ->all();
        }

        return $list;
    }

    /**
     * 获得某个奖品信息(后台活动中奖记录)
     *
     * @param int $awardId 不需额外校验
     *
     * @return array
     *
     * [
     *      $reward_id => ['name' => '']
     *      ......
     * ]
     */
    public static function getAward($awardId)
    {
        $awardList = self::getAwardList();
        return isset($awardList[$awardId]) ? $awardList[$awardId] : [];
    }

    /**
     * 获得某个人已中奖列表
     *
     * @param User $user 无需格外校验
     *
     * @return array
     */
    public function getRewardedList(User $user)
    {
        return PromoLotteryTicket::find()
            ->where(['promo_id' => $this->promo->id])
            ->andWhere(['user_id' => $user->id])
            ->andWhere(['isDrawn' => true])
            ->orderBy('drawAt desc')
            ->all();
    }
}
