<?php

namespace common\models\promo;

use common\models\offline\OfflineUser;
use common\models\user\User;
use common\models\user\UserInfo;
use wap\modules\promotion\models\RankingPromo;
use yii\web\Request;

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
     * 根据来源获取用户剩余的抽奖信息
     * @param User $user    用户
     * @param $source    来源
     * @return array|null|\yii\db\ActiveRecord
     */
    public function fetchOneActiveTicket(User $user, $source)
    {
        return PromoLotteryTicket::find()
            ->where([
                'promo_id' => $this->promo->id,
                'user_id' => $user->id,
                'isDrawn' => false,
                'source' => $source
            ])->andWhere(['>=', 'expiryTime', date('Y-m-d H:i:s')])
            ->one();
    }

    /**
     * 根据来源及当前日期获取用户的已抽奖信息
     * @param User $user 用户
     * @param null $source 来源
     * @param null $day 当前日期
     * @return array|null|\yii\db\ActiveRecord
     */
    public function fetchOneDrawnTicket(User $user, $source = null, $startTime = null, $endTime = null)
    {
        if ($startTime === null) {
            $startTime = strtotime('today');
        }
        if ($endTime === null) {
            $endTime = strtotime('tomorrow');
        }
        $query = PromoLotteryTicket::find()
            ->where([
                'promo_id' => $this->promo->id,
                'user_id' => $user->id,
                'isDrawn' => true,
            ]);
        if (null !== $source) {
            $query->andWhere(['source' => $source]);
        }
        if ($startTime !== null && $endTime !== null) {
            $query->andWhere(['between', 'created_at', $startTime, $endTime]);
        }

        return $query->andWhere(['>=', 'expiryTime', date('Y-m-d H:i:s')])
            ->one();
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
     * @param User $user  用户对象
     * @param bool $isDaily true:获取当天已抽奖次数， false:获取活动中所有的抽奖次数
     * @return mixed  int
     */
    public function getDrawnCount(User $user, $isDaily = false)
    {
        $query =  PromoLotteryTicket::find()
            ->where(['promo_id' => $this->promo->id])
            ->andWhere(['user_id' => $user->id])
            ->andWhere(['isDrawn' => true]);

        if ($isDaily) {
            $joinTime = new \DateTime();
            $query->andWhere(['date(from_unixtime(created_at))' => $joinTime->format('Y-m-d')]);
        }

        return (int)$query->count();
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
        $ticketCount = (int) Award::find()
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
     * @param bool $exceptXS 是否排除新手标
     * @param string $range all为全部，offline为线下，online为线上
     *
     * @return string
     */
    public function calcUserAmount($user, $isAnnual = true, $exceptXS = true, $range = 'all')
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
            if ($exceptXS) {
                $onlineInvestment = UserInfo::calcAnnualInvestNoXS($user->id, $startDate, $endDate);
            } else {
                $onlineInvestment = UserInfo::calcAnnualInvest($user->id, $startDate, $endDate);
            }
            $offlineInvestment = UserInfo::calcOfflineAnnualInvest($user->id, $startDate, $endDate);
            $totalInvestment = $offlineInvestment + $onlineInvestment;
        } else {
            if ($exceptXS) {
                $onlineInvestment = UserInfo::calcInvestNoXS($user->id, $startDate, $endDate);
            } else {
                $onlineInvestment = UserInfo::calcInvest($user->id, $startDate, $endDate);
            }
            $offlineInvestment = UserInfo::calcOfflineInvest($user->id, $startDate, $endDate);
            $totalInvestment = $offlineInvestment + $onlineInvestment;
        }

        return $totalInvestment;
    }

    /**
     * 某个奖品总库存剩余为0时概率的变化
     * @param $pool  原奖池
     * @param $sn  概率变化时,将概率加到此奖品上
     * @return 变化后的奖池
     */
    public function reviseStocksRate($pool, $psn = null)
    {
        $rewardSns = Reward::find()
            ->select('sn')
            ->where(['>', 'limit', 0])
            ->orWhere('`limit` is null')
            ->andWhere(['promo_id' => $this->promo->id])
            ->column();
        foreach ($pool as $sn => $rate) {
            if (!in_array($sn, $rewardSns)) {
                if (!is_null($psn)) {
                    $pool[$psn] = (string)($pool[$psn] + $rate);
                }
                $pool[$sn] = '0';
            }
        }
        return $pool;
    }
    /**
     * 给邀请用户赠送抽奖机会
     * @param User $newUser $newUser 新注册用户
     * @param Request|null $request
     * @param $source  抽奖机会来源
     * @param bool $isAddMore   是否给邀请用户多于1次的抽奖机会
     */
    protected function addInviteTicketInternal(User $newUser, Request $request = null, $source, \DateTime $expiryTime = null, $isAddMore = true)
    {
        //获取邀请当前用户的人
        $promoStartTime = strtotime($this->promo->startTime);
        $inviteRecord = InviteRecord::find()
            ->where(['invitee_id' => $newUser->id])
            ->andWhere(['>=', 'created_at', $promoStartTime])
            ->one();

        if (!empty($inviteRecord)) {
            $inviterId = $inviteRecord->user_id;
            $inviterUser = User::findOne(['id' => $inviterId]);
            //获取邀请者在活动期间邀请人数
            $inviteCount = (int)InviteRecord::find()->where(['user_id' => $inviterId])->andWhere(['>=', 'created_at', $promoStartTime])->count();
            //获取当前用户因为邀请被赠送的抽奖机会
            $ticketCount = (int)PromoLotteryTicket::find()->where(['user_id' => $inviterId, 'source' => $source, 'promo_id' => $this->promo->id])->count();
            //用户第一次邀请，给一次抽奖机会
            if ($inviteCount === 1 && $ticketCount === 0) {
                PromoLotteryTicket::initNew($inviterUser, $this->promo, $source, $expiryTime, $request)->save(false);
            } elseif ($inviteCount > 1 && $isAddMore) {
                $lastCount = $inviteCount - $ticketCount;//需要添加机会
                if ($lastCount > 0) {
                    for ($i = 1; $i <= $lastCount; $i++) {
                        PromoLotteryTicket::initNew($inviterUser, $this->promo, $source, $expiryTime, $request)->save(false);
                    }
                }
            }
        }
    }
}
