<?php

namespace common\models\promo;

use common\models\transfer\Transfer;
use common\models\user\User;
use common\models\user\UserInfo;
use wap\modules\promotion\models\RankingPromo;
use Yii;

class Promo170520
{
    public $promo;
    private static $rewardPromo;

    public function __construct(RankingPromo $promo)
    {
        $this->promo = $promo;
        $promo = RankingPromo::findOne(['key' => 'promo_201705']);
        //判断promo不存在，报错
        if (null === $promo) {
            throw new \Exception('未找到活动');
        }
        self::$rewardPromo = $promo;
    }

    private static function getPromo()
    {
        return self::$rewardPromo;
    }

    /**
     * 抽奖
     *
     * @param User $user
     * @return PromoLotteryTicket
     * @throws \Exception
     */
    public function draw(User $user)
    {
        //判断该用户是否参与了活动
        $this->promo->isActive($user);

        //根据用户选择对应的奖池设置数组（private）
        $poolSetting = $this->getPoolByUser($user);

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
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            //LotteryTicket
            $promo = self::getPromo();
            $sql = "select * from promo_lottery_ticket where user_id = :userId and promo_id = :promoId and isDrawn = :isDrawn limit 1";
            $lottery = $db->createCommand($sql, [
                'userId' => $user->id,
                'promoId' => $promo->id,
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

        //发奖环节，调用Transfer的AR类init方法初始化并保存（后台脚本执行发红包）
        $lottery = $this->reward($user, $reward, $lottery['id']);

        return $lottery;
    }

    private function reward($user, $reward, $lotteryId)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $lottery = PromoLotteryTicket::findOne($lotteryId);
        try {
            //更新抽奖机会的发奖状态
            if ($lottery->isRewarded) {
                throw new \Exception('1000:已发奖');
            }
            $lottery->isRewarded = true;
            $lottery->rewardedAt = time();
            $lottery->save(false);

            //将记录写入红包队列transfer表
            $metadata['promo_id'] = $this->promo->id;
            Transfer::initNew($user, $reward->ref_amount, $metadata)->save();
            $transaction->commit();
        } catch (\Exception $ex) {
            $transaction->rollBack();
        }

        return $lottery;
    }

    private function getPoolByUser($user)
    {
        //获得用户累计年化金额
        $promo = self::getPromo();
        $startDate = (new \DateTime($promo->startTime))->format('Y-m-d');
        $endDate = (new \DateTime($promo->endTime))->format('Y-m-d');
        $annualInvestment = UserInfo::calcAnnualInvest($user->id, $startDate, $endDate);
        $pool = [];
        $flag = 0;
        $packet520 = [];

        if ($annualInvestment <= 10000) {
            $pool = [
                'packet_0.66' => '0.8',
                'packet_0.88' => '0.2',
            ];
        } elseif ($annualInvestment > 10000 && $annualInvestment < 100000) {
            $pool = [
                'packet_0.66' => '0.1',
                'packet_0.88' => '0.2',
                'packet_1.66' => '0.2',
                'packet_1.88' => '0.2',
                'packet_2.66' => '0.1',
                'packet_2.88' => '0.1',
                'packet_5.2' => '0.0899',
                'packet_16' => '0.01',
            ];
            $flag = 1;
            $packet520 = ['packet_520_1' => '0.0001'];
        } elseif ($annualInvestment >= 100000 && $annualInvestment < 1000000) {
            $pool = [
                'packet_2.66' => '0.1',
                'packet_2.88' => '0.1',
                'packet_5.2' => '0.299',
                'packet_16' => '0.3',
                'packet_52' => '0.1',
                'packet_66' => '0.05',
                'packet_88' => '0.05',
            ];
            $flag = 3;
            $packet520 = ['packet_520_3' => '0.001'];
        } elseif ($annualInvestment >= 1000000) {
            $pool = [
                'packet_5.2' => '0.3',
                'packet_16' => '0.3',
                'packet_52' => '0.2',
                'packet_66' => '0.1',
                'packet_88' => '0.09',
            ];
            $flag = 10;
            $packet520 = ['packet_520_10' => '0.01'];
        }

        if (!$this->hasRewarded520($user, $promo->id)) {
            if ($flag > 0) {
                $reward = Reward::find()
                    ->where(['promo_id' => $promo->id])
                    ->andWhere(['sn' => 'packet_520_' . $flag])
                    ->andWhere(['>', 'limit', 0])
                    ->one();
                if (null !== $reward) {
                    $pool = array_merge($pool, $packet520);
                }
            }
        }

        return $pool;
    }

    private function hasRewarded520($user, $promoId)
    {
        $sns = ['packet_520_1', 'packet_520_3', 'packet_520_10'];
        $rewardIds = Reward::find()
            ->select('id')
            ->where(['in', 'sn', $sns])
            ->andWhere(['promo_id' => $promoId])
            ->column();

        $lottery520Count = PromoLotteryTicket::findLotteryByPromoId($promoId)
            ->andWhere(['user_id' => $user->id])
            ->andWhere(['in', 'reward_id', $rewardIds])
            ->andWhere(['isDrawn' => true])
            ->count();

        return $lottery520Count > 0;
    }

    /**
     * 获得该用户的剩余抽奖机会
     *
     * @param User $user
     *
     * @return int
     */
    public function getRestTicketCount(User $user)
    {
        return (int) PromoLotteryTicket::find()->where(['promo_id' => self::getPromo()->id, 'isDrawn' => false, 'user_id' => $user->id])->count();
    }

    /**
     * 获取活动奖品列表(后台活动中奖记录)
     *
     * @return array
     */
    public static function getAwardList()
    {
        $list = Reward::find()
            ->where(['promo_id' => self::getPromo()->id])
            ->indexBy('id')
            ->asArray()
            ->all();

        return $list;
    }

    /**
     * 获得某个奖品信息(后台活动中奖记录)
     *
     * @param int $awardId 额外校验
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
        $awardId = (int) $awardId;
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
            ->where(['promo_id' => self::getPromo()->id])
            ->andWhere(['user_id' => $user->id])
            ->andWhere(['isRewarded' => true])
            ->all();
    }
}
