<?php

namespace common\models\promo;

use common\models\order\OnlineOrder;
use common\models\user\User;
use common\models\user\UserInfo;
use wap\modules\promotion\models\RankingPromo;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use common\utils\SecurityUtils;

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
            'promo_170706',
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
                    //获取被邀请者首次正式标投资
                    $firstOrder = OnlineOrder::find()
                        ->leftJoin('online_product', 'online_order.online_pid = online_product.id')
                        ->where(['online_order.uid' => $invitee['id'], 'online_order.status' => 1])
                        ->andWhere(['online_product.is_xs' => 0])
                        ->orderBy(['online_order.id' => SORT_ASC])
                        ->one();
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
            $res[] = ['name' =>$invitee['real_name'], 'mobile' => SecurityUtils::decrypt($invitee['safeMobile']), 'day' => date('Y-m-d', $invitee['created_at']), 'coupon' => $coupon];
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
     * 获取邀请者
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
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

    /**
     * 获取某一时间段内注册并投资的好友人数
     *
     * @param User   $user      账户
     * @param string $startTime 开始时间
     * @param string $endTime   结束时间
     *
     * @return int
     */
    public static function getFriendsCountByUser(User $user, $startTime, $endTime)
    {
        $records = InviteRecord::find()
            ->where(['user_id' => $user->id])
            ->andWhere(['>=', 'created_at', strtotime($startTime)])
            ->andWhere(['<=', 'created_at', strtotime($endTime)])
            ->all();
        if (empty($records)) {
            return 0;
        }
        $inviteeIds = ArrayHelper::getColumn($records, 'invitee_id');

        return (int) UserInfo::find()
            ->where(['in', 'user_id', $inviteeIds])
            ->andWhere(['isInvested' => true])
            ->andWhere(['>=', 'firstInvestDate', $startTime])
            ->andWhere(['<=', 'firstInvestDate', $endTime])
            ->count();
    }
}
