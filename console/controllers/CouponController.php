<?php

namespace console\controllers;

use common\models\order\OnlineOrder;
use common\models\payment\Repayment;
use common\models\product\OnlineProduct;
use common\models\promo\TicketToken;
use common\models\sms\SmsConfig;
use common\models\coupon\UserCoupon;
use common\models\user\User;
use common\models\coupon\CouponType;
use common\models\user\UserInfo;
use common\service\SmsService;
use common\service\WDSmsService;
use common\utils\SecurityUtils;
use common\utils\StringUtils;
use Yii;
use yii\console\Controller;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;

class CouponController extends Controller
{
    /**
     * 给即将过期的代金券发送过期短信提醒.
     * 当前为3天(若今天为周一，则提示有效期到周三的)
     *
     * @param int $expireDay 离现在为止即将过期的天数
     *
     * @throws \Exception
     */
    public function actionReminder($expireDay = 3)
    {
        $u = User::tableName();
        $ct = CouponType::tableName();
        $contactTel = Yii::$app->params['platform_info.contact_tel'];
        $expiryDate = date('Y-m-d', strtotime('+' . ($expireDay - 1) . 'days'));

        $userCoupons = UserCoupon::find()
            ->innerJoin($ct, "couponType_id = $ct.id")
            ->innerJoin($u, "user_id = $u.id")
            ->where(['isUsed' => 0, "$ct.isDisabled" => 0, 'expiryDate' => $expiryDate])
            ->all();

        if (!empty($userCoupons)) {
            $peopleCount = 0;
            $groupUserCoupons = ArrayHelper::index($userCoupons, null, 'user_id');
            $wodong = new WDSmsService();
            foreach ($groupUserCoupons as $userId => $userCoupons) {
                $user = User::findOne($userId);
                $num = 0;
                $amount = 0;
                $peopleCount++;
                $includedBonus = false;
                foreach ($userCoupons as $userCoupon) {
                    $num++;
                    $couponType = $userCoupon->couponType;
                    if (0 === $couponType->type) {
                        $amount = bcadd($amount, $couponType->amount, 2);
                    } else {
                        $includedBonus = true;
                    }
                }
                if ($includedBonus) {
                    $param1 = $num.'张';
                } else {
                    $param1 = '共计'.StringUtils::amountFormat2($amount).'元';
                }

                //沃动短信通道
                $mobile = SecurityUtils::decrypt($user->safeMobile);
                $templateContent = '【温都金服】尊敬的客户您好，您有{1}优惠券即将过期，请尽快使用，地址{2}，如有疑问请致电{3}，回复TD退订';
                $templateMessage = [
                    $param1,
                    'https://m.wenjf.com/',
                    $contactTel,
                ];
                $templateParam = [
                    '{1}',
                    '{2}',
                    '{3}',
                ];
                $content = str_replace($templateParam, $templateMessage, $templateContent);

                $res = $wodong->send($mobile, $content);
                if (!$res) {
                    throw new \Exception('给手机号为'.$mobile.'发送短信内容【'.$content.'】失败');
                }
                usleep(500000);
            }
            $this->stdout('共给' . $peopleCount . '人发出代金券过期提醒!', Console::BG_YELLOW);
            return Controller::EXIT_CODE_NORMAL;
        }
        $this->stdout('当前没有需要代金券过期提醒的用户!', Console::BG_YELLOW);
        return Controller::EXIT_CODE_ERROR;
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
        $templateId = '198136';
        $count = 0;

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
            SmsService::send($user->getMobile(), $templateId, ['1400', 'm.wenjf.com'], $user);
            ++$count;
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
            SmsService::send($user->getMobile(), $templateId, $smsConfig->getConfig(), $user);
            ++$count;
        }

        $this->stdout('共给'.$count.'位用户发送了短信!', Console::BG_YELLOW);

