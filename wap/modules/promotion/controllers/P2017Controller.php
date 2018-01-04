<?php

namespace wap\modules\promotion\controllers;

use common\models\coupon\CouponType;
use common\models\coupon\UserCoupon;
use common\models\mall\PointRecord;
use common\models\offline\OfflineUser;
use common\models\order\OnlineOrder;
use common\models\product\OnlineProduct;
use common\models\promo\AnnualReport;
use common\models\stats\Perf;
use common\models\transfer\Transfer;
use common\models\user\MoneyRecord;
use common\models\user\User;
use common\utils\StringUtils;
use Yii;
use yii\filters\AccessControl;

class P2017Controller extends BaseController
{
    public $layout = '@app/views/layouts/fe';

    public function behaviors()
    {
        $access = [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
                'except' => [
                    's1',
                    's2',
                ],
            ],
        ];

        return $access;
    }

    /**
     * 17年及以前注册用户-年报展示页
     */
    public function actionAnnualReport()
    {
        $user = $this->getAuthedUser();
        $userInfo = $user->info; //如果不存在用户信息，为错误数据，报404
        if (null === $userInfo) {
            throw $this->ex404();
        }

        //用户注册日期
        $registerDate = new \DateTime(date('Y-m-d', $user->created_at));

        //如果用户为2018新注册用户，跳转到新的落地页
        if ($registerDate->format('Y') > 2017) {
            return $this->redirect('new');
        }

        //平台上线日期
        $platOnlineDate = new \DateTime('2016-05-20');

        //17年年末日期
        $annualEndDate = new \DateTime('2017-12-31');

        //首次投资日期
        $investDate = new \DateTime($userInfo->firstInvestDate);

        //用户17年前是否投资
        $userIsInvested = $userInfo->isInvested && $investDate <= $annualEndDate; //用户是否投资

        //520当天投资获得积分 PointRecord::TYPE_LOAN_ORDER,PointRecord::TYPE_FIRST_LOAN_ORDER_POINTS_1
        $investPointsIn520 = (float) PointRecord::find()
            ->where(['date(recordTime)' => '2017-05-20'])
            ->andWhere(['user_id' => $user->id])
            ->andWhere(['in', 'ref_type', [PointRecord::TYPE_LOAN_ORDER, PointRecord::TYPE_FIRST_LOAN_ORDER_POINTS_1]])
            ->sum('incr_points');

        //520当天活动获得红包
        $redPacketIn520 = (float) Transfer::find()
            ->where(['status' => Transfer::STATUS_SUCCESS])
            ->andWhere(['user_id' => $user->id])
            ->andWhere(['date(createTime)' => '2017-05-20'])
            ->sum('amount');

        $showStaticIn520 = 0 === bccomp($investPointsIn520, 0, 2) && 0 === bccomp($redPacketIn520, 0, 2);
        if ($showStaticIn520) {
            $investPointsIn520 = 0;
            $redPacketIn520 = 0;
        }

        //基础数据处理
        $platOnlineDateOut = $platOnlineDate->format('Y.n.j'); //平台上线日期
        $registerDateOut = $registerDate->format('Y.n.j'); //注册日期
        $platToRegisterDays = $registerDate->diff($platOnlineDate)->days + 1; //第n天注册
        $investDateOut = $investDate->format('Y.n.j'); //第一次投资日期
        $userData = [
            'platOnlineDateOut' => $platOnlineDateOut, //平台上线日期
            'registerDateOut' => $registerDateOut, //注册日期
            'platToRegisterDays' => $platToRegisterDays, //第n天注册
            'userIsInvested' => (int) $userIsInvested, //用户是否18年前投资
            'investDateOut' => $investDateOut, //首投日期
            'showStaticIn520' => (int) $showStaticIn520, //是否展示静态页（无需后台渲染）
            'investPointsIn520' => $investPointsIn520, //520当天投资获得积分
            'redPacketIn520' => $redPacketIn520, //520当天获得的红包
        ];
        $userData = array_merge($userData, $this->getUserPartialData($user));
        $platData = $this->getPlatData();

        return $this->render('index', array_merge($userData, $platData));
    }

    /**
     * 17年及以前注册用户-查看我的年报
     */
    public function actionS1()
    {
        $sc = Yii::$app->request->get('sc');
        if (empty($sc) || null === ($user = User::find()->where(['usercode' => $sc])->one())) {
            throw $this->ex404();
        }

        $wxInfo = $this->getWxInfo($user->id);
        if (null === $wxInfo['headImgUrl']) {
            $wxInfo['headImgUrl'] = FE_BASE_URI.'wap/campaigns/active20180102/images/share-default.png';
        }
        if (null === $wxInfo['nickName']) {
            $wxInfo['nickName'] = StringUtils::obfsMobileNumber($user->getMobile());
        }

        //平台信息
        $platData = $this->getPlatData();
        //用户信息
        $userData = $this->getUserPartialData($user);
        $userData = array_merge($userData, $wxInfo);
        $data = array_merge($platData, $userData);

        return $this->render('s1', $data);
    }

    /**
     * 18年注册用户-查看我的年报
     */
    public function actionS2()
    {
        //平台信息
        $platData = $this->getPlatData();

        return $this->render('s2', $platData);
    }

    /**
     * 18年注册用户-年报页（带分享按钮）
     */
    public function actionNew()
    {
        //平台信息
        $platData = $this->getPlatData();

        return $this->render('new', $platData);
    }

    //平台信息
    private function getPlatData()
    {
        //当天日期
        $todayDate = new \DateTime(date('Y-m-d'));
        //平台上线日期
        $platOnlineDate = new \DateTime('2016-05-20');
        //平台统计数据
        $platStats = Perf::getStats();
        $platRefundAmount = $platStats['totalRefundAmount']; //平台累计兑付金额
        $platRefundInterest = $platStats['totalRefundInterest']; //平台累计带来收益
        $platSafeDays = $todayDate->diff($platOnlineDate)->days; //平台运营天数

        return [
            'platSafeDays' => $platSafeDays, //平台运营天数
            'platRefundAmount' => bcdiv($platRefundAmount, 100000000, 0), //平台兑付金额（亿）
            'platRefundInterest' => bcdiv($platRefundInterest, 100000000, 0), //平台兑付收益（亿）
        ];
    }

    private function getUserPartialData($user)
    {
        //用户注册日期
        $registerDate = new \DateTime(date('Y-m-d', $user->created_at));

        //当天日期
        $todayDate = new \DateTime(date('Y-m-d'));

        //17年年末日期
        $annualEndDate = new \DateTime('2017-12-31');
        $annualEndDateFormat = $annualEndDate->format('Y-m-d');

        //线上累计收益，线上累计收益排名
        $totalProfit = 0;
        $totalProfitRanking = 0; //累计收益排名：无回款 0； 有回款 1-99；
        $annualExport = AnnualReport::find()
            ->where(['user_id' => $user->id])
            ->one();
        if (null !== $annualExport) {
            $totalProfit = $annualExport->totalProfit;
            $annualExportCount = (int) AnnualReport::find()->count();
            $totalProfitRanking = bcdiv(bcmul($annualExport->id, 100, 14), $annualExportCount, 0);
            if ('0' === $totalProfitRanking) {
                $totalProfitRanking = 1;
            }
            if ('100' === $totalProfitRanking) {
                $totalProfitRanking = 99;
            }
        }

        //累计积分 = 累计线上获得积分 + 累计线下获得积分
        $onlineUserTotalPoints = (float) PointRecord::find()
            ->where(['user_id' => $user->id])
            ->andWhere(['isOffline' => false])
            ->andWhere(['<=', 'date(recordTime)', $annualEndDateFormat])
            ->sum('incr_points');
        $offlineUser = OfflineUser::find()
            ->where(['onlineUserId' => $user->id])
            ->one();
        $offlineUserTotalPoints = 0;
        if (null !== $offlineUser) {
            $offlineUserTotalPoints = (float) PointRecord::find()
                ->where(['user_id' => $offlineUser->id])
                ->andWhere(['isOffline' => true])
                ->andWhere(['<=', 'date(recordTime)', $annualEndDateFormat])
                ->sum('incr_points');
        }
        $totalPoints = bcadd($onlineUserTotalPoints, $offlineUserTotalPoints, 2);

        //现金红包总额
        $totalRedPacket = (float) MoneyRecord::find()
            ->where(['uid' => $user->id])
            ->andWhere(['type' => MoneyRecord::TYPE_CASH_GIFT])
            ->andWhere(['<=', 'date(from_unixtime(created_at))', $annualEndDateFormat])
            ->sum('in_money');
        $totalRedPacket = bcdiv($totalRedPacket, 1, 2);

        //代金券个数
        $uc = UserCoupon::tableName();
        $ct = CouponType::tableName();
        $query = UserCoupon::find()
            ->innerJoinWith('couponType')
            ->where(["$uc.user_id" => $user->id])
            ->andWhere(['<=', "date(from_unixtime($uc.created_at))", $annualEndDateFormat]);
        $queryBonus = clone $query;
        $couponNum = (int) $query->andWhere(["$ct.type" => 0])->count();

        //加息券个数
        $bonusCouponNum = (int) $queryBonus->andWhere(["$ct.type" => 1])->count();
        $registerToTodayDays = $todayDate->diff($registerDate)->days;

        //用户投资慈善金额
        $charityAmount = '0.00';
        $loanIds = OnlineProduct::find()
            ->andWhere(['like', 'tags', '慈善专属'])
            ->column();
        $charityInvestTotal = (float) OnlineOrder::find()
            ->where(['status' => OnlineOrder::STATUS_SUCCESS])
            ->andWhere(['uid' => $user->id])
            ->andWhere(['in', 'online_pid', $loanIds])
            ->andFilterWhere(['<=', 'date(from_unixtime(created_at))', $annualEndDateFormat])
            ->sum('order_money');
        if (bccomp($charityInvestTotal, 0, 2) > 0) {
            $charityAmount = bcdiv($charityInvestTotal, 10000, 2);
        }

        return [
            'registerToTodayDays' => $registerToTodayDays, //注册至现在时间间隔
            'totalProfit' => $totalProfit, //平台累计收益，只有线上
            'totalProfitRanking' => $totalProfitRanking, //平台累计收益排名，只有线上
            'totalPoints' => rtrim(rtrim($totalPoints, '0'), '.'), //累计积分，包含线上与线下
            'totalRedPacket' => rtrim(rtrim($totalRedPacket, '0'), '.'), //累计红包，只有线上
            'couponNum' => $couponNum, //代金券获得个数，只有线上
            'bonusCouponNum' => $bonusCouponNum, //加息券获得个数，只有线上
            'charityAmount' => rtrim(rtrim($charityAmount, '0'), '.'), //慈善捐赠金额
            'userCode' => $user->usercode, //用户标识
        ];
    }
}
