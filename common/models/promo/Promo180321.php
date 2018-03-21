<?php

namespace common\models\promo;

use common\models\adv\Session;
use common\models\adv\ShareLog;
use common\models\user\User;
use Yii;

class Promo180321 extends BasePromo
{

    /**
     * 根据用户对象和参与答题时间获得用户应该答得问题
     *
     * @param User $user 用户对象
     *
     * @return array
     */
    public function getQuestions(User $user)
    {
        $currentCount = $this->getCurrentRewardedCount($user) + 1;
        $batchSn = 'batchNum' . $currentCount % 5;
        $questionsOrigin = Question::find()
            ->select(['id', 'title', 'batchSn as sn'])
            ->where(['batchSn' => $batchSn])
            ->asArray()
            ->all();
        $questionsBak = $questionsOrigin;

        //两次混合答题信息数组
        shuffle($questionsOrigin);
        shuffle($questionsBak);

        return array_merge($questionsOrigin, $questionsBak);
    }

    /**
     * 获得当前答题过程信息
     *
     * @param User $user 用户对象
     * @param \DateTime $joinTime 参与时间
     *
     * @return array
     */
    public function getAnswerInfo(User $user, \DateTime $joinTime)
    {
        $restTime = 30; //每轮秒数
        $restRealTime = 30; //实际倒计时秒数
        $result = [
            'status' => 0,
            'answer' => [],
        ]; //答题信息

        $currentTime = strtotime($joinTime->format('Y-m-d H:i:s')); //当前时间戳
        $redis = Yii::$app->redis_session;
        if ($redis->hexists('vernal equinox', $user->id)) {
            $lastTime = $redis->hget('vernal equinox', $user->id);
            $restRealTime = $currentTime - $lastTime;
        }

        $restCount = (int) PromoLotteryTicket::findLotteryByPromoId($this->promo->id)
            ->andWhere(['user_id' => $user->id])
            ->andWhere(['isDrawn' => true])
            ->andWhere(['isRewarded' => false])
            ->count();
        if ($restCount > 0 && $restRealTime >= 30) {
            $result['status'] = 1;
            $rewardCount = $this->getCurrentRewardedCount($user);
            $currentCount = $rewardCount + 1;
            $batchSn = 'batchNum' . $currentCount % 5;
            $sessionSn = 'batchNum' . $currentCount;
            $sessionCount = (int) Session::find()
                ->where(['batchSn' => $sessionSn])
                ->andWhere(['userId' => $user->id])
                ->count();
            $result['answer'] = [
                'sn' => $batchSn,
                'count' => $sessionCount,
            ];
        }

        return [
            'restTime' => $restRealTime < 30 && $restCount > 0 ? 30 - $restRealTime : $restTime,
            'result' => $result,
        ];
    }

    /**
     * 检查是否有答题机会
     *
     * @param User $user 用户对象
     * @param \DateTime $joinTime 参与时间
     *
     * @throws \Exception
     */
    public function checkRestTicket(User $user, \DateTime $joinTime)
    {
        $promoEndTime = new \DateTime($this->promo->endTime);
        try {
            $freeKey = $this->promo->id . '-'. $user->id . '-' . $joinTime->format('Ymd') . '-free';
            TicketToken::initNew($freeKey)->save(false);
            PromoLotteryTicket::initNew($user, $this->promo, 'free', $promoEndTime)->save(false);
        } catch (\Exception $ex) {
            $code = $ex->getCode();
            if (23000 !== (int) $code) {
                throw $ex;
            }
        }
        $shareLog = ShareLog::fetchByConfig($user, 'timeline', 'p180321', $joinTime);
        if (null !== $shareLog) {
            try {
                $shareKey = $this->promo->id . '-'. $user->id . '-' . $joinTime->format('Ymd') . '-share';
                TicketToken::initNew($shareKey)->save(false);
                PromoLotteryTicket::initNew($user, $this->promo, 'share', $promoEndTime)->save(false);
            } catch (\Exception $ex) {
                $code = $ex->getCode();
                if (23000 !== (int) $code) {
                    throw $ex;
                }
            }
        }

        $todayTickets = PromoLotteryTicket::findLotteryByPromoId($this->promo->id)
            ->andWhere(['user_id' => $user->id])
            ->andWhere(['date(from_unixtime(created_at))' => $joinTime->format('Y-m-d')])
            ->all();
        $drawnCount = 0;
        $ticketsCount = count($todayTickets);
        foreach ($todayTickets as $ticket) {
            if ($ticket->isDrawn) {
                $drawnCount++;
            }
        }
        if ($drawnCount === $ticketsCount) {
            if (2 === $drawnCount) {
                throw new \Exception('今日答题机会已用完', 5);
            } elseif (1 === $drawnCount) {
                throw new \Exception('分享以后可再次答题', 4);
            }
        }

        //将ticket用掉
        $ticket = PromoLotteryTicket::find()
            ->where(['promo_id' => $this->promo->id])
            ->andWhere(['user_id' => $user->id])
            ->andWhere(['isDrawn' => false])
            ->andWhere(['date(from_unixtime(created_at))' => $joinTime->format('Y-m-d')])
            ->one();

        if (is_null($ticket)) {
            throw new \Exception('无抽奖机会');
        }
        $db = Yii::$app->db;
        $sql = "update promo_lottery_ticket set isDrawn = :isDrawn, drawAt = :drawAt where id = :lotteryId and isDrawn = FALSE";
        $affectRows = $db->createCommand($sql, [
            'isDrawn' => true,
            'drawAt' => time(),
            'lotteryId' => $ticket->id,
        ])->execute();
        if (!$affectRows) {
            throw new \Exception('答题机会已被使用');
        }
        $ticket->refresh();

        //更新上次答题时间，过期时间10天
        $redis = Yii::$app->redis_session;
        $redis->hset('vernal equinox', $user->id, time());
        $redis->expire('vernal equinox', 10 * 24 * 3600);
    }

