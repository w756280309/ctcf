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
     * 给即将过期的优惠券发送过期短信提醒.
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
                $templateContent = '【温都金服】尊敬的客户您好，您有{1}优惠券即将过期，请尽快使用，如有疑问请致电{2}，回复TD退订';
                $templateMessage = [
                    $param1,
                    $contactTel,
                ];
                $templateParam = [
                    '{1}',
                    '{2}',
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

    public function actionSendByConfig($couponTypeIds, $groupId)
    {
        $fileName = Yii::getAlias('@app/runtime/send_coupon_'.date('YmdHis').'.txt');
        $userMobiles = $this->getUserMobilesByGroupId($groupId);
        $couponTypeIdsArr = explode(',', $couponTypeIds);
        $couponTypes = CouponType::find()
            ->where(['id'=>$couponTypeIdsArr])
            ->all();
        $this->stdout('待发放用户'.count($userMobiles).'人');
        $num = 0;
        foreach ($userMobiles as $mobile) {
            try {
                $user = User::findOne(['safeMobile' => SecurityUtils::encrypt($mobile)]);
                $ticketToken = new TicketToken();
                $ticketToken->key = date('Ymd') . '-' . $user->id . '-' . $groupId;
                $ticketToken->save(false);
                foreach ($couponTypes as $couponType) {
                    UserCoupon::addUserCoupon($user, $couponType)->save();
                }
                $num++;
            } catch (\Exception $ex) {
                file_put_contents($fileName, $mobile.PHP_EOL, FILE_APPEND);
                continue;
            }
        }
        $this->stdout('实际发放用户人数为'.$num.'人');
    }

    private function getUserMobilesByGroupId($id)
    {
        $userMobiles = [];
        if ('1' === $id) {
            // 发送10元和5元代金券 id=126,127
            $userMobiles = [
                '13634293080',
                '13868314060',
                '13868584477',
                '13958943127',
                '13957725288',
                '13957780900',
                '18070071185',
                '13967726993',
                '13806882595',
                '18905770511',
                '13625778817',
                '13707184528',
                '15857786907',
                '13819716832',
                '13758780861',
                '18267713169',
                '18118990532',
                '15868537881',
                '13968881197',
                '18658388687',
                '15958700525',
                '15990745799',
                '13566196866',
                '13968833762',
                '15058329408',
                '18657713658',
                '15657770568',
                '13806846230',
                '13780192603',
                '15058315392',
                '13968829775',
                '13676704330',
                '18758788606',
                '18072119532',
                '13957735312',
                '13858858269',
                '13736790058',
                '15355972061',
                '13676717456',
                '13758467546',
                '13616870157',
                '13968839136',
                '13566290060',
                '13968841870',
                '13758830884',
                '13566238008',
                '13806685314',
                '15088585669',
                '13858777585',
                '15858510043',
                '13857775463',
                '13823354778',
                '13587677576',
                '15355968832',
                '13858821330',
                '13567789094',
                '13968825374',
                '15517594856',
                '13676480248',
                '15356878320',
                '13858283339',
                '15034569125',
                '13601070056',
                '13285987963',
                '13276917925',
                '13950456647',
                '13950455947',
                '13075848732',
                '18558725043',
                '18558725048',
                '13566195776',
                '13606875554',
                '13777797952',
                '15990016161',
                '13075705918',
                '13968813828',
                '13676580859',
                '13588357746',
                '15057532906',
                '13957799058',
                '13605779975',
                '13515878696',
                '13587520380',
                '13758721930',
                '13600666067',
                '13806891521',
                '13295793320',
                '13968875400',
                '18967771234',
                '15258760503',
                '13566229022',
                '13600669095',
                '13199313139',
                '13806877008',
            ];
        } elseif ('2' === $id) {
            // 发送50代金券 id = 30
            $userMobiles = [
                '18435699897',
                '13957790639',
                '18958796688',
                '13806893345',
                '13857700306',
                '13732066695',
                '13017872738',
                '13777760099',
                '13857779713',
                '15305771107',
                '13566222675',
                '13968860636',
                '13967781171',
                '13957792359',
                '15858822566',
                '15825678871',
                '13777763710',
                '13600686720',
                '13958821601',
                '18167291519',
                '13736310208',
                '13566283804',
                '18815001816',
                '15968185321',
                '13806801778',
                '15058303803',
                '13388531133',
                '15057331901',
                '15057702560',
                '13906879525',
                '15257705317',
                '13806693505',
                '13676561376',
                '13868741486',
                '13868861256',
                '13735450409',
                '13868843323',
                '13957717613',
                '13958714605',
                '13506660689',
                '13587429727',
                '13906630987',
                '13706666910',
                '13587897504',
                '13587878785',
                '13868802025',
                '13868120973',
                '13968968555',
                '13566222658',
                '13506651558',
                '18906653595',
                '13185778289',
                '13605772557',
                '13806557890',
                '13587523893',
                '18106639928',
                '13967760881',
                '13695898863',
                '13706621616',
                '13777774929',
                '13868612010',
                '13456007379',
                '13736361975',
                '13857766630',
                '13857740982',
                '13587618017',
                '13757891763',
                '15657728121',
                '13566194834',
                '15990761519',
                '18857712125',
                '13325778515',
                '13868865125',
                '13957751997',
                '13967722859',
                '13968867855',
                '18958981998',
                '13587963114',
                '13858847872',
                '18968788828',
                '13506661312',
                '15258026416',
                '13588353220',
                '13706681119',
                '13736933114',
                '13858810061',
                '18967771116',
                '13868868867',
                '13906636130',
                '15038125177',
                '15325536061',
                '13356175810',
                '13587769262',
                '15957707950',
                '15558999656',
                '13736355678',
                '13566233215',
                '13857733996',
                '13706661031',
                '13306777699',
                '13805872343',
                '13486426220',
                '13626577262',
                '18906657162',
                '13545023228',
                '15088910804',
                '13605773576',
                '18072003000',
                '13587883892',
                '13858841039',
                '13957779895',
                '13968805339',
                '15205870680',
                '13868655886',
                '13605770696',
                '13705776835',
                '13857735286',
                '18968850785',
                '15336513759',
                '18688113100',
                '13566566241',
                '13656508101',
                '15356517386',
                '13868240006',
            ];
        } elseif ('3' === $id) {
            $userMobiles = [
                '13750892318',
                '18072155979',
                '13600670340',
                '15988760895',
                '13758710572',
                '15057395023',
                '15858582827',
                '18627934677',
                '13968848822',
                '13905775678',
                '13868586088',
                '15858587774',
                '13355888708',
                '13757897187',
                '15988717041',
                '13616657123',
                '13858831005',
                '15957700106',
                '18968839580',
                '15305770566',
                '15805877049',
                '13967719535',
                '15888293958',
                '13587651821',
                '15157714255',
                '18969706983',
                '13098447041',
                '15057766981',
                '13857797765',
                '13868623077',
                '13777119798',
                '15355086718',
                '13957739408',
                '13868627167',
                '15858706127',
                '18968818432',
                '15888275050',
                '18658855776',
            ];
        }

        return $userMobiles;
    }
}
