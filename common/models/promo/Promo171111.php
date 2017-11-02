<?php

namespace common\models\promo;

use common\models\order\OnlineOrder;
use common\models\user\User;
use common\models\user\UserInfo;
use wap\modules\promotion\models\RankingPromo;

class Promo171111 extends BasePromo
{
    const SOURCE_ORDER = 'order';
    const SOURCE_INVITE  = 'invite';
    private $ticketEndTime = '2017-11-08 23:59:59';
    private $inviteEndTime = '2017-11-03 23:59:59';
    private $ticketEffectEndTime = '2017-11-11 23:59:59';

    private function getDrawPromo()
    {
        return 'promo_171111' === $this->promo->key
            ? $this->promo
            : RankingPromo::findOne(['key' => 'promo_171111']);
    }

    public function doAfterSuccessLoanOrder(OnlineOrder $order)
    {
        $user = $order->user;

        //判断当前是否已经过了获得ticket的截止时间
        $orderTime = new \DateTime(date('Y-m-d H:i:s', $order->order_time));
        $ticketEndTime = new \DateTime($this->ticketEndTime);
        if ($orderTime > $ticketEndTime) {
            return;
        }

        //投资成功 - 添加投资抽奖机会
        $this->addUserTicket($user, self::SOURCE_ORDER);

        //作为被邀请者 - 在活动时间内注册并投资给邀请者添加抽奖机会
        $this->addInviteTicket($user, self::SOURCE_INVITE, $orderTime);
    }

    private function addInviteTicket(User $user, $ticketSource, $joinTime)
    {
        //判断是否大于邀请截止时间
        $inviteEndTime = new \DateTime($this->inviteEndTime);
        if ($joinTime > $inviteEndTime) {
            return;
        }

        //判断是否在活动期间被邀请
        if (!$user->isInvited($this->promo->startTime, $this->promo->endTime)) {
            return;
        }

        //获取邀请者
        $inviter = $user->fetchInviter();
        if (null === $inviter) {
            return;
        }

        $this->addUserTicket($inviter, $ticketSource);
    }

    public function addUserTicket($user, $source)
    {
        $expireTime = new \DateTime($this->ticketEffectEndTime);
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $key = $this->promo->id . '-' . $user->id . '-' . $source;
            TicketToken::initNew($key)->save(false);
            PromoLotteryTicket::initNew($user, $this->getDrawPromo(), $source, $expireTime)->save(false);
            $transaction->commit();
        } catch (\Exception $ex) {
            $transaction->rollBack();
            if (23000 !== (int)$ex->getCode()) {
                throw $ex;
            }
        }
    }

    public function getPromoTaskStatus(User $user)
    {
        $data = [
            'inviteTask' => 0,
            'investTask' => 0,
        ];

        //查询是否有对应的token,来判断任务是否完成
        $keyPrefix = $this->promo->id . '-' . $user->id . '-';
        $keyOrder = $keyPrefix.self::SOURCE_ORDER;
        $keyInvite = $keyPrefix.self::SOURCE_INVITE;
        $ticketTokens = TicketToken::find()
            ->where(['in', 'key', [$keyOrder, $keyInvite]])
            ->all();
        foreach ($ticketTokens as $token) {
            if ($token->key === $keyOrder) {
                $data['investTask'] = 1;
            } elseif ($token->key === $keyInvite) {
                $data['inviteTask'] = 1;
            }
        }

        return $data;
    }

    public function getAwardPool($user, \DateTime $dateTime)
    {
        $startTime = new \DateTime('2017-11-01 00:00:00');
        $annualInvest = UserInfo::calcAnnualInvest($user->id, $startTime->format('Y-m-d'), $dateTime->format('Y-m-d'));
        if ($annualInvest <= 50000) {
            $pool = [
                '171111_0.52' => '0.35',
                '171111_0.6' => '0.35',
                '171111_0.8' => '0.2',
                '171111_1.1' => '0.1',
            ];
        } elseif ($annualInvest > 50000 && $annualInvest <= 200000) {
            $pool = [
                '171111_1.1' => '0.45',
                '171111_1.6' => '0.3',
                '171111_1.8' => '0.2',
                '171111_5.2' => '0.03',
                '171111_11.11' => '0.02',
            ];
        } else {
            $pool = [
                '171111_1.1' => '0.35',
                '171111_1.6' => '0.15',
                '171111_1.8' => '0.15',
                '171111_5.2' => '0.25',
                '171111_11.11' => '0.0999',
                '171111_1111' => '0.0001',
            ];
        }

        return $pool;
    }
}
