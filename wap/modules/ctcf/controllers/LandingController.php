<?php

namespace wap\modules\ctcf\controllers;

use yii\web\Controller;

class LandingController extends Controller
{
    public $layout = '@app/views/layouts/normal';

    /**
     * 积分商城介绍
     */
    public function actionMall()
    {
        return $this->render('introduct');
    }

    /**
     * 新手首投礼
     */
    public function actionXsInvest()
    {
        return $this->render('xs_invest');
    }
}
