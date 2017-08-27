<?php

namespace wap\modules\promotion\controllers;

use common\models\coupon\CouponType;
use common\models\promo\Award;
use common\models\promo\Fest77;
use common\models\promo\PromoLotteryTicket;
use common\models\promo\PromoService;
use common\models\promo\Reward;
use common\models\promo\TicketToken;
use Composer\Package\Loader\ValidatingArrayLoader;
use wap\modules\promotion\models\RankingPromo;
use common\models\user\UserInfo;
use Yii;

class Fest77InController extends BaseController
{
    public $layout = '@app/views/layouts/fe';

    //七夕活动首页
    public function actionIndex()
    {
        $user = $this->getAuthedUser();
        $status = $this->checkPromo($user);
        //奖励
        $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_170828']);
        $awardList = [];
        $promoClass = new Fest77($promo);
        if (null !== $user) {
            $awardList = $promoClass->getAwardList($user);
        }
        $awardKeys = $this->getThreeRewardKey();
        foreach ($awardList as $k => $award) {
            $awardList[$k]['note'] = in_array($award['sn'], $awardKeys) ? '第三关' : '第一关';
        }
        return $this->render('index', ['user' => $user, 'promo' => $promo, 'status' => $status, 'awardlist' => $awardList]);
    }
    //检查活动状态
    private function checkPromo($user)
    {
        $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_170828']);
        $promoStatus = null;
        try {
            $promo->isActive($user, time());
        } catch (\Exception $e) {
            $promoStatus = $e->getCode();
        }
        if (!is_null($promoStatus)) {
            if ($promoStatus == 1) {
                $status = ['code' => 0, 'message' => '活动未开始'];
            } else {
                $status = ['code' => 2, 'message' => '活动已结束'];
            }
        } else {
            $status = ['code' => 1, 'message' => '活动正常'];
        }
        return $status;
    }

    //redis连接
    private function redisConnect()
    {
        $redis = Yii::$app->redis_session;
        return $redis;
    }
    //redis编辑
    private function redisAdd($user)
    {
        $redis = $this->redisConnect();
        if ($redis->hincrby('qixi', $user->id, 1)) {
            return true;
        }
        return false;
    }

    /**
     * 判断用户是否参加活动
     * 如果用户未参加活动奖状太设置为0
     */
    private function check($user)
    {
        $redis = $this->redisConnect();
        if (!$redis->hexists('qixi', $user->id)) {
            $redis->hset('qixi', $user->id, 0);
            $redis->expire('qixi', 7 * 24 * 3600);
        }
        return $redis->hget('qixi', $user->id);
    }

    /**
     * 第一关 ---  答题
     * 限制条件：（1）最多答题两次 （2）必须分享一次才可以第二次答题
     */
    public function actionFirst()
    {
        $user = $this->getAuthedUser();
        $res = null;
        $coupon = null;
        if (!is_null($user)) {  //登录后判断是否可以参加活动
            $res = $this->check($user);
        }
        if ($res == 1 || $res == 3) {   //查获得的代金券
            $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_170828']);
            $award = Award::find()->where(['user_id' => $user->id, 'promo_id' => $promo->id, 'ref_type' => 'coupon'])
                    ->orderBy(['createTime' => SORT_DESC])->one();
            if (!is_null($award)) {
                $gift = Reward::findOne($award->reward_id);
            }
            if (!is_null($gift)) {
                $coupon = CouponType::findOne($gift->ref_id);
            }
        }
        return $this->render('first', ['status' => $res, 'coupon' => $coupon]);
    }

    public function actionAnswer()
    {
        $user = $this->getAuthedUser();
        if (!is_null($user)) {
            $res = $this->check($user);
            if ($res == 2 || $res == 0) {  //答题次数最多2
                if ($this->redisAdd($user)) {
                    $this->reward($user);
                    return ['code' => 1, 'message' => '答题成功'];
                } else {
                    return ['code' => 0, 'message' => '答题失败'];
                }
            }
            return ['code' => 0, 'message' => '答题失败'];
        }
        return ['code' => 0, 'message' => '答题失败'];
    }
    /**
     * 发放奖励
     * 每次答题成功后发放奖励
     */
    private function reward($user)
    {
        $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_170828']);
        if ($user->created_at < (time() - 3600 * 24 * 30)) {    //老用户
            $key = Reward::draw(['170828_lc_10' => '0.5', '170828_lc_20' => '0.2', '170828_lc_50' => '0.3']);
        } else {    //一个月内
            $key = Reward::draw(['170828_sc_10' => '0.3', '170828_sc_20' => '0.5', '170828_sc_30' => '0.2']);
        }
        $reward = Reward::fetchOneBySn($key);
        if (!is_null($promo) && !is_null($user) && !is_null($reward)) {
            PromoService::award($user, $reward, $promo);
            return $reward;
        }
    }

