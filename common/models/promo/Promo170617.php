<?php

namespace common\models\promo;

use common\models\coupon\CouponType;
use common\models\coupon\UserCoupon;
use common\models\transfer\Transfer;
use common\models\user\User;
use wap\modules\promotion\models\RankingPromo;

class Promo170617
{
    public $promo;
    private $promoConfig;

    const SOURCE_REGISTER = 'register'; //新手注册
    const SOURCE_CALLOUT = 'callout'; //召集赠送

    public function __construct(RankingPromo $promo)
    {
        $this->promo = $promo;
        $this->promoConfig = json_decode($promo->config, true);
    }

    public function addTicket(User $user, $ticketSource)
    {
        //判断当前用户是否参加了活动（有截止时间判断）
        $this->promo->isActive($user);

        //判断ticket来源
        if (!in_array($ticketSource, [self::SOURCE_REGISTER, self::SOURCE_CALLOUT])) {
            throw new \Exception('未知的ticket来源');
        }

        //判断来源为召集的是否应该添加抽奖机会
        if (self::SOURCE_CALLOUT === $ticketSource) {
            $callout = Callout::find()
                ->where(['user_id' => $user->id])
                ->andWhere(['promo_id' => $this->promo->id])
                ->one();

            //没有发起召集或者召集响应人数<最大召集人数限制
            $config = $this->promoConfig;
            if (null === $callout || (null !== $callout && $callout->responderCount < $config['responderLimit'])) {
                throw new \Exception('未发起召集或召集相响应人数不足');
            }
        }

        //判断是否存在对应来源的抽奖机会，若存在，不应添加抽奖机会
        $ticketCount = (int) PromoLotteryTicket::findLotteryByPromoId($this->promo->id)
            ->andWhere(['user_id' => $user->id])
            ->andWhere(['source' => $ticketSource])
            ->count();

        //判断是否存在ticket
        if ($ticketCount > 0) {
            throw new \Exception('已添加过抽奖机会');
        }

        //添加抽奖机会
        PromoLotteryTicket::initNew($user, $this->promo, $ticketSource)->save();
    }

    private function getPoolByDrawnCount($user, $totalDrawnCount)
    {
        //根据档次获得原始的奖池概率
        $config = $this->promoConfig;
        $restDrawSn = $config['rest_draw_sn'];
        $trackLimitSns = $config['trackLimitSn'];
        if ($totalDrawnCount > 0 && $totalDrawnCount <= 50) {
            $pool = $config['draw_50'];
        } elseif ($totalDrawnCount > 50 && $totalDrawnCount <= 300) {
            $pool = $config['draw_300'];
        } elseif ($totalDrawnCount > 300 && $totalDrawnCount <= 1000) {
            $pool = $config['draw_1000'];
        } elseif ($totalDrawnCount > 1000 && $totalDrawnCount <= 3000) {
            $pool = $config['draw_3000'];
        } else {
            $pool = $config['draw_most'];
        }

        //获得不应该包含的sn数组
        $intersectSns = array_intersect($trackLimitSns, array_keys($pool));
        if ($this->hasRewarded($user, $trackLimitSns)) {
            $notSns = $intersectSns;
        } else {
            $notSns = [];
            if (!empty($intersectSns)) {
                $rewards = Reward::find()
                    ->where(['in', 'sn', $intersectSns])
                    ->andWhere(['promo_id' => $this->promo->id])
                    ->andWhere(['IS NOT', 'limit', null])
                    ->andWhere(['<=', 'limit', 0])
                    ->all();
                foreach ($rewards as $reward) {
                    $notSns[] = $reward->sn;
                }
            }
        }

        //将不应该包含的奖项概率加到红包1.66元上
        $tmp_gailv = 0;
        foreach ($notSns as $sn) {
            $tmp_gailv += $pool[$sn];
            unset($pool[$sn]);
        }

        if (isset($pool[$restDrawSn]) && $tmp_gailv > 0) {
            $pool[$restDrawSn] = strval($pool[$restDrawSn] + $tmp_gailv);
        }

        return $pool;
    }

    private function hasRewarded($user, $sns)
    {
        $rewardIds = Reward::find()
            ->select('id')
            ->where(['in', 'sn', $sns])
            ->andWhere(['promo_id' => $this->promo->id])
            ->column();

        $lotteryCount = PromoLotteryTicket::findLotteryByPromoId($this->promo->id)
            ->andWhere(['user_id' => $user->id])
            ->andWhere(['in', 'reward_id', $rewardIds])
            ->andWhere(['isDrawn' => true])
            ->count();

        return $lotteryCount > 0;
    }

    public function draw(User $user)
    {
        //判断该用户是否参与了活动
        $this->promo->isActive($user);

        //根据用户及已抽奖次数选择对应的奖池设置数组（private）
        $totalDrawnCount = $this->getTotalDrawnCount();
        $poolSetting = $this->getPoolByDrawnCount($user, $totalDrawnCount + 1);

        //根据奖池设置数组获得一个奖品sn(reward::draw($poolSetting))
        $sn = reward::draw($poolSetting);
        if (false === $sn) {
            throw new \Exception('2000:未抽到奖项，抽奖失败，请联系客服!');
        }
        $reward = Reward::findOne(['sn' => $sn]);
        if (null === $reward) {
            throw new \Exception('2001:无法获得奖品信息，抽奖失败，请联系客服!');
        }

        //抽奖环节
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
                throw new \Exception('0000:无抽奖机会');
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
            throw new \Exception($ex->getMessage());
        }

        //发奖
        $lottery = $this->reward($user, $reward, $lottery['id']);

        return $lottery;
    }

    public function reward($user, $reward, $lotteryId)
    {
        $lottery = PromoLotteryTicket::findOne($lotteryId);
        //如果非代金券及红包类型，直接返回Lottery
        if (!in_array($reward->ref_type, [Reward::TYPE_COUPON, Reward::TYPE_RED_PACKET])) {
            return $lottery;
        }

        $transaction = \Yii::$app->db->beginTransaction();
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
            } else {
                //直接发放代金券
                $couponType = CouponType::findOne($reward->ref_id);
                if (null === $couponType) {
                    throw new \Exception('1001:未找到可发放的代金券');
                }
                UserCoupon::addUserCoupon($user, $couponType)->save();
            }
            $transaction->commit();
        } catch (\Exception $ex) {
            $transaction->rollBack();
        }

        return $lottery;
    }

    private function getTotalDrawnCount()
    {
        return (int) PromoLotteryTicket::findLotteryByPromoId($this->promo->id)
            ->andWhere(['isDrawn' => true])
            ->count();
    }

    /**
     * 获取某个人还有多少次抽奖机会
     *
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
        $promo = RankingPromo::findOne(['key' => 'promo_170617']);
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
