<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\models\sms\SmsMessage;
use common\models\coupon\UserCoupon;
use common\models\user\User;
use common\models\coupon\CouponType;
use common\utils\StringUtils;

class CouponController extends Controller
{

    /**
     * 给即将过期的代金券发送过期短信提醒
     *
     * @param int $expireDay 离现在为止即将过期的天数(若今天为周一，则提示周五的)
     *
     * @throws \Exception
     */
    public function actionReminder($expireDay = 5)
    {
        $u = User::tableName();
        $ct = CouponType::tableName();

        $contactTel = \Yii::$app->params['contact_tel'];
        $expiryDate = date('Y-m-d', strtotime('+' . ($expireDay - 1) . 'days'));

        $userCoupons = UserCoupon::find()
            ->select("user_id, $u.mobile, $ct.amount")
            ->innerJoin($ct, "couponType_id = $ct.id")
            ->innerJoin($u, "user_id = $u.id")
            ->where(['isUsed' => 0, "$ct.isDisabled" => 0, 'expiryDate' => $expiryDate])
            ->orderBy(['user_id' => SORT_DESC, 'amount' => SORT_DESC])
            ->asArray()
            ->all();
        try {
            $transaction = Yii::$app->db->beginTransaction();
            if (!empty($userCoupons)) {
                $lastUserId = 0;
                $count = 0;
                foreach ($userCoupons as $userCoupon) {
                    $user_id = $userCoupon['user_id'];
                    if ($user_id !== $lastUserId) {
                        $message = [
                            StringUtils::amountFormat2($userCoupon['amount']) . '元',
                            $expireDay,
                            $contactTel,
                        ];
                        $sms = new SmsMessage([
                            'template_id' => \Yii::$app->params['sms']['coupon_reminder'],
                            'level' => SmsMessage::LEVEL_LOW,
                        ]);
                        $sms->uid = $user_id;
                        $sms->mobile = $userCoupon['mobile'];
                        $sms->message = json_encode($message);
                        if (!$sms->save()) {
                            throw new \Exception('本条插入失败，数据记录为' . json_encode($sms->getAttributes()));
                        }
                        $count++;
                        $lastUserId = $user_id;
                    } else {
                        continue;
                    }
                }
                $transaction->commit();
                echo '共给' . $count . '人发出代金券过期提醒';
                exit;
            }
            echo '当前没有需要代金券过期提醒的用户';
            exit;
        } catch(\Exception $ex) {
            $transaction->rollBack();
            throw new \Exception($ex->getMessage());
        }
    }
}
