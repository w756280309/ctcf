<?php

namespace wap\modules\promotion\controllers;

use wap\modules\promotion\models\RankingPromo;

class PromoPointsController extends BaseController
{
    public $layout = '@app/views/layouts/fe';

    public function actionP171123()
    {
        $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_171123']);
        $user = $this->getAuthedUser();
        $totalMoney = $this->getPromoAnnualInvest($promo, $user);
        $this->registerPromoStatusInView($promo);

        return $this->render('index171123', [
            'totalMoney' => rtrim(rtrim(bcdiv($totalMoney, 10000, 2), '0'), '.'),
        ]);
    }
}