        return Controller::EXIT_CODE_NORMAL;
    }

    private function getCoupons($type)
    {
        $sns = [];
        switch ($type) {
            case 1:
                $sns = ['invest_1000_5'];
                break;
            case 2:
                $sns = ['invest_1000_5', 'invest_5000_10'];
                break;
            case 3:
                $sns = ['invest_10000_20'];
                break;
            case 4:
                $sns = ['invest_10000_20', 'invest_50000_50'];
                break;
        }

        return CouponType::find()
            ->where(['in', 'sn', $sns])
            ->all();
    }

    /**
     * 临时一次性代码 $group = 1 or $group = 2
     *
     * 投资一次，首投金额＜10000或投资两次、两次中最高那笔投资额＜10000
     * php yii coupon/send 1
     *
     * 投资一次，首投金额≥10000或投资两次、两次中最高那笔投资额≥10000
     * php yii coupon/send 2
     *
     */
    public function actionSend($group)
    {
        $info = UserInfo::tableName();
        if (!in_array($group, ['1', '2'])) {
            $this->stdout('分组不对');
            return false;
        }
        if ('1' === $group) {
            $query = User::find()
                ->innerJoinWith('info')
                ->where(["$info.investCount" => 1])
                ->andWhere(['<', "$info.firstInvestAmount", 10000])
                ->orWhere(["$info.investCount" => 2, "if($info.firstInvestAmount < 10000 and $info.lastInvestAmount < 10000, 1, 0)" => 1])
                ->andWhere(["$info.isInvested" => true])
                ->andFilterWhere(['<=', "$info.lastInvestDate", date('Y-m-d')]);
            $couponTypes1 = $this->getCoupons(1);
            $couponTypes2 = $this->getCoupons(2);
            $fileName = '/tmp/user-coupon-1.csv';
            $errorFileName = '/tmp/user-coupon-1-error.csv';
        } else {
            $query = User::find()
                ->innerJoinWith('info')
                ->where(["$info.investCount" => 1])
                ->andWhere(['>=', "$info.firstInvestAmount", 10000])
                ->orWhere(["$info.investCount" => 2, "if($info.firstInvestAmount >= 10000 or $info.lastInvestAmount >= 10000, 1, 0)" => 1])
                ->andWhere(["$info.isInvested" => true])
                ->andFilterWhere(['<=', "$info.lastInvestDate", date('Y-m-d')]);
            $couponTypes1 = $this->getCoupons(3);
            $couponTypes2 = $this->getCoupons(4);
            $fileName = '/tmp/user-coupon-2.csv';
            $errorFileName = '/tmp/user-coupon-2-error.csv';
        }
        if (file_exists($errorFileName)) {
            @unlink($errorFileName);
        }
        $cQuery = Clone $query;
        $userCount = $query->count();
        $this->stdout('共有' . $userCount . '人待发放代金券');
        $halfCount = ceil($userCount / 2);
        $users = $cQuery->all();
        foreach ($users as $k => $user) {
            if ($k < $halfCount) {
                $couponTypes = $couponTypes1;
            } else {
                $couponTypes = $couponTypes2;
            }
            $couponAmount = 0;
            $flag = true;
            foreach ($couponTypes as $couponType) {
                try {
                    $ticketToken = new TicketToken();
                    $ticketToken->key = date('Ymd') . '-' . $user->id . '-' . $couponType->id;
                    $ticketToken->save(false);
                    UserCoupon::addUserCoupon($user, $couponType)->save();
                } catch (\Exception $ex) {
                    file_put_contents($errorFileName, $user->id.','.$couponType->id.PHP_EOL, FILE_APPEND);
                    $flag = false;
                    continue;
                }
                $couponAmount = $couponAmount + $couponType->amount;
            }
            if ($flag) {
                $data = $user->real_name . "\t" . $user->mobile . "\t" . $couponAmount . '元代金券' . PHP_EOL;
                file_put_contents($fileName, $data, FILE_APPEND);
            }
        }
        $this->stdout('发放结束！');
    }
}
