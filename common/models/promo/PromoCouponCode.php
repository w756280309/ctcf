<?php

namespace common\models\promo;

use common\models\couponcode\CouponCode;
use common\models\coupon\CouponType;
use common\models\coupon\UserCoupon;
use common\models\user\User;
use Yii;

class PromoCouponCode
{
    public static function duihuan($code, User $user)
    {
        if (empty($code)) {
            //返回兑换码输入不能为空
            return ['code'=>1, 'message'=>'请输入兑换码', 'data'=>''];
        }
        $user_id = intval($user->id);
        //判断活动期限内该用户兑换代金券的次数,coupontype与couponcode联查
        $nowdate = date('Y-m-d');
        $num  = CouponCode::find()->where(['isUsed'=>1, 'user_id'=>$user_id])->count();
        if ((int)$num >= 12) {
            //返回兑换码次数错误
            return ['code'=>1, 'message'=>'您已超过兑换上限，最多可兑换12次', 'data'=>''];
        }
        $model = CouponCode::findOne(['code'=>strtoupper($code)]);
        if (null === $model) {
            //返回兑换码有误
            return ['code'=>1, 'message'=>'兑换码有误，请重新输入', 'data'=>''];
        }
        if ($model->isUsed) {
            //返回已兑换
            if ($model->user_id !== $user_id) {
                return ['code'=>1, 'message'=>'该兑换码已被兑换', 'data'=>''];
            }
            return ['code'=>1, 'message'=>'您已兑换，请在我的代金券中查看', 'data'=>''];
        }
        if (strtotime($model->expiresAt) < time()) {
            //返回兑换码已过期
            return ['code'=>1, 'message'=>'兑换码已过期', 'data'=>''];
        }
        $model->user_id = $user_id;
        $model->isUsed = 1;
        $model->usedAt = date('Y-m-d H:i:s');
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($model->save()) {
                //发放1张代金券
                $coupon_type = CouponType::findOne(['sn'=>$model->couponType_sn]);
                if (UserCoupon::addUserCoupon($user, $coupon_type)->save()) {
                     $transaction->commit();
                     return ['code'=>0, 'message'=>'兑换成功', 'data'=>intval($coupon_type->amount).'元代金券'];
                }
            }
        } catch (\Exception $ex) {
            $code = $ex->getCode();
            $transaction->rollBack();
        }
        if ($code === 1 || $code === 2) {
            return ['code'=>2, 'message'=>'兑换码未生效', 'data'=>''];
        }
        return ['code'=>2, 'message'=>'兑换失败，请重试', 'data'=>''];
    }
}
