<?php

namespace common\models\promo;

use common\models\code\Code;
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
            return ['code' => 1, 'message' => '请输入兑换码', 'data' => ''];
        }
        $user_id = intval($user->id);
        $model = Code::findOne(['code' => strtoupper($code), 'goodsType' => Code::TYPE_COUPON]);
        if (null === $model) {
            //返回兑换码有误
            return ['code' => 1, 'message' => '兑换码有误，请重新输入', 'data' => ''];
        }
        if ($model->isUsed) {
            //返回已兑换
            if ($model->user_id !== $user_id) {
                return ['code' => 1, 'message' => '该兑换码已被兑换', 'data' => ''];
            }
            return ['code' => 1, 'message' => '您已兑换，请在我的代金券中查看', 'data' => ''];
        }
        if (strtotime($model->expiresAt) < time()) {
            //返回兑换码已过期
            return ['code' => 1, 'message' => '兑换码已过期', 'data' => ''];
        }
        $model->user_id = $user_id;
        $model->isUsed = 1;
        $model->usedAt = date('Y-m-d H:i:s');
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($model->save()) {
                //发放1张代金券
                $coupon_type = CouponType::findOne(['id' => $model->goodsType_sn]);
                if (UserCoupon::addUserCoupon($user, $coupon_type)->save()) {
                    $transaction->commit();
                    return ['code' => 0, 'message' => '兑换成功', 'data' => intval($coupon_type->amount) . '元代金券'];
                }
            }
        } catch (\Exception $ex) {
            $code = $ex->getCode();
            $transaction->rollBack();
        }
        if ($code === 1 || $code === 2) {
            return ['code' => 2, 'message' => '兑换码未生效', 'data' => ''];
        }
        return ['code' => 2, 'message' => '兑换失败，请重试', 'data' => ''];
    }
}
