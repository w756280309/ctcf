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
            throw new Exception('代金券已经使用');
        }

        if (null !== $user && $coupon->user_id !== intval($user->id)) {
            throw new Exception('代金券使用异常');
        }

        $time = time();

        if (strtotime($coupon->expiryDate.' 23:59:59') < $time) {
            throw new Exception('代金券不可以使用');
        }

        if (bccomp($coupon->couponType->minInvest, $money, 2) > 0) {
            throw new Exception('代金券最低投资'.rtrim(rtrim($coupon->couponType->minInvest, '0'), '.').'元可用');
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
            $coupon->order_id = null;
            $coupon->isUsed = 0;
            return $coupon->save(false);
        }
        return true;
    }

    /**
     * 给指定用户添加代金券
     * @param User $user
     * @param CouponType $couponType
     * @return UserCoupon
     * @throws Exception
     */
    public static function addUserCoupon(User $user, CouponType $couponType)
    {
        if (!$couponType->allowIssue()) {
            throw new \Exception('代金券不满足发放条件', 1);
        }

        if (!$couponType->expiresInDays && !$couponType->useEndDate) {
            throw new \Exception('代金券截止日期异常', 2);
        }

        $time = time();
        $expiryDate = empty($couponType->expiresInDays) ? $couponType->useEndDate : date('Y-m-d', $time + 24 * 60 * 60 * ($couponType->expiresInDays - 1));
        $model = new self([    //expiryDate记录了代金券的有效结束时间,如果有效天数不为空,则以领用时间为起点计算有效结束时间,否则直接读取代金券的有效结束时间
            'couponType_id' => $couponType->id,
            'user_id' => $user->id,
            'isUsed' => 0,
            'created_at' => $time,
            'expiryDate' => $expiryDate,
        ]);
        return $model;
    }
}
