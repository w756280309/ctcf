<?php

namespace wap\modules\promotion\controllers;

use yii\web\Controller;

class LandingController extends Controller
{
    public function actionHomecoming17()
    {
        return $this->render('homecoming17');
    }

    public function actionC180214()
    {
        $this->layout = '@app/views/layouts/fe';

        return $this->render('c180214_shuangbeijifen');
    }
}
