<?php

namespace console\modules\ctcf\controllers;

use common\models\coupon\CouponType;
use common\models\coupon\UserCoupon;
use common\models\mall\PointRecord;
use common\models\user\User;
use common\models\user\UserInfo;
use common\service\PointsService;
use yii\console\Controller;
use Yii;

class OldUserController extends Controller
{
    /**
     * 未投资过：送888红包
     * 已投资过：根据投资年化总额（购买所有产品都算，包括新手和转让）根据规则补发相应的红包和积分
     */
    public function actionReward($startDate = '2000-01-01', $endDate = '2018-02-15')
    {
        $users = User::find()->all();
        foreach ($users as $user) {
            $userInfo = UserInfo::findOne(['user_id' => $user->id]);
            if (null !== $userInfo && $userInfo->isInvested) {
                $annualInvest = UserInfo::calcAnnualInvest($user->id, $startDate, $endDate) / 10000;
                if ($annualInvest > 0 && $annualInvest < 20) {
                    $couponArray = Yii::$app->params['old_user_invested_level_1_coupon'];
                    $point = Yii::$app->params['old_user_invested_level_1_point'];
                    $popType = 3;
                } elseif ($annualInvest >= 20 && $annualInvest < 50) {
                    $couponArray = Yii::$app->params['old_user_invested_level_2_coupon'];
                    $point = Yii::$app->params['old_user_invested_level_2_point'];
                    $popType = 4;
                } elseif ($annualInvest >= 50) {
                    $couponArray = Yii::$app->params['old_user_invested_level_3_coupon'];
                    $point = Yii::$app->params['old_user_invested_level_3_point'];
                    $popType = 5;
                }
            } else {
                $couponArray = Yii::$app->params['old_user_not_invested_coupon'];
                $point = 0;
                $popType = 2;
            }
            //发代金券
            $couponTypes = CouponType::findAll(['sn' => $couponArray]);
            foreach ($couponTypes as $couponType) {
                try {
                    UserCoupon::addUserCoupon($user, $couponType)->save();
                } catch (\Exception $ex) {
                    // do nothing.
                }
            }
            if ($point > 0) {
                //发积分
                $record = new PointRecord([
                    'ref_type' => PointRecord::TYPE_POINT_FA_FANG,
                    'incr_points' => $point,
                    'remark' => '老用户升级礼包'
                ]);
                try {
                    PointsService::addUserPoints($record, false, $user);
                } catch (\Exception $ex) {
                    // do nothing.
                }
            }
            //设置弹窗标识,一年后过期
            $redis = Yii::$app->redis;
            $redis->set('oldUserRewardPop_' . $user->id, $popType);
            $redis->expire('oldUserRewardPop_' . $user->id, 365 * 24 * 3600);
        }
    }
}
