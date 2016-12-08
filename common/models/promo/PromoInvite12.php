<?php

namespace common\models\promo;


use common\models\coupon\CouponType;
use common\models\coupon\UserCoupon;
use common\models\order\OnlineOrder;
use common\models\user\User;
use common\service\AccountService;
use common\service\SmsService;
use wap\modules\promotion\models\RankingPromo;
use yii\helpers\ArrayHelper;

/**
 * 12月份邀请好友活动
 */
class PromoInvite12
{
    public $promo;

    //代金券sn
    const COUPON_30_SN = '0019:10000-30';
    const COUPON_50_SN = '0019:10000-50';

    public function __construct(RankingPromo $promo)
    {
        $this->promo = $promo;
    }

    /**
     * 给被邀请者送代金券
     * @param User $invitee            被邀请者
     */
    public function addInviteeCoupon(User $invitee)
    {
        //被邀请者注册送50元代金券
        if ($invitee->isInvited($this->promo->startAt, $this->promo->endAt)) {
            $couponType = CouponType::findOne(['sn' => self::COUPON_50_SN]);
            try {
                if ($couponType && $couponType->allowIssue()) {
                    UserCoupon::addUserCoupon($invitee, $couponType)->save();
                }
            } catch (\Exception $ex) {
            }
        }
    }

    /**
     * 用户投资成功之后处理逻辑
     * @param OnlineOrder $order
     */
    public function doAfterSuccessLoanOrder(OnlineOrder $order)
    {
        $this->dealWithOrder($order);
    }

    //投资成功之后处理逻辑
    private function dealWithOrder(OnlineOrder $order)
    {
        $promo = $this->promo;
        $user = $order->user;
        if (intval($order->status) === 1 && $promo->isActive($user)) {
            //判断是不是被邀请者
            $invite = InviteRecord::find()
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
                            //发放30元代金券
                            $coupon = CouponType::find()->where(['sn' => self::COUPON_30_SN])->one();
                            if ($coupon && $coupon->allowIssue()) {
                                $userCoupon = UserCoupon::addUserCoupon($user, $coupon);
                                $userCoupon->save();
                                $mess = '30元代金券';
                            }
                        } else {
                            //发放50元代金券
                            $coupon = CouponType::find()->where(['sn' => self::COUPON_50_SN])->one();
                            if ($coupon && $coupon->allowIssue()) {
                                $userCoupon = UserCoupon::addUserCoupon($user, $coupon);
                                $userCoupon->save();
                                $mess = '50元代金券';
                            }
                        }
                    }
                    //前三次投资给邀请者发现金红包
                    $loan = $order->loan;
                    if (in_array($order->id, $orderIds) && !$loan->is_xs) {
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
                        $templateId = \Yii::$app->params['sms']['invite_bonus'];
                        $message = [
                            $order->mobile,
                            $mess,
                        ];

                        SmsService::send($user->mobile, $templateId, $message, $user);
                    }
                }
            }
        }
    }
}