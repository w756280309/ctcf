<?php

namespace common\models\promo;

use common\models\coupon\CouponType;
use common\models\coupon\UserCoupon;
use common\models\order\OnlineOrder;
use common\models\user\User;
use common\models\user\UserInfo;
use wap\modules\promotion\models\RankingPromo;
use yii\db\Exception;

class Promo171111 extends BasePromo
{
    const SOURCE_ORDER = 'order';
    const SOURCE_INVITE  = 'invite';
    const COUPON_10_10 = 1;  //加息10天，利率1%
    const COUPON_10_15 = 2;  //加息10天，利率1.5%
    const COUPON_10_20 = 3;  //加息10天，利率2%
    const COUPON_10_25 = 4;  //加息10天，利率2.5%
    const COUPON_10_30 = 5;  //加息10天，利率3%
    const COUPON_07_05 = 6;  //加息7天，利率0.5%
    const COUPON_07_06 = 7;  //加息7天，利率0.6%
    const COUPON_07_08 = 8;  //加息7天，利率0.8%
    const COUPON_07_10 = 9;  //加息7天，利率1%
    const COUPON_07_12 = 10;  //加息7天，利率1.2%
    private $ticketEndTime = '2017-11-08 23:59:59';
    private $inviteEndTime = '2017-11-03 23:59:59';
    private $ticketEffectEndTime = '2017-11-11 23:59:59';

    private function getDrawPromo()
    {
        return 'promo_171111' === $this->promo->key
            ? $this->promo
            : RankingPromo::findOne(['key' => 'promo_171111']);
    }
    //获取加息券种类列表
    public function getCouponList()
    {
        return [
            self::COUPON_10_10 => ['name' => '最低投资1万，加息10天，利率1%', 'sn' => 'coupon_10_10'],
            self::COUPON_10_15 => ['name' => '最低投资5万，加息10天，利率1.5%', 'sn' => 'coupon_10_15'],
            self::COUPON_10_20 => ['name' => '最低投资20万，加息10天，利率2%', 'sn' => 'coupon_10_20'],
            self::COUPON_10_25 => ['name' => '最低投资50万，加息10天，利率2.5%', 'sn' => 'coupon_10_25'],
            self::COUPON_10_30 => ['name' => '最低投资100万，加息10天，利率3%', 'sn' => 'coupon_10_30'],
            self::COUPON_07_05 => ['name' => '最低投资1万，加息7天，利率0.5%', 'sn' => 'coupon_07_05'],
            self::COUPON_07_06 => ['name' => '最低投资5万，加息7天，利率0.6%', 'sn' => 'coupon_07_06'],
            self::COUPON_07_08 => ['name' => '最低投资20万，加息7天，利率0.8%', 'sn' => 'coupon_07_08'],
            self::COUPON_07_10 => ['name' => '最低投资50万，加息7天，利率1%', 'sn' => 'coupon_07_10'],
            self::COUPON_07_12 => ['name' => '最低投资100万，加息7天，利率1.2%', 'sn' => 'coupon_07_12'],
        ];
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
    //获取2017年11月9日零点之前最后一次预约加息券的人数
    public function getAwardUserList()
    {
        $userList = \Yii::$app->db->createCommand(
                "select a.* from appliament a INNER JOIN (SELECT userID, MAX(appointmentTime) as 'maxTime' from appliament GROUP BY userID) b 
                  on a.appointmentTime=b.maxTime and a.userID=b.userID")
                ->queryAll();

        return $userList;
    }
    //给预约的用户发放相应的加息券
    public function sendAwardToUsers(array $users)
    {
        $awardList = $this->getCouponList();
        $couponList = [];
        $successCoupon = 0;
        //判断CouponType表中是否有相应的配置内容
        foreach ($awardList as $key => $award) {
            $coupon = CouponType::findOne(['sn' => $award['sn']]);
            if (empty($coupon)) {
                throw new \Exception('没有找到sn为 ' . $award['sn'] . ' 的加息券');
            }
            $couponList[$key] = $coupon;
        }
        //遍历用户的最后一次的信息，将相应的userID和couponType_id插入到user_coupon中，如果已发过，则跳过
        foreach ($users as $user) {
            $realUser = User::findOne(['id' => $user['userID']]);
            $couponType = $this->getCouponInfo($user['appointmentAward'], $user['appointmentObjectId'], $couponList);
            $userCoupon = UserCoupon::find()->where([
                'couponType_id' => $couponType['id'],
                'user_id' => $realUser->id,
            ])->one();
            if (!empty($userCoupon)) {
                \Yii::info("[command][promo/send-double-eleven-coupon] 双十一活动二用户({$realUser->id})已经发过加息券, 跳过", 'command');
                continue;
            }
            try {
                $userCoupon = UserCoupon::addUserCoupon($realUser, $couponType);
                $res = $userCoupon->save(false);
                if (!$res) {
                    throw new \Exception('代金券发送失败');
                }
                $successCoupon++;
            } catch (\Exception $ex) {
                throw new Exception($ex->getMessage());
            }
        }
        \Yii::info("[command][promo/send-double-eleven-coupon] 双十一活动二共成功发放{$successCoupon}个代金券", 'command');
    }

    //根据预约金额和预约类型获取加息券信息（利率,最低投资金额,天数,预约类型名称）
    private function getCouponInfo($appointmentAward, $appointmentObjectId, $couponList)
    {
        $lowestInvestMoney = 1;
        $couponRate = 1;
        $couponLength = 10;
        if ($appointmentObjectId == 1) {
            $couponLength = 7;
        }
        if($appointmentAward >= 1 && $appointmentAward < 5 && $appointmentObjectId == 0){
            $couponRate = 1;
            $lowestInvestMoney = 1;
        } else if ($appointmentAward >= 5 && $appointmentAward < 20 && $appointmentObjectId == 0) {
            $couponRate = 1.5;
            $lowestInvestMoney = 5;
        } else if ($appointmentAward >= 20 && $appointmentAward < 50 && $appointmentObjectId == 0) {
            $couponRate = 2;
            $lowestInvestMoney = 20;
        } else if ($appointmentAward >= 50 && $appointmentAward < 100 && $appointmentObjectId == 0) {
            $couponRate = 2.5;
            $lowestInvestMoney = 50;
        } else if ($appointmentAward >= 100 && $appointmentObjectId == 0) {
            $couponRate = 3;
            $lowestInvestMoney =100;
        } else if ($appointmentAward >= 1 && $appointmentAward < 5 && $appointmentObjectId == 1) {
            $couponRate = 0.5;
            $lowestInvestMoney = 1;
        } else if ($appointmentAward >= 5 && $appointmentAward < 20 && $appointmentObjectId == 1) {
            $couponRate = 0.6;
            $lowestInvestMoney = 5;
        } else if ($appointmentAward >= 20 && $appointmentAward < 50 && $appointmentObjectId == 1) {
            $couponRate = 0.8;
            $lowestInvestMoney = 20;
        } else if ($appointmentAward >= 50 && $appointmentAward < 100 && $appointmentObjectId == 1) {
            $couponRate = 1.0;
            $lowestInvestMoney = 50;
        } else if ($appointmentAward >= 100 && $appointmentObjectId == 1) {
            $couponRate = 1.2;
            $lowestInvestMoney = 100;
        }
        $lowestInvestMoney*=10000;
        foreach ($couponList as $coupon) {
            if ($couponLength == $coupon['bonusDays'] && $couponRate == $coupon['bonusRate'] && $lowestInvestMoney == $coupon['minInvest']) {
                return $coupon;
            }
        }
    }
}
