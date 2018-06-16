<?php

namespace wap\modules\promotion\controllers;

use wap\modules\promotion\models\RankingPromo;

class P180618Controller extends BaseController
{
    public $layout = '@app/views/layouts/fe';
    /**
     *  温都端午节初始化页面
     *  promoStatus 判断活动状态：活动未开始1,活动已结束2,活动进行中：0
     * isLoggedIn 判断登录状态，已登录：true,未登录：false
     */
    public function actionIndex()
    {
        $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_180618']);
        $user = $this->getAuthedUser();
        $userAnnualInvest = 0;
        if ($user) {
            $promoClass = new $promo->promoClass($promo);
            $userAnnualInvest = $promoClass->calcUserAmount($user);
        }
        $data = [
            'isLoggedIn' => null !== $user,
            'promoStatus' => $this->getPromoStatus($promo),
            'userAnnualInvest' => $userAnnualInvest,
        ];
        $this->renderJsInView($data);

        return $this->render('index');
    }
}
