<?php

namespace common\models\coupon;

use common\models\order\OnlineOrder;
use common\models\product\OnlineProduct as Loan;
use common\models\user\User;
use Exception;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "user_coupon".
 *
 * @property int $id
 * @property int $couponType_id
 * @property int $user_id
 * @property int $order_id
 * @property int $isUsed
 * @property int $created_at
 */
class UserCoupon extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_coupon';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['couponType_id', 'user_id', 'isUsed', 'created_at'], 'required'],
            [['couponType_id', 'user_id', 'order_id', 'isUsed', 'created_at'], 'integer'],
            [['order_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'updatedAtAttribute' => false,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'couponType_id' => '代金券类型',
            'user_id' => '用户ID',
            'order_id' => '订单ID',
            'isUsed' => '是否使用',
            'created_at' => '创建时间',
        ];
    }

    /**
     * 获取相关订单信息.
     */
    public function getOrder()
    {
        return $this->hasOne(OnlineOrder::className(), ['id' => 'order_id']);
    }

    public function getCouponType()
    {
        return $this->hasOne(CouponType::className(), ['id' => 'couponType_id']);
    }

    /**
     * 检查代金券是否可用.
     *
     * @param \common\models\coupon\UserCoupon $coupon
     * @param type                             $money
     * @param User                             $user
     * @param Loan                             $loan
     *
     * @return UserCoupon
     *
     * @throws Exception
     */
    public static function checkAllowUse(UserCoupon $coupon, $money, User $user = null, Loan $loan = null)
    {
        if ($coupon->isUsed) {
            throw new Exception('已经使用');
        }

        if (null !== $user && $coupon->user_id !== $user->id) {
            throw new Exception('代金券使用异常');
        }

        $time = time();
        if (
                strtotime($coupon->couponType->useStartDate) > $time
                || strtotime($coupon->couponType->useEndDate) < $time
                || strtotime($coupon->couponType->issueStartDate) > $time
                || strtotime($coupon->couponType->issueEndDate) < $time
         ) {
            throw new Exception('代金券不可以使用');
        }

        if (bccomp($coupon->couponType->minInvest, $money, 2) > 0) {
            throw new Exception('最低投资'.$coupon->couponType->minInvest.'元');
        }

        return $coupon;
    }

    public static function unuseCoupon(OnlineOrder $ord)
    {
        if ($ord->userCoupon_id) {
            $coupon = UserCoupon::findOne($ord->userCoupon_id);
            if (null === $coupon) {
                throw new \Exception('无法找到代金券');
            } elseif (!$coupon->isUsed || $coupon->order_id !== $ord->id) {
                throw new \Exception('代金券使用异常');
            }
            $coupon->order_id = 0;
            $coupon->isUsed = 0;
            return $coupon->save(false);
        }
        return true;
    }
}
