<?php

namespace common\models\promo;

use common\models\coupon\CouponType;
use common\models\coupon\UserCoupon;
use common\models\user\User;
use common\service\SmsService;
use wap\modules\promotion\models\RankingPromo;

/**
 * 生日当天送代金券活动
 */
class BirthdayCoupon
{
    public $promo;

    const AWARD_70 = 1;//70元代金券
    const AWARD_30 = 2;//30元代金券
    const AWARD_20 = 3;//20元代金券

    public function getAwardList()
    {
        return [
            self::AWARD_70 => ['name' => '70元生日感恩券', 'sn' => '0020:50000-70'],
            self::AWARD_30 => ['name' => '30元生日感恩券', 'sn' => '0020:30000-30'],
            self::AWARD_20 => ['name' => '20元生日感恩券', 'sn' => '0020:20000-20'],
        ];
    }

    public function __construct(RankingPromo $promo)
    {
        $this->promo = $promo;
    }

    public function getAwardUserList()
    {
        $users = User::find()->where(['type' => 1])->andWhere(['like', 'idcard', date('md')])->all();
        $day = date('m-d');
        $data = [];
        foreach ($users as $user) {
            $birthday = $user->getBirthday();
            if (substr($birthday, 5) === $day) {
                $data[] = $user;
            }
        }
        return $data;
    }

    public function sendAwardToUsers(array  $users)
    {
        $promo = $this->promo;
        $time = time();
        $awardList = $this->getAwardList();
        $couponList = [];
        foreach ($awardList as $key => $award) {
            $coupon = CouponType::findOne(['sn' => $award['sn']]);
            if (empty($coupon)) {
                throw new \Exception('没有找到sn为 ' . $award['sn'] . ' 的代金券');
            }
            $couponList[$key] = $coupon;
        }
        foreach ($users as $user) {
            if (!$user instanceof User) {
                continue;
            }
            $translation = \Yii::$app->db->beginTransaction();
            $ticket = PromoLotteryTicket::find()->where([
                'user_id' => $user->id,
                'promo_id' => $promo->id,
                'source' => 'command',
                'date_format(from_unixtime(rewardedAt),"%Y")' => date('Y', $time),
            ])->one();
            if (!empty($ticket)) {
                continue;
            }
            try {
                if ($promo->isActive($user)) {
                    foreach ($couponList as $awardId => $coupon) {
                        //插入发奖记录
                        $ticket = new PromoLotteryTicket([
                            'user_id' => $user->id,
                            'isDrawn' => 1,
                            'isRewarded' => 1,
                            'reward_id' => $awardId,
                            'rewardedAt' => $time,
                            'drawAt' => $time,
                            'source' => 'command',
                            'promo_id' => $promo->id,
                        ]);
                        if (!$ticket->save(false)) {
                            $translation->rollBack();
                            throw new \Exception();
                        }
                        //给用户发代金券
                        try {
                            $userCoupon = UserCoupon::addUserCoupon($user, $coupon);
                            $res = $userCoupon->save(false);
                            if (!$res) {
                                throw new \Exception();
                            }
                        } catch (\Exception $ex) {
                            $translation->rollBack();
                            throw new \Exception($ex->getMessage());
                        }
                    }
                    //发短信
                    $message = [
                        $user->real_name,
                        180,
                        \Yii::$app->params['clientOption']['host']['frontend'],
                        \Yii::$app->params['contact_tel'],
                    ];
                    $templateId = \Yii::$app->params['sms']['birthday_coupon'];
                    $res = SmsService::send($user->mobile, $templateId, $message, $user);
                    if (!$res) {
                        $translation->rollBack();
                    }

                    $translation->commit();
                }
            } catch (\Exception $ex) {
                $translation->rollBack();
                throw new \Exception($ex->getMessage());
            }
        }
    }
}