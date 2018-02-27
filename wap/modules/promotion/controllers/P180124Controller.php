<?php

namespace wap\modules\promotion\controllers;

use wap\modules\promotion\models\RankingPromo;

class P180124Controller extends BaseController
{
    public $layout = '@app/views/layouts/fe';

    public function actionIndex()
    {
        $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_180124']);
        $this->registerPromoStatusInView($promo);

        return $this->render('index');
    }
}