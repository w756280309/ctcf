<?php

namespace common\models\promo;

use common\models\order\OnlineOrder;
use common\models\user\User;
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
        $promoKey = [
            'WAP_INVITE_PROMO_160804',
            'promo_invite_12',
        ];
        $promos = RankingPromo::find()->where(['key' => $promoKey])->all();
        $res = [];
        $dates = [];
        foreach ($promos as $promo) {
            $dates[] = ['start' => $promo->startAt, 'end' => $promo->endAt];
        }
        $inviteRecords = InviteRecord::find()->select(['invitee_id', 'created_at'])->where(['user_id' => $user->id])->orderBy(['created_at' => SORT_DESC])->asArray()->all();
        foreach ($inviteRecords as $record) {
            $invitee = User::find()->where(['id' => $record['invitee_id']])->asArray()->one();
            if (empty($invitee)) {
                continue;
            }
            $coupon =0;
            $cash = 0.00;
            foreach ($dates as $date) {
                if ($record['created_at'] >= $date['start'] && $record['created_at'] <= $date['end']) {
                    $orderData = OnlineOrder::find()
                        ->select(['online_order.id', 'online_order.order_money'])
                        ->innerJoin('online_product', 'online_order.online_pid=online_product.id')
                        ->where(['online_order.uid' => $invitee['id'], 'online_order.status' => 1])
                        ->andWhere(['between', 'online_order.order_time', $date['start'], $date['end']])
                        ->andWhere(['online_product.is_xs' => 0])
                        ->orderBy(['online_order.order_time' => SORT_ASC])
                        ->limit(3)
                        ->asArray()
                        ->all();

                    //邀请者因为此被邀请者得到的代金券金额
                    if (count($orderData) > 0) {
                        $firstMoney = $orderData[0]['order_money'];
                        if ($firstMoney < 10000) {
                            $coupon = 30;
                        } else {
                            $coupon = 50;
                        }
                        //邀请者因为此被邀请者得到的现金红包
                        $money = ArrayHelper::getColumn($orderData, 'order_money');
                        $cash = bcdiv(array_sum($money), 1000, 2);
                    }
                    break;
                }
            }
            $res[] = ['name' =>$invitee['real_name'], 'mobile' => $invitee['mobile'], 'day' => date('Y-m-d', $record['created_at']), 'coupon' => $coupon, 'cash' => $cash];
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
