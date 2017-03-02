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
            $dates[] = ['start' => strtotime($promo->startTime), 'end' => empty($promo->endTime) ? '' : strtotime($promo->endTime)];
        }
        $inviteRecords = InviteRecord::find()->select(['invitee_id', 'created_at'])->where(['user_id' => $user->id])->orderBy(['created_at' => SORT_DESC])->asArray()->all();
        foreach ($inviteRecords as $record) {
            $invitee = User::find()->where(['id' => $record['invitee_id']])->asArray()->one();
            if (empty($invitee)) {
                continue;
            }
            $coupon =0;
            foreach ($dates as $date) {
                if (
                    $record['created_at'] >= $date['start']
                    && (empty($date['end']) || $record['created_at'] <= $date['end'])
                ) {
                    //获取被邀请者首次投资
                    $firstOrder = OnlineOrder::find()->where(['uid' => $invitee['id'], 'status' => 1])->orderBy(['id' => SORT_ASC])->one();
                    $loan = is_null($firstOrder) ? null : $firstOrder->loan;
                    if (
                        !is_null($loan)
                        && $firstOrder->order_time >= $date['start']
                        && (empty($date['end']) || $firstOrder->order_time <= $date['end'])
                        && !$loan->is_xs
                    ) {
                        $firstMoney = $firstOrder->order_money;
                        if ($firstMoney < 10000) {
                            $coupon = 30;
                        } else {
                            $coupon = 50;
                        }
                    }
                    break;
                }
            }
            $res[] = ['name' =>$invitee['real_name'], 'mobile' => $invitee['mobile'], 'day' => date('Y-m-d', $invitee['created_at']), 'coupon' => $coupon];
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

    /**
     * 获取用户被邀请次数.
     *
     * @param string | int $userId 用户ID
     *
     * @return int
     */
    public static function inviteeCount($userId)
    {
        $count = self::find()->where([
            'invitee_id' => $userId,
        ])->count();

        return intval($count);
    }
}
