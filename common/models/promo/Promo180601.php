<?php
namespace common\models\promo;

use common\models\user\User;
use yii\helpers\ArrayHelper;
use Yii;
use common\models\adv\ShareLog;

class Promo180601 extends BasePromo
{
    /**
     * 获取答题活动的问题,答案及选项
     * @param User $user  当前用户
     * @param $sn          题组编号
     * @return array  该题组下的所有问题
     */
    public function getQuestions(User $user, $sn)
    {
        $currentCount = $this->getCurrentTicketCount($user);
        $batchSn = $sn . $currentCount % 5;
        $data = [];
        $questions = Question::find()
            ->select("id, title, batchSn as sn")
            ->where(['batchSn' => $batchSn])
            ->andWhere(['promoId' => $this->promo->id])
            ->asArray()
            ->all();
        if (!empty($questions)) {
            $questionsIds = ArrayHelper::getColumn($questions, 'id');

            $options = Option::find()
                ->select('id, content, questionId')
                ->where(['in', 'questionId', $questionsIds])
                ->asArray()
                ->all();

            $options = ArrayHelper::map($options, 'id', 'content', 'questionId');

            foreach ($questions as $k => $question) {
                if (isset($options[$question['id']])) {
                    $data[$k] = $question;
                    $data[$k]['options'] = $options[$question['id']];
                }
            }

            //两次混合答题信息数组
            shuffle($data);

            return $data;
        }
    }

    /**
     * 赠送免费及分享的答题机会，并检查剩余的答题机会
     * @param $user  用户
     * @param $joinTime
     * @throws \Exception
     */
    public function checkRestTicket($user, $joinTime)
    {
        $this->addPromoTicket($user, 'free');
        $this->addPromoTicket($user, 'share');
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
    }

    /**
     * 根据用户的答题结果发送相应奖励
     * @param $sn  选择的寓言故事标识
     * @param $results  答题结果
     * @param $user 用户
     * @param null $joinTime  参加提答题的时间
     * @return array  是否通关信息及获奖代金券金额
     * @throws \Exception
     */
    public function getAwardRecord($sn, $results, $user, $joinTime = null)
    {
        if ($joinTime === null) {
            $joinTime = new \DateTime();
        }
        $currentCount = $this->getCurrentTicketCount($user);
        $batchSn = $sn . $currentCount % 5;
        $qa = Question::find()
            ->select('id,answer')
            ->where(['batchSn' => $batchSn])
            ->andWhere(['promoId' => $this->promo->id])
            ->indexBy('id')
            ->asArray()
            ->all();
        if (empty($qa)) {
            throw new \Exception('无答题信息', 88);
        }
        $correctNum = 0;
        if (null === $results) {
            throw new \Exception('答题信息格式错误', 89);
        }
        foreach ($results as $qid => $answer) {
            if (isset($qa[$qid]) && $answer === $qa[$qid]['answer']) {
                $correctNum++;
            }
        }
        if ($user === null) {
            throw new \Exception('用户不存在', 90);
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
        $result = [];
        if ($correctNum >= 4) {
            $rewardC20 = Reward::fetchOneBySn('180601_C20');
            PromoService::award($user, $rewardC20, $this->promo);
            $rewardP20 = Reward::fetchOneBySn('180601_P20');
            PromoService::award($user, $rewardP20, $this->promo);
            $this->updatePromoLotteryTicket($ticket);
            $redis = Yii::$app->redis;
            $redis->hset('talesStatus', $sn . $user->id, 'success');
            $redis->expire('talesStatus', 8 * 24 *3600);
            $result['isPassed'] = true;
            $result['amount'] = ['ref_amount' =>$rewardC20->ref_amount];
        } else {
            $pool = $this->getAwardPool();
            $awardSn = PromoService::openLottery($pool);
            $reward = Reward::fetchOneBySn($awardSn);
            PromoService::award($user, $reward, $this->promo);
            $this->updatePromoLotteryTicket($ticket);
            $result['isPassed'] = false;
            $result['amount'] = ['ref_amount' => $reward->ref_amount];
        }

        return $result;
    }

    public function subtractOneTicket($user, $joinTime = null)
    {
        if ($joinTime === null) {
            $joinTime = new \DateTime();
        }
        $ticket = PromoLotteryTicket::find()
            ->where(['promo_id' => $this->promo->id])
            ->andWhere(['user_id' => $user->id])
            ->andWhere(['isDrawn' => false])
            ->andWhere(['date(from_unixtime(created_at))' => $joinTime->format('Y-m-d')])
            ->one();

        if (is_null($ticket)) {
            throw new \Exception('无抽奖机会');
        }
        $this->updatePromoLotteryTicket($ticket);

        return true;
    }

    //更新用户的答题机会
    private function updatePromoLotteryTicket(PromoLotteryTicket $ticket)
    {
        $db = Yii::$app->db;
        $sql = "update promo_lottery_ticket set isDrawn = :isDrawn, drawAt = :drawAt, isRewarded = :isRewarded where id = :lotteryId and isDrawn = FALSE";
        $affectRows = $db->createCommand($sql, [
            'isDrawn' => true,
            'isRewarded' => true,
            'drawAt' => time(),
            'lotteryId' => $ticket->id,
        ])->execute();
        if (!$affectRows) {
            throw new \Exception('答题机会已被使用', 91);
        }
        $ticket->refresh();

        return true;
    }

    //用户当前完成答题的次数
    private function getCurrentTicketCount(User $user)
    {
        return PromoLotteryTicket::findLotteryByPromoId($this->promo->id)
            ->andWhere(['user_id' => $user->id])
            ->andWhere(['isRewarded' => true])
            ->count();
    }

    //未通关情况下的获奖概率
    private function getAwardPool()
    {
        return [
            '180601_C10' => '0.15',         //10元代金券
            '180601_C8' => '0.2',           //8元代金券
            '180601_C5' => '0.2',           //5元代金券
            '180601_C3' => '0.15',          //3元代金券
            '180601_wzj' => '0.3',          //未获奖
        ];
    }

    //活动期间给用户增加免费的抽奖机会以及分享到朋友圈的抽奖机会
    public function addPromoTicket($user, $type, \DateTime $joinTime = null)
    {
        if ($joinTime === null) {
            $joinTime = new \DateTime();
        }
        $promoStartTime = new \DateTime($this->promo->startTime);
        $promoEndTime = new \DateTime($this->promo->endTime);
        if ($joinTime >= $promoStartTime && $joinTime <= $promoEndTime) {
            if ($user) {
                if (in_array($type, ['free', 'share'])) {
                    try {
                        $key = $this->promo->id . '-' . $user->id . '-' . $joinTime->format('Ymd') . '-' . 'free';
                        if ($type === 'share') {
                            $shareLog = ShareLog::fetchByConfig($user, 'timeline', 'p180601');
                            if (null !== $shareLog) {
                                $key = $this->promo->id . '-' . $user->id . '-' . $joinTime->format('Ymd') . '-' . 'share';
                            }
                        }
                        TicketToken::initNew($key)->save(false);
                        PromoLotteryTicket::initNew($user, $this->promo, 'free')->save(false);
                    } catch (\Exception $ex) {
                        $code = $ex->getCode();
                        if (23000 !== (int)$code) {
                            throw  $ex;
                        }
                    }
                }
            }
        }
    }
}
