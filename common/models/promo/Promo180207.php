<?php

namespace common\models\promo;

use common\models\adv\Session;
use common\models\adv\ShareLog;
use common\models\user\User;
use Yii;

class Promo180207 extends BasePromo
{
    /**
     * 答题提交
     *
     * @param User $user 用户对象
     * @param int $correctNum 答题次数
     * @param string $sn 批次号
     *
     * @return Reward
     *
     * @throws \Exception
     */
    public function reply(User $user, $correctNum, $sn)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $joinTime = new \DateTime();
            $ticketCount = $this->getTodayActiveTicketCount($user, $joinTime);
            if (0 === $ticketCount) {
                throw new \Exception('无游戏次数', 4);
            }

            //根据答对的条数来判断奖池概率
            if (0 === $correctNum) {
                $pool = ['180207_ZW' => '1'];
            } else if ($correctNum < 3) {
                $pool = [
                    '180207_ZW' => '0.5',
                    '180207_C3' => '0.2',
                    '180207_C5' => '0.15',
                    '180207_C8' => '0.1',
                    '180207_C10' => '0.05',
                ];
            } else {
                $pool = [
                    '180207_C20' => '0.05',
                    '180207_C3' => '0.3',
                    '180207_C5' => '0.35',
                    '180207_C8' => '0.25',
                    '180207_C10' => '0.05',
                ];
            }

            //防止重复发奖
            TicketToken::initNew($this->promo->id.'-'.$user->id.'-'.$sn)->save(false);
            $awardSn = PromoService::openLottery($pool);
            $reward = Reward::fetchOneBySn($awardSn);
            $awardBool = PromoService::award($user, $reward, $this->promo);
            if (!$awardBool) {
                throw new \Exception('发奖失败');
            }

            //插入答题记录
            Session::initNew($user, $sn)->save(false);
            $transaction->commit();

            return $reward;
        } catch (\Exception $ex) {
            $transaction->rollBack();
            throw $ex;
        }
    }

    /**
     * 获得今日剩余的游戏次数
     *
     * @param User $user 用户对象
     * @param \DateTime $joinTime 参与时间
     *
     * @return int
     */
    public function getTodayActiveTicketCount(User $user, \DateTime $joinTime)
    {
        //当天初始次数为1
        $activeTicketCount = 1;

        //若当天有分享记录，则初始次数为2
        $shareLog = ShareLog::fetchByConfig($user, 'timeline', 'p180207', $joinTime);
        if (null !== $shareLog) {
            $activeTicketCount = 2;
        }

        //本天已抽取次数
        $sessionCount = (int) Session::findByCreateTime($user, $joinTime)->count();
        $realCount = $activeTicketCount - $sessionCount;

        return $realCount;
    }
}