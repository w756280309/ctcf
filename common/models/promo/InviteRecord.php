<?php

namespace common\models\promo;

use common\models\coupon\CouponType;
use common\models\coupon\UserCoupon;
use common\models\order\OnlineOrder;
use common\models\user\User;
use common\service\AccountService;
use common\service\SmsService;
use wap\modules\promotion\models\RankingPromo;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "invite_record".
 *
 * @property int $id
 * @property int $user_id   邀请者ID
 * @property int $invitee_id    被邀请者ID
 * @property int $created_at
 * @property int $updated_at
 */
class InviteRecord extends ActiveRecord
{
    const PROMO_KEY = 'WAP_INVITE_PROMO_160804';

    public static function tableName()
    {
        return 'invite_record';
    }

    public function rules()
    {
        return [
            [['user_id', 'invitee_id', 'created_at', 'updated_at'], 'integer'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'invitee_id' => 'Invitee ID',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * 获取用户的邀请好友记录.
     *
     * @param User $user
     * @return array ['name' => '用户真名', 'mobile' => '手机号', 'day' => '用户注册时间Y-m-d', 'coupon' => '代金券金额', 'cash' => '现金红包金额']
     */
    public static function getInviteRecord(User $user)
    {
        $promo = RankingPromo::find()->where(['key' => self::PROMO_KEY])->one();
        //获取邀请者首次投资记录
        $firstOrder = OnlineOrder::find()->where(['uid' => $user->id, 'status' => 1])->orderBy(['order_time' => SORT_ASC])->one();
        $invitee = self::find()->where(['user_id' => $user->id])->andWhere(['between', 'created_at', $promo->startAt, $promo->endAt])->select('invitee_id')->asArray()->all();
        $ids = ArrayHelper::getColumn($invitee, 'invitee_id');
        $res = [];
        if ($ids) {
            $users = User::find()->where(['id' => $ids])->orderBy(['id' => SORT_DESC])->all();
            foreach ($users as $k => $v) {
                //邀请者因为此被邀请者得到的代金券金额
                $order = OnlineOrder::find()
                    ->select('order_money')
                    ->where(['uid' => $v->id, 'status' => 1])
                    ->andWhere(['between', 'order_time', $promo->startAt, $promo->endAt])
                    ->orderBy(['order_time' => SORT_ASC])
                    ->asArray()
                    ->one();
                if ($order) {
                    $firstMoney = $order['order_money'];
                    if ($firstMoney < 10000) {
                        $coupon = 30;
                    } else {
                        $coupon = 50;
                    }
                } else {
                    $coupon = 0;
                }
                if ($firstOrder) {
                    //邀请者因为此被邀请者得到的现金红包
                    $thirdMoney = OnlineOrder::find()
                        ->where(['uid' => $v->id, 'status' => 1])
                        ->andWhere(['between', 'order_time', max($promo->startAt, $firstOrder->order_time), $promo->endAt])
                        ->orderBy(['order_time' => SORT_ASC])
                        ->limit(3)
                        ->all();
                    if ($thirdMoney) {
                        $money = ArrayHelper::getColumn($thirdMoney, 'order_money');
                        $cash = round(floatval(array_sum($money)) / 1000, 1);
                    } else {
                        $cash = 0;
                    }
                } else {
                    $cash = 0;
                }
                $res[$k] = ['name' => $v->real_name, 'mobile' => $v->mobile, 'day' => date('Y-m-d', $v->created_at), 'coupon' => $coupon, 'cash' => $cash];
            }
        }

        return $res;
    }


    /**
     * 获取被邀请者
     */
    public function getInvitee()
    {
        return $this->hasOne(User::className(), ['id' => 'invitee_id']);
    }
}
