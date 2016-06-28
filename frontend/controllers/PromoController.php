<?php

namespace frontend\controllers;


class PromoController extends BaseController
{
    public function actionB1(){
        return $this->renderFile('@frontend/views/promo/201606/b1.php');
    }

    public function actionB2() {
        return $this->renderFile('@frontend/views/promo/201606/b2.php');
    }
}