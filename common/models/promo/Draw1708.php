<?php

namespace common\models\promo;

use common\models\mall\PointRecord;
use common\models\order\OnlineOrder;
use common\models\user\CheckIn;
use common\models\user\User;
use common\models\user\UserInfo;

class Draw1708 extends BasePromo
{
    private $investRewardLimit = 10;
    private $annualInvestMoneyLimit = 50000;
    private $inviteLimit = 3;
    const SOURCE_INIT = 'init';

    public function doAfterSuccessLoanOrder(OnlineOrder $order)
    {
        $user = $order->user;
        if ($order->status !== OnlineOrder::STATUS_SUCCESS) {
            return false;
        }
        $this->promo->isActive($user, $order->order_time);
        $nowTime = new \DateTime();
        $nowDate = $nowTime->format('Y-m-d');

        //投资获得奖励部分
        $tickets = $this->hasInvestQuery($user, $nowTime)->count();
        $reward = Reward::findOne(['sn' => '1708_cash_10']);
        if (null === $reward) {
            return false;
        }
        //查询本天累计投资年化
        $annualInvest = UserInfo::calcAnnualInvest($user->id, $nowDate, $nowDate);
        $actualInvestLimit = intval($annualInvest / $this->annualInvestMoneyLimit);
        $allTicket = $actualInvestLimit >= $this->investRewardLimit ? $this->investRewardLimit : $actualInvestLimit;
        $extraTicket = max($allTicket - $tickets, 0);
        $flag = false;
        try {
            for ($i = 1; $i <= $extraTicket; $i++) {
                $flag = true;
                PromoService::award($user, $reward, $this->promo);
            }
            if ($flag) {
                $this->addPromoTicket($user);
            }
        } catch (\Exception $ex) {
        }

        //被邀请者活动期间首次投资获得奖励部分
        $reward = Reward::findOne(['sn' => '1708_points_500']);
        if (null === $reward) {
            return false;
        }

        //当前用户是否为被邀请者且为第一次投资
        $userInfo = $user->info;
        if (null !== $userInfo
            && $userInfo->isAffiliator
            && $nowDate === $userInfo->firstInvestDate
            && 1 === $userInfo->investCount
        ) {
            //获取邀请者
            $inviteRecord = InviteRecord::find()
                ->where(['invitee_id' => $user->id])
                ->one();
            $inviteUser = $inviteRecord->user;
            $inviteLimit = $this->hasRewardQuery($reward, $inviteUser, $nowTime)->count();
            if ($inviteLimit < $this->inviteLimit) {
                PromoService::award($inviteUser, $reward, $this->promo);
                $this->addPromoTicket($inviteUser);
            }
        }
    }

    public function doAfterCheckIn(CheckIn $checkIn)
    {
        $user = User::findOne(['id' => $checkIn->user_id]);
        if (null === $user) {
            return false;
        }
        $this->promo->isActive($user, strtotime($checkIn->createTime));
        $reward = Reward::findOne(['sn' => '1708_points_5']);
        if (null === $reward) {
            return false;
        }

        if (!$this->isCheckInDone($user, new \DateTime($checkIn->createTime))) {
            PromoService::award($user, $reward, $this->promo);
            $this->addPromoTicket($user);
        }
    }

    public function addPromoTicket($user)
    {
        $this->promo->isActive($user);
        $nowTime = new \DateTime();
        if ($this->hasGotTicket($user, $nowTime)) {
            return false;
        }
        if ($this->isCheckInDone($user, $nowTime)
            && $this->isInvestDone($user, $nowTime)
            && $this->isInviteDone($user, $nowTime)
        ) {
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                $token = new TicketToken();
                $token->key = $this->promo->id . '-' . $user->id . '-' . $nowTime->format('Ymd');
                $expireTime = new \DateTime($nowTime->format('Y-m-d') . ' 23:59:59');
                $token->save(false);
                PromoLotteryTicket::initNew($user, $this->promo, self::SOURCE_INIT, $expireTime)->save(false);
                $transaction->commit();
            } catch (\Exception $ex) {
                $transaction->rollBack();
            }
        }
    }

    public function getAwardPool($user, \DateTime $dateTime)
    {
        return [
            '1708_iphone' => '0.0001',
            '1708_yagao' => '0.1999',
            '1708_card_50' => '0.03',
            '1708_air' => '0.01',
            '1708_pillow' => '0.25',
            '1708_phonefare_50' => '0.01',
            '1708_cash_8.8' => '0.1',
            '1708_cash_6.6' => '0.2',
            '1708_points_160' => '0.1',
            '1708_points_120' => '0.1',
        ];
    }

    public function getInvestedInviteeCount($user, \DateTime $dateTime)
    {
        return $user->findByFirstInvestTimeAndInvited($dateTime, true)
            ->count();
    }

    public function getTasktSatus($user, \DateTime $dateTime)
    {
        return [
            'checkInFinished' => $this->isCheckInDone($user, $dateTime),
            'investFinished' => $this->isInvestDone($user, $dateTime),
            'inviteFinished' => $this->isInviteDone($user, $dateTime),
        ];
    }

    private function isCheckInDone($user, \DateTime $dateTime)
    {
        $reward = Reward::findOne(['sn' => '1708_points_5']);
        if (null === $reward) {
            return false;
        }

        return null !== $this->hasRewardQuery($reward, $user, $dateTime)->one();
    }

    private function isInvestDone($user, \DateTime $dateTime)
    {
        return null !== $this->hasInvestQuery($user, $dateTime)->one();
    }

    private function isInviteDone($user, \DateTime $dateTime)
    {
        //判断邀请任务是否已经完成
        $reward = Reward::findOne(['sn' => '1708_points_500']);
        if (null === $reward) {
            return false;
        }

        return null !== $this->hasRewardQuery($reward, $user, $dateTime)->one();
    }

    private function hasRewardQuery(Reward $reward, $user, \DateTime $dateTime)
    {
        return PointRecord::find()
            ->where(['ref_type' => PointRecord::TYPE_PROMO])
            ->andWhere(['ref_id' => $reward->id])
            ->andWhere(['user_id' => $user->id])
            ->andWhere(['date(recordTime)' => $dateTime->format('Y-m-d')]);
    }

    private function hasInvestQuery($user, \DateTime $dateTime)
    {
        return Award::find()
            ->where(['ref_type' => Award::TYPE_TRANSFER])
            ->andWhere(['date(createTime)' => $dateTime->format('Y-m-d')])
            ->andWhere(['ticket_id' => null])
            ->andWhere(['user_id' => $user->id])
            ->andWhere(['promo_id' => $this->promo->id]);
    }

    private function hasGotTicket($user, \DateTime $dateTime)
    {
        //判断当日是否已经给过抽奖机会了
        return null !== PromoLotteryTicket::findLotteryByPromoId($this->promo->id)
            ->andWhere(['user_id' => $user->id])
            ->andWhere(['date(from_unixtime(created_at))' => $dateTime->format('Y-m-d')])
            ->andWhere(['source' => self::SOURCE_INIT])
            ->one();
    }
}