    /**
     * 分享好友
     * 条件：必须答题一次才可以分享
     */
    public function actionShare()
    {
        $user = $this->getAuthedUser();
        if (!is_null($user) && $this->check($user) == 1) {
            $this->redisAdd($user);
            return ['code' => 1, 'message' => '分享成功'];
        }
        return ['code' => 0, 'message' => '分享失败'];
    }

    /**
     * 第二关
     * 获取用户累计年化
     */
    public function actionSecond()
    {
        $user = $this->getAuthedUser();
        $sum = null;
        $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_170828']);
        if (!is_null($user)) {
            $sum = UserInfo::calcAnnualInvest($user->id, $promo->startTime, $promo->endTime);
        }
        $status = $this->checkPromo($user);
        return $this->render('second',['sum' => $sum, 'status' => $status]);
    }

    /**
     * 第三关
     * 首投，送抽奖机会
     */
    public function actionThird()
    {
        $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_170828']);
        $user = $this->getAuthedUser();
        try {
            $promo->isActive($user);
        } catch (\Exception $ex) {
            return $this->redirect('index');
        }
        $awardList = [];
        $waitAwarded = false;
        $promoClass = new Fest77($promo);
        if (null !== $user) {
            $awardList = $promoClass->getAwardList($user);
            $waitAwarded = null !== PromoLotteryTicket::fetchOneActiveTicket($promo, $user);
            $awardKeys = $this->getThreeRewardKey();
            foreach ($awardList as $k => $award) {
                $awardList[$k]['note'] = in_array($award['sn'], $awardKeys) ? '第三关' : '第一关';
            }
        }

        //获取当前是否已经领取
        return $this->render('third', [
            'user' => $user,
            'awardList' => $awardList,
            'waitAwarded' => $waitAwarded,
            'isFinishedThree' => $this->getFinishedThree($awardList),
        ]);
    }

    private function getFinishedThree($awardList)
    {
        $rewardKeys = $this->getThreeRewardKey();

        foreach ($awardList as $award) {
            if (in_array($award['sn'], $rewardKeys)) {
                return true;
            }
        }

        return false;
    }

    private function getThreeRewardKey()
    {
        return [
            '170828_coupon_20',
            '170828_coupon_50',
            '170828_card_50',
            '170828_fare_50',
            '170828_scales',
            '170828_p_520',
            '170828_p_77',
            '170828_cash_77',
        ];
    }

    /**
     * 第三关送固定奖励：7.7现金红包
     */
    public function actionAwardThree()
    {
        $user = $this->getAuthedUser();
        //活动状态
        $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_170828']);
        try {
            $promo->isActive($user);
        } catch (\Exception $ex) {
            $code = $ex->getCode();
            if (in_array($code, [1, 2])) {
                return [
                    'code' => $code,
                    'message' => $ex->getMessage(),
                ];
            }
        }
        if (null === $user) {
            return [
                'code' => 3,
                'message' => '您还未登录！',
            ];
        }

        $promoClass = new Fest77($promo);
        $list = $promoClass->getAwardList($user);
        if (!$this->getFinishedThree($list)) {
            return [
                'code' => 4,
                'message' => '您还没有完成第三关！',
            ];
        }

        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $key = $promo->id . '-' . $user->id . '-cash-7.7';
            TicketToken::initNew($key)->save(false);
            $reward = Reward::fetchOneBySn('170828_cash_77');
            PromoService::award($user, $reward, $promo);
            $transaction->commit();
        } catch (\Exception $ex) {
            $transaction->rollBack();
            return [
                'code' => 5,
                'message' => $ex->getMessage(),
            ];
        }

        return [
            'code' => 0,
            'message' => '成功',
        ];
    }
}
