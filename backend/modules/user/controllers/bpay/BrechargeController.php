<?php

namespace backend\modules\user\controllers\bpay;


use yii\web\Controller;

class BrechargeController extends Controller
{
    public function actionFrontendNotify()
    {
        return $this->redirect('/tool/index');
    }
}