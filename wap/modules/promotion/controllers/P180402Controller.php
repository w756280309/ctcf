<?php

namespace wap\modules\promotion\controllers;

use wap\modules\promotion\models\RankingPromo;

class P180402Controller extends BaseController
{
    public $layout = '@app/views/layouts/fe';

    public function actionIndex()
    {
        $amount = 0;
        $promo = $this->findOr404(RankingPromo::className(), ['key' => 'promo_180402']);
        $user = $this->getAuthedUser();
        if (null !== $user) {
            $promoClass = new $promo->promoClass($promo);
            $amount = $promoClass->calcUserAmount($user);
        }
        $data['promoStatus'] = $this->getPromoStatus($promo);
        $this->renderJsInView($data);

        return $this->render('index', ['amount' => $amount]);
    }
}