    /**
     * 获得当前所有已经答题并发过奖的次数
     */
    public function getCurrentRewardedCount(User $user)
    {
        return (int) PromoLotteryTicket::findLotteryByPromoId($this->promo->id)
            ->andWhere(['user_id' => $user->id])
            ->andWhere(['isRewarded' => true])
            ->count();
    }

    /**
     * 发奖
     *
     * @param User $user 用户对象
     *
     * @return Reward $reward
     *
     * @throws \Exception
     */
    public function openAward(User $user)
    {
        $ticket = PromoLotteryTicket::findLotteryByPromoId($this->promo->id)
            ->andWhere(['user_id' => $user->id])
            ->andWhere(['isDrawn' => true])
            ->andWhere(['isRewarded' => false])
            ->one();
        if (null === $ticket) {
            throw new \Exception('没有宝箱可兑换', 4);
        }

        $sessionCount = $this->getSessionCount($user);
        $awardPool = $this->getRewardPoolByTimes($sessionCount);
        $awardSn = PromoService::openLottery($awardPool);
        $reward = Reward::fetchOneBySn($awardSn);
        if (null === $reward) {
            throw new \Exception('未找到奖品');
        }
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            PromoService::award($user, $reward, $this->promo, $ticket);
            $sql = "update promo_lottery_ticket set isRewarded = :isRewarded, rewardedAt = :rewardedAt, reward_id = :rewardId where id = :lotteryId and isRewarded = FALSE";
            $affectRows = $db->createCommand($sql, [
                'isRewarded' => true,
                'rewardedAt' => time(),
                'rewardId' => $reward->id,
                'lotteryId' => $ticket->id,
            ])->execute();
            if (!$affectRows) {
                throw new \Exception('答题机会已被使用');
            }

            $transaction->commit();
        } catch (\Exception $ex) {
            $transaction->rollBack();
            throw $ex;
        }

        return $reward;
    }

    /**
     * 当前批次答对题数
     *
     * @param User $user
     *
     * @return int
     */
    public function getSessionCount(User $user)
    {
        $rewardCount = $this->getCurrentRewardedCount($user);
        $currentCount = $rewardCount + 1;
        $sessionSn = 'batchNum' . $currentCount;

        return (int) Session::find()
            ->where(['batchSn' => $sessionSn])
            ->andWhere(['userId' => $user->id])
            ->count();
    }

    private function getRewardPoolByTimes($times)
    {
        if ($times <= 2) {
            $awardPool = [
                '180318_ZW' => '0.2',
                '180318_C1' => '0.25',
                '180318_C2' => '0.25',
                '180318_C3' => '0.2',
                '180318_C5' => '0.1',
            ];
        } elseif ($times <= 5) {
            $awardPool = [
                '180318_C2' => '0.3',
                '180318_C3' => '0.3',
                '180318_C5' => '0.2',
                '180318_C6' => '0.2',
            ];
        } else {
            $awardPool = [
                '180318_C3' => '0.2',
                '180318_C5' => '0.25',
                '180318_C6' => '0.3',
                '180318_C8' => '0.15',
                '180318_C20' => '0.1',
            ];
        }

        return $awardPool;
    }
}