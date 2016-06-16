<?php

namespace wap\modules\promotion\models;

use common\models\coupon\CouponType;
use common\models\coupon\UserCoupon;
use common\models\user\User;
use Exception;

/**
 * 160520活动.
 *
 * @author zhanghongyu<zhanghongyu@wangcaigu.com>
 */
class Promo160520
{
    public static function couponConfig($prizeId)
    {
        $config = [
            1 => [
                '0001:1000-18',
                '0003:10000-30',
                '0006:50000-80',
                '0008:100000-120',
            ],
            2 => [
                '0002:1000-28',
                '0004:10000-40',
                '0006:50000-80',
                '0008:100000-120',
            ],
            3 => [
                '0002:1000-28',
                '0005:10000-50',
                '0007:50000-90',
                '0008:100000-120',
            ],
        ];

        return $config[$prizeId];
    }

    public static function draw($mobile)
    {
        if (!self::isValidMobile($mobile)) {
            throw new Exception('无效的手机号');
        }
        $user = User::findOne(['mobile' => $mobile]);
        $log = Promo160520Log::findOne(['mobile' => $mobile]);
        if (null !== $user) {
            if (!self::insertCoupon($user, 3)) {
                throw new Exception('获取优惠券失败');
            }
            $log = (new Promo160520Log(['mobile' => $mobile, 'prizeId' => 3, 'count' => 1, 'isNewUser' => 0]));
            $log->save();
        } else {
            if ($log) {
                $prizeId = (1 === $log->count) ? (1 === $log->prizeId ? 2 : 1) : 3;
                $log->prizeId = $prizeId;
                $log->count = (3 === $log->count) ? 1 : $log->count + 1;
            } else {
                $log = new Promo160520Log(['mobile' => $mobile, 'prizeId' => rand(1, 2), 'count' => 1, 'isNewUser' => 1]);
            }

            if (!$log->save()) {
                throw new Exception('获取优惠券失败');
            }
        }

        return $log;
    }

    public static function checkDraw($mobile)
    {
        if (!self::isValidMobile($mobile)) {
            throw new Exception('无效的手机号');
        }

        $user = User::findOne(['mobile' => $mobile]);
        if ($user) {
            if (Promo160520Log::find()->where(['mobile' => $mobile])->exists()) {
                throw new Exception('您已领过,请用本手机登录账户中心查看', 1);
            }
        } else {
            if (Promo160520Log::find()->where(['mobile' => $mobile, 'count' => 3])->exists()) {
                throw new Exception('您已领过,请用本手机登录账户中心查看', 1);
            }
        }
    }

    public static function insertCoupon(User $user, $prizeId)
    {
        $config = self::couponConfig($prizeId);

        if (empty($config)) {
            return false;
        }

        $coupons = CouponType::find()->where(['sn' => $config])->andFilterWhere(['>=' , 'issueEndDate' , date('Y-m-d')])->all();

        if (!$coupons) {
            return true;
        }

        $transaction = \Yii::$app->db->beginTransaction();
        foreach ($coupons as $coupon) {
            $time = time();

            $ret = (new UserCoupon([    //expiryDate记录了代金券的有效结束时间,如果有效天数不为空,则以领用时间为起点计算有效结束时间,否则直接读取代金券的有效结束时间
                'couponType_id' => $coupon->id,
                'user_id' => $user->id,
                'isUsed' => 0,
                'created_at' => $time,
                'expiryDate' => empty($coupon->expiresInDays) ? $coupon->useEndDate : date('Y-m-d', $time + 24 * 60 * 60 * ($coupon->expiresInDays - 1)),
            ]))->save(false);

            if (!$ret) {
                $transaction->rollBack();

                return false;
            }
        }
        $transaction->commit();

        return true;
    }

    public static function isValidMobile($mobile)
    {
        if (empty($mobile) || !preg_match('/^1[34578]\d{9}$/', $mobile)) {
            return false;
        }

        return true;
    }
}
