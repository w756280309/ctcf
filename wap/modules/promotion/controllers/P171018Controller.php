<?php

namespace wap\modules\promotion\controllers;

use wap\modules\promotion\models\RankingPromo;

class P171018Controller extends BaseController
{
    public $layout = '@app/views/layouts/fe';

    public function actionIndex()
    {
        //获得活动及当前登录用户
        $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_171018']);
        $user = $this->getAuthedUser();

        //获得当前用户在活动期间的累计年化及将promo_status渲染到前端页面
        $totalMoney = $this->getPromoAnnualInvest($promo, $user);
        $this->registerPromoStatusInView($promo);

        return $this->render('index', [
            'totalMoney' => rtrim(rtrim(bcdiv($totalMoney, 10000, 2), '0'), '.'),
        ]);
    }
}