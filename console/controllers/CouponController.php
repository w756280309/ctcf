<?php

namespace console\controllers;

use common\models\order\OnlineOrder;
use common\models\payment\Repayment;
use common\models\product\OnlineProduct;
use common\models\sms\SmsConfig;
use common\models\sms\SmsMessage;
use common\models\coupon\UserCoupon;
use common\models\user\User;
use common\models\coupon\CouponType;
use common\utils\SecurityUtils;
use common\utils\StringUtils;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;

class CouponController extends Controller
{
    /**
     * 给即将过期的代金券发送过期短信提醒.
     *
     * @param int $expireDay 离现在为止即将过期的天数(若今天为周一，则提示有效期到周三的)
     *
     * @throws \Exception
     */
    public function actionReminder($expireDay = 3)
    {
        $u = User::tableName();
        $ct = CouponType::tableName();
        $contactTel = \Yii::$app->params['contact_tel'];
        $expiryDate = date('Y-m-d', strtotime('+' . ($expireDay - 1) . 'days'));

        $userCoupons = UserCoupon::find()
            ->innerJoin($ct, "couponType_id = $ct.id")
            ->innerJoin($u, "user_id = $u.id")
            ->where(['isUsed' => 0, "$ct.isDisabled" => 0, 'expiryDate' => $expiryDate])
            ->orderBy(['user_id' => SORT_DESC, 'amount' => SORT_DESC])
            ->all();
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if (!empty($userCoupons)) {
                $lastUserId = 0;
                $count = 0;
                foreach ($userCoupons as $userCoupon) {
                    $user_id = $userCoupon->user->id;
                    if ($user_id !== $lastUserId) {
                        $message = [
                            StringUtils::amountFormat2($userCoupon->couponType->amount) . '元',
                            'https://m.wenjf.com/',
                            $contactTel,
                        ];
                        //发送短信
                        $sms = SmsMessage::initSms($userCoupon->user, $message, 155508);
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
                $this->stdout('共给' . $count . '人发出代金券过期提醒!', Console::BG_YELLOW);

                return Controller::EXIT_CODE_NORMAL;
            }
            $this->stdout('当前没有需要代金券过期提醒的用户!', Console::BG_YELLOW);
            return Controller::EXIT_CODE_ERROR;
        } catch(\Exception $ex) {
            $transaction->rollBack();
            throw new \Exception($ex->getMessage());
        }
    }

    /**
     * 注册后三天未投资的用户,发送短信提醒.
     *
     * 例如用户注册是在1日,而发送短信的日子则为3日,两边的节点都算在3天内.
     */
    public function actionNoOrder($days = 3)
    {
        $time = strtotime('today - '.($days - 1).' days');

        //获取指定用户列表
        $users = User::find()
            ->where(['between', 'created_at', $time, $time + 60 * 60 * 24 - 1])
            ->all();
        $templateId = '155514';
        $count = 0;

        $smsConfig = SmsConfig::findOne(['template_id' => $templateId]);

        if (null === $smsConfig) {
            $this->stdout('没有找到对应的短信模板!', Console::BG_YELLOW);

            return Controller::EXIT_CODE_ERROR;
        }

        foreach ($users as $user) {
            //开通免密且已投资的用户,跳过
            if ($user->mianmiStatus && $user->orderCount() > 0) {
                continue;
            }

            //O2O渠道注册的用户不发送首投送积分的短信
            if ($user->isO2oRegister()) {
                continue;
            }

            //发送短信
            $sms = SmsMessage::initSms($user, $smsConfig->getConfig(), $templateId);

            if ($sms->save(false)) {
                ++$count;
            }
        }

        $this->stdout('共给'.$count.'位用户发送了短信!', Console::BG_YELLOW);

        return Controller::EXIT_CODE_NORMAL;
    }

    /**
     * 只投新手标，新手标回款后3天未投资其他项目.
     *
     * 例如新手标回款是在1日,而发送短信的日子则为3日,两边的节点都算在3天内.
     */
    public function actionOnlyXs($days = 3)
    {
        $dateTime = (new \DateTime('today - '.($days - 1).' days'))->format('Y-m-d');

        $l = OnlineProduct::tableName();

        //获取指定日期回款的新手标ID
        $loanIds = Repayment::find()
            ->innerJoinWith('loan')
            ->where(['like', 'refundedAt', $dateTime])
            ->andWhere(['is_xs' => true])
            ->select("$l.id")
            ->column();

        if (empty($loanIds)) {
            $this->stdout('本次没有找到指定已回款的新手标!', Console::BG_YELLOW);

            return Controller::EXIT_CODE_ERROR;
        }

        //获取购买指定新手标的用户ID
        $userIds = OnlineOrder::find()
            ->innerJoinWith('loan')
            ->where(['online_pid' => $loanIds])
            ->select('uid')
            ->distinct('uid')
            ->column();

        //获取指定用户列表
        $users = User::findAll(['id' => $userIds]);
        $templateId = '155515';
        $count = 0;

        $smsConfig = SmsConfig::findOne(['template_id' => $templateId]);

        if (null === $smsConfig) {
            $this->stdout('没有找到对应的短信模板!', Console::BG_YELLOW);

            return Controller::EXIT_CODE_ERROR;
        }

        foreach ($users as $user) {
            //除了新手标还投资过其他项目的,跳过
            if ($user->orderCount() > 1) {
                continue;
            }

            //发送短信
            $sms = SmsMessage::initSms($user, $smsConfig->getConfig(), $templateId);

            if ($sms->save(false)) {
                ++$count;
            }
        }

        $this->stdout('共给'.$count.'位用户发送了短信!', Console::BG_YELLOW);

        return Controller::EXIT_CODE_NORMAL;
    }
}
