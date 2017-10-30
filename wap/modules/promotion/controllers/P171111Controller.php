<?php

namespace wap\modules\promotion\controllers;

use wap\modules\promotion\models\RankingPromo;
class P171111Controller extends BaseController
{
    public $layout = '@app/views/layouts/fe';

    /**
     * 活动总览页
     */
    public function actionTotal()
    {
        $promos = RankingPromo::find()
            ->where(['in', 'key', $this->getAllPromoKey()])
            ->orderBy(['endTime' => SORT_ASC])
            ->all();
        //活动配置不足，直接抛404
        if (count($promos) < 3) {
            throw $this->ex404();
        }
        $promoArr = [
            'promoStatus1' => 0,
            'promoStatus2' => 0,
            'promoStatus3' => 0,
            'activeTicketCount' => 0,
        ];

        //初始化3个分活动的活动状态
        foreach ($promos as $k => $promo) {
            $statusKey = 'promoStatus'.($k+1);
            $promoArr[$statusKey] = $this->getPromoStatus($promo);
        }

        //获取活动的有效抽奖机会
        $promoClass = new $promo->promoClass($promo);
        if (null !== ($user = $this->getAuthedUser())) {
            $promoArr['activeTicketCount'] = $promoClass->getActiveTicketCount($user);
        }

        return $this->render('total', $promoArr);
    }

    private function getAllPromoKey()
    {
        return [
            'promo_171103',
            'promo_171108',
            'promo_171111',
        ];
    }

    /**
     * 分活动1
     */
    public function actionFirst()
    {
        $data = [
            'inviteTask' => 0,
            'investTask' => 0,
        ];

        $user = $this->getAuthedUser();
        if (null !== $user) {
            $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_171103']);
            $promoClass = new $promo->promoClass($promo);
            $data = $promoClass->getPromoTaskStatus($user);
        }

        return $this->render('first', $data);
    }
}
