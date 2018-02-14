<?php

namespace wap\modules\promotion\controllers;

use wap\modules\promotion\models\RankingPromo;

class P180212Controller extends BaseController
{
    public $layout = '@app/views/layouts/fe';

    public function actionIndex()
    {
        $promo = $this->findOr404(RankingPromo::class, ['key' => 'promo_180212']);
        $this->registerPromoStatusInView($promo);

        return $this->render('index');
    }
}