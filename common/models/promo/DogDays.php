<?php

namespace common\models\promo;

use common\models\order\OnlineOrder;
use common\models\user\User;
use common\models\user\UserInfo;
use Yii;

class DogDays extends BasePromo
{
    const SOURCE_FREE = 'free';
    const SOURCE_SHARE = 'share';
    const SOURCE_ORDER = 'order';
    private $annualInvestLimit = 100000;
    private $investLimit = 20000;

    public function addPromoTicket(User $user, $ticketSource)
    {
        //判断被邀请用户是否参加了活动（有截止时间判断）
        $this->promo->isActive($user);

        //如果不在当前类型范围内，直接返回
        if (!$this->isSourceInRange($ticketSource)) {
            throw new \Exception('类型不对');
        }

        //判断来源为召集的是否应该添加抽奖机会
        $nowTime = new \DateTime();
        $nowDate = $nowTime->format('Y-m-d');

        $transaction = Yii::$app->db->beginTransaction();
        try {
            $key = $user->id . '-' . $this->promo->id . '-' . $nowTime->format('Ymd') . '-' . $ticketSource;
            $ticketToken = new TicketToken();
            $ticketToken->key = $key;
            $ticketToken->save(false);
            $expiryTime = new \DateTime($nowDate . ' 23:59:59');
            PromoLotteryTicket::initNew($user, $this->promo, $ticketSource, $expiryTime)->save();
            $transaction->commit();
            return true;
        } catch (\Exception $ex) {
            $transaction->rollBack();
            throw $ex;
        }
    }

    /**
     * 订单完成之后统一调用逻辑
     */
    public function doAfterSuccessLoanOrder(OnlineOrder $order)
    {
        $promo = $this->promo;
        $user = $order->user;
        if ($order->status !== OnlineOrder::STATUS_SUCCESS
            || !$promo->isActive($user, $order->order_time)
        ) {
            return;
        }
        $nowTime = new \DateTime();
        $nowDate = $nowTime->format('Y-m-d');
        //查询本天累计投资年化
        $annualInvest = UserInfo::calcAnnualInvest($user->id, $nowDate, $nowDate);
        if ($annualInvest >= $this->annualInvestLimit) {
            $this->addPromoTicket($user, self::SOURCE_ORDER);
        }
    }

    public function getAwardPool($user, \DateTime $dateTime)
    {
        $nowDate = $dateTime->format('Y-m-d');
        if ($this->requireRaisePool($user, $dateTime)) {
            $pool = [
                '717_lc_50' => '0.2',
                '717_p_duofen' => '0.1',
                '717_p_xiyiye' => '0.15',
                '717_p_yusan' => '0.15',
                '717_p_oil' => '0.1',
                '717_p_card50' => '0.15',
                '717_p_air' => '0.05',
                '717_p_cheng' => '0.05',
                '717_p_card100' => '0.05',
           ];
        } else {
            $cDateTime = clone $dateTime;
            $startTime = $cDateTime->sub(new \DateInterval('P50D'));
            $totalInvest = UserInfo::calcInvest($user->id, date('Y-m-d H:i:s', $user->created_at), $dateTime->format('Y-m-d H:i:s'));
            if ($this->isRegisterInDuring($user, $startTime, $dateTime) && $totalInvest <= $this->investLimit) {
                $pool = [
                    '717_c_8' => '0.2',
                    '717_c_20' => '0.35',
                    '717_c_30' => '0.45',
                ];
                $sns = ['717_c_8', '717_c_20', '717_c_30'];
                $rewardSns = $this->getRewardSns($user, $nowDate);
                $arr = array_intersect($sns, $rewardSns);
                foreach ($arr as $sn) {
                    unset($pool[$sn]);
                }
            } else {
                $pool = [
                    '717_lc_10' => '0.6',
                    '717_lc_20' => '0.3',
                    '717_lc_50' => '0.1',
                ];
            }
        }

        return $pool;
    }

    public function requireRaisePool($user, \DateTime $nowTime)
    {
        $tickets = PromoLotteryTicket::findLotteryByPromoId($this->promo->id)
            ->andWhere(['user_id' => $user->id])
            ->andWhere(['date(from_unixtime(created_at))' => $nowTime->format('Y-m-d')])
            ->all();

        $orderTicketTime = $this->getOrderTicketTime($tickets);

        return null !== $orderTicketTime && $this->isFirstDraw($user, $orderTicketTime, $nowTime);
    }

    private function isSourceInRange($ticketSource)
    {
        return in_array($ticketSource, [
            self::SOURCE_FREE,
            self::SOURCE_SHARE,
            self::SOURCE_ORDER,
        ]);
    }

    /**
     * 判断一个用户是否为某个时间段内的注册用户
     */
    private function isRegisterInDuring($user, \DateTime $startTime, \DateTime $endTime)
    {
        $createTime = new \DateTime(date('Y-m-d H:i:s', $user->created_at));

        return $startTime <= $createTime && $createTime <= $endTime;
    }

    private function getRewardSns($user, $date) {
        $r = Reward::tableName();
        $p = PromoLotteryTicket::tableName();
        $sns = PromoLotteryTicket::find()
            ->innerJoinWith('reward')
            ->select("$r.sn")
            ->where(["$p.promo_id" => $this->promo->id])
            ->andWhere(["$p.user_id" => $user->id])
            ->andWhere(["$p.isDrawn" => true])
            ->andWhere(["date(from_unixtime($p.drawAt))" => $date])
            ->column();

        return $sns;
    }

    private function getOrderTicketTime($tickets)
    {
        $orderTicketTime = null;
        foreach ($tickets as $ticket) {
            if ($ticket->source === self::SOURCE_ORDER) {
                $orderTicketTime = new \DateTime(date('Y-m-d H:i:s', $ticket->created_at));
            }
        }

        return $orderTicketTime;
    }

    private function isFirstDraw($user, \DateTime $orderTicketTime, \DateTime $nowTime)
    {
        return null === PromoLotteryTicket::findLotteryByPromoId($this->promo->id)
            ->andWhere(['user_id' => $user->id])
            ->andWhere(['isDrawn' => true])
            ->andWhere(['>=', 'from_unixtime(drawAt)', $orderTicketTime->format('Y-m-d H:i:s')])
            ->andWhere(['<=', 'from_unixtime(drawAt)', $nowTime->format('Y-m-d H:i:s')])
            ->one();
    }
}
