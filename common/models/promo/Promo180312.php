<?php

namespace common\models\promo;

use Yii;
use common\models\order\OnlineOrder;

class Promo180312 extends BasePromo
{
    private $annualInvestLimit = 10000;

    /**
     * 判断该用户是否已经领取每天免费的浇水次数
     * @param $user
     * @param $source  浇水来源-free（每天免费获取）
     * @return bool true为已获取，false为未获取
     */
    public function receive($user, $source)
    {
        $result = PromoLotteryTicket::find()
            ->where(['promo_id' => $this->promo->id])
            ->andWhere(['user_id' => $user->id])
            ->andWhere(['source' => $source])
            ->andWhere(["date_format(from_unixtime(created_at), '%Y-%m-%d')" => date('Y-m-d', time())])
            ->one();
        return !is_null($result) ? true : false;
    }

    /**
     * 判断用户是否已经分享朋友圈
     * @param $user
     * @param $source  浇水来源-share-（活动期间分享一次）
     * @return bool true为已分享，false为未分享
     */

    public function share($user, $source)
    {
        $startDate = (new \DateTime($this->promo->startTime))->format('Y-m-d');
        $endDate = (new \DateTime($this->promo->endTime))->format('Y-m-d');
        $result = PromoLotteryTicket::find()
            ->where(['promo_id' => $this->promo->id])
            ->andWhere(['user_id' => $user->id])
            ->andWhere(['source' => $source])
            ->andWhere("date_format(from_unixtime(created_at), '%Y-%m-%d') >=" . $startDate)
            ->andWhere("date_format(from_unixtime(created_at), '%Y-%m-%d') >=" . $endDate)
            ->count();
        return $result >= 1 ? true : false;
    }

    /**
     * 根据用户id保存最后一次的浇水次数id到redis中
     * @param $user
     * @param $id   浇水次数的id,即promo_lottery_ticket的id
     */
    public function setRedis($user, $id)
    {
        $redis = Yii::$app->redis;
        $redis->hset('lastPromoTicketId', $user->id, $id);
        $redis->expire('lastPromoTicketId', 8 * 24 * 3600);
    }

    /**
     * 根据用户及奖id判断将奖品是否已经被领取
     * @param $user
     * @param $rewardId  奖品id，即reward表的id
     * @return bool 已领取为true,未领取为false
     */
    public function awardStatus($user, $rewardId)
    {
        $result = Award::find()
            ->where(['user_id' => $user->id])
            ->andWhere(['promo_id' => $this->promo->id])
            ->andWhere(['reward_id' => $rewardId])
            ->one();
        return !is_null($result) ? true :false;
    }

    /**
     * 订单成功后执行方法
     *
     * @param OnlineOrder $order 订单对象
     *
     * @throws \Exception
     */
    public function doAfterSuccessLoanOrder($order)
    {
        //每1万年化增加一次浇水机会
        $user = $order->onlineUser;
        //根据当前累计年化金额给用户添加抽奖机会。
        $annualAmount = $this->calcUserAmount($user);
        $this->sendTicketsByConfig($user, $annualAmount, $this->annualInvestLimit);
    }
}
