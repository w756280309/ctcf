<?php

namespace wap\modules\promotion\controllers;

use common\models\user\UserInfo;
use wap\modules\promotion\models\RankingPromo;

class P170919Controller extends BaseController
{
    public $layout = '@app/views/layouts/fe';

    public function actionIndex()
    {
        $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_170919']);
        $promoStatus = $this->getPromoStatus($promo);
        $totalAnnual = 0;
        $user = $this->getAuthedUser();
        if (null !== $user) {
            $startDate = (new \DateTime($promo->startTime))->format('Y-m-d');
            $endDate = (new \DateTime($promo->endTime))->format('Y-m-d');
            $totalAnnual = UserInfo::calcAnnualInvest($user->id, $startDate, $endDate);
        }
        $this->view->params['promoStatus'] = $promoStatus;
        return $this->render('index', [
            'totalAnnual' => $totalAnnual,
        ]);
    }
}
