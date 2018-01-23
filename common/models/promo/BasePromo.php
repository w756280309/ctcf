<?php

namespace common\models\promo;

use common\models\offline\OfflineUser;
use common\models\user\User;
use common\models\user\UserInfo;
use wap\modules\promotion\models\RankingPromo;

class BasePromo
{
    public $promo;

    public function __construct(RankingPromo $promo)
    {
        $this->promo = $promo;
    }

    /**
     * 获取某个用户剩余抽奖机会
     *
     * @param User $user
     *
     * @return int
     */
    public function getActiveTicketCount(User $user)
    {
        return (int)PromoLotteryTicket::find()
            ->where([
                'promo_id' => $this->promo->id,
                'isDrawn' => false,
                'user_id' => $user->id,
            ])->andWhere(['>=', 'expiryTime', date('Y-m-d H:i:s')])
            ->count();
    }

    /**
     * 获得某个人已中奖列表 --- 仅适用通过抽奖机会的获奖列表(暂时保留)
     *
     * @param User $user 无需格外校验
     *
     * @return array
     */
    public function getDrawnList(User $user)
    {
        return PromoLotteryTicket::find()
            ->where(['promo_id' => $this->promo->id])
            ->andWhere(['user_id' => $user->id])
            ->andWhere(['isDrawn' => true])
            ->orderBy('drawAt desc')
            ->all();
    }

    /**
     * 获取用户在活动中已抽奖的次数
     *
     * @param User $user 用户对象
     *
     * @return int
     */
    public function getDrawnCount(User $user)
    {
        return (int) PromoLotteryTicket::find()
            ->where(['promo_id' => $this->promo->id])
            ->andWhere(['user_id' => $user->id])
            ->andWhere(['isDrawn' => true])
            ->count();
    }

    /**
     * 获取用户活动中所有的中奖记录
     *
     * @param User $user 用户对象
     *
     * @return array
     */
    public function getAwardList(User $user)
    {
        $r = Reward::tableName();
        $a = Award::tableName();

        return Award::find()
            ->select("$r.sn,$r.name,$r.ref_type,$r.ref_id,$r.path,$a.id as awardId, $a.amount as ref_amount,$a.createTime as awardTime")
            ->innerJoin($r, "$a.reward_id = $r.id")
            ->where(["$a.user_id" => $user->id])
            ->andWhere(["$a.promo_id" => $this->promo->id])
            ->orderBy(["$a.createTime" => SORT_DESC])
            ->asArray()
            ->all();
    }

    /**
     * 根据用户及相关配置给用户发放该活动对应的抽奖机会
     *
     * @param object $user 用户对象
     * @param string $amount 金额
     * @param null|string $everyAccumulatedAmount 每累计金额
     * @param null|integer $maxTicketCount 最大获得次数（默认为不限制）
     *
     * @throws \Exception
     */
    public function sendTicketsByConfig($user, $amount, $everyAccumulatedAmount = null, $maxTicketCount = null)
    {
        if (null === $user) {
            throw new \Exception('用户不存在');
        }
        $endTime = new \DateTime($this->promo->endTime);

        if (null === $everyAccumulatedAmount && null === $maxTicketCount) {
            throw new \Exception('累计金额和最大获得次数两者不能同时为null');
        }

        //如果不设置最大获得次数，就设置一个极大值
        if (null === $maxTicketCount) {
            $maxTicketCount = 999999;
        }
        if (null !== $everyAccumulatedAmount) {
            $allNum = intval($amount / $everyAccumulatedAmount);
            $allNum = $maxTicketCount <= $allNum ? $maxTicketCount : $allNum;
        } else {
            $allNum = $maxTicketCount;
        }

        $ticketCount = (int)PromoLotteryTicket::findLotteryByPromoId($this->promo->id)
            ->andWhere(['user_id' => $user->id])
            ->andWhere(['source' => 'order'])
            ->count();
        $extraNum = max($allNum - $ticketCount, 0);
        for ($i = 1; $i <= $extraNum; $i++) {
            PromoLotteryTicket::initNew($user, $this->promo, 'order', $endTime)->save();
        }
    }

    /**
     * 根据用户及相关配置给用户发放该活动对应的抽奖机会
     *
     * @param object $user 当前用户对象
     * @param string $rewardSn 奖品sn
     * @param string $everyAccumulatedAmount 每累计金额
     * @param null|integer $maxTicketCount 最大获得次数（默认为不限制）
     *
     * @throws \Exception
     */
    public function sendRewardByConfig($user, $rewardSn, $everyAccumulatedAmount, $maxTicketCount = null)
    {
        if (null === $user) {
            throw new \Exception('用户不存在');
        }
        $reward = Reward::fetchOneBySn($rewardSn);
        if (null === $reward) {
            throw new \Exception('奖品不存在');
        }
        //如果不设置最大获得次数，就设置一个极大值
        if (null === $maxTicketCount) {
            $maxTicketCount = 999999;
        }
        $amount = $this->calcUserAmount($user);
        $allNum = intval($amount / $everyAccumulatedAmount);
        $allNum = $maxTicketCount <= $allNum ? $maxTicketCount : $allNum;
        $ticketCount = (int)Award::find()
            ->where(['promo_id' => $this->promo->id])
            ->andWhere(['user_id' => $user->id])
            ->count();
        $extraNum = max($allNum - $ticketCount, 0);
        for ($i = 1; $i <= $extraNum; $i++) {
            PromoService::award($user, $reward, $this->promo);
        }
    }

    /**
     * 计算某用户在活动期间共获得的线上/线下的累计认购金额/累计年化认购金额
     *
     * @param object $user 用户对象
     * @param bool $isAnnual 是否为计算累计年化
     * @param string $range all为全部，offline为线下，online为线上
     *
     * @return string
     */
    public function calcUserAmount($user, $isAnnual = true, $range = 'all')
    {
        if ($user instanceof OfflineUser) {
            $user = $user->getOnlineUser();
            //如果为纯线下用户，未与线上做关联，在活动中不与考虑
            if (null === $user) {
                return false;
            }
        }
        $endTime = new \DateTime($this->promo->endTime);
        $startDate = (new \DateTime($this->promo->startTime))->format('Y-m-d');
        $endDate = $endTime->format('Y-m-d');
        if ($isAnnual) {
            $onlineInvestment = UserInfo::calcAnnualInvest($user->id, $startDate, $endDate);
            $offlineInvestment = UserInfo::calcOfflineAnnualInvest($user->id, $startDate, $endDate);
            $totalInvestment = $offlineInvestment + $onlineInvestment;
        } else {
            $onlineInvestment = UserInfo::calcInvest($user->id, $startDate, $endDate);
            $offlineInvestment = UserInfo::calcOfflineInvest($user->id, $startDate, $endDate);
            $totalInvestment = $offlineInvestment + $onlineInvestment;
        }

        return $totalInvestment;
    }
}
