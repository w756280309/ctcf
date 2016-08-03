<?php

namespace common\models\promo;

use common\models\coupon\CouponType;
use common\models\coupon\UserCoupon;
use common\models\order\OnlineOrder;
use common\models\sms\SmsMessage;
use common\models\user\User;
use common\service\AccountService;
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
        //判断用户是否投资过
        $count = OnlineOrder::find()->where(['uid' => $user->id, 'status' => 1])->count();
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
                if ($count > 0) {
                    //邀请者因为此被邀请者得到的现金红包
                    $thirdMoney = OnlineOrder::find()
                        ->where(['uid' => $v->id, 'status' => 1])
                        ->andWhere(['between', 'order_time', $promo->startAt, $promo->endAt])
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

    //投资成功之后处理逻辑
    public static function dealWithOrder(OnlineOrder $order)
    {
        $promo = RankingPromo::find()->where(['key' => self::PROMO_KEY])->one();
        $time = time();
        if (intval($order->status) === 1 && $time >= $promo->startAt && $time <= $promo->endAt) {
            //判断是不是被邀请者
            $invite = self::find()
                ->where(['invitee_id' => $order->uid])
                ->andWhere(['between', 'created_at', $promo->startAt, $promo->endAt])
                ->count();
            if ($invite > 0) {
                //获取被邀请者活动期间前三次投资订单id
                $orderData = OnlineOrder::find()
                    ->select('id')
                    ->where(['uid' => $order->uid, 'status' => 1])
                    ->andWhere(['between', 'order_time', $promo->startAt, $promo->endAt])
                    ->orderBy(['order_time' => SORT_ASC])
                    ->limit(3)
                    ->all();
                $orderIds = ArrayHelper::getColumn($orderData, 'id');
                //获取邀请者
                $user = User::find()
                    ->innerJoin('invite_record', 'user.id = invite_record.user_id')
                    ->where(['invite_record.invitee_id' => $order->uid])
                    ->one();
                if (count($orderIds) > 0 && $user) {
                    $mess = '';
                    //首次投资给邀请者发代金券
                    if ($orderIds[0] === $order->id) {
                        //todo 等更改代金券时候需要手工更改代码
                        if ($order->order_money < 10000) {
                            //发放30元代金券 0011:10000-30
                            $coupon = CouponType::find()->where(['sn' => '0011:10000-30'])->one();
                            if ($coupon && $coupon->allowIssue()) {
                                $userCoupon = UserCoupon::addUserCoupon($user, $coupon);
                                $userCoupon->save();
                                $mess = '30元代金券';
                            }
                        } else {
                            //发放50元代金券 0011:10000-50
                            $coupon = CouponType::find()->where(['sn' => '0011:10000-50'])->one();
                            if ($coupon && $coupon->allowIssue()) {
                                $userCoupon = UserCoupon::addUserCoupon($user, $coupon);
                                $userCoupon->save();
                                $mess = '50元代金券';
                            }
                        }
                    }
                    //前三次投资给邀请者发现金红包
                    if (in_array($order->id, $orderIds)) {
                        $money = round($order->order_money / 1000, 1);
                        //判断邀请者是否有过投资
                        $record = OnlineOrder::find()->where(['status' => 1, 'uid' => $user->id])->count();
                        if ($money > 0 && $record > 0) {
                            AccountService::userTransfer($user, $money);
                            $mess = $mess ? $mess . '和' . $money . '元现金红包' : $money . '元现金红包';
                        }
                    }
                    //发短信
                    if ($mess) {
                        $sms = new SmsMessage([
                            'template_id' => \Yii::$app->params['sms']['invite_bonus'],
                            'level' => SmsMessage::LEVEL_LOW,
                        ]);
                        $message = [
                            $order->mobile,
                            $mess,
                        ];
                        $sms->uid = $user->id;
                        $sms->mobile = $user->mobile;
                        $sms->message = json_encode($message);
                        $sms->save();
                    }
                }
            }
        }
    }
}
