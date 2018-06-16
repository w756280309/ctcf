<?php

namespace wap\modules\ctcf\controllers;

use common\models\promo\PromoLotteryTicket;
use wap\modules\promotion\models\RankingPromo;
use Yii;

class P180618Controller extends BaseController
{
    public $layout = '@app/views/layouts/fe';
    /**
     *  楚天端午节初始化页面
     *  promoStatus 判断活动状态：活动未开始1,活动已结束2,活动进行中：0
     * isLoggedIn 判断登录状态，已登录：true,未登录：false
     * isInvested: 活动期间是否有过一次投资
     */
    public function actionIndex()
    {
        $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_1806181']);
        $user = $this->getAuthedUser();
        $userAnnualInvest = 0;
        $isInvested = false;
        if ($user) {
            $promoClass = new $promo->promoClass($promo);
            $userAnnualInvest = $promoClass->calcUserAmount($user);
            $isInvested = null !== PromoLotteryTicket::fetchOneActiveTicket($promo, $user);
        }
        $data = [
            'isLoggedIn' => null !== $user,
            'promoStatus' => $this->getPromoStatus($promo),
            'userAnnualInvest' => $userAnnualInvest,
            'isInvested' => $isInvested,
        ];

        $this->renderJsInView($data);

        return $this->render('index');
    }
}
