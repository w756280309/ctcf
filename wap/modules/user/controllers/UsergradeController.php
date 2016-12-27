<?php

namespace app\modules\user\controllers;

use app\controllers\BaseController;


class UsergradeController extends BaseController
{
    /**
     *会员等级
     */
    public function actionIndex()
    {
        $user = $this->getAuthedUser();
        return $this->render('index',['user' => $user]);
    }

    /**
     *如何获得财富值
     */
    public function actionObtaincoins()
    {
        return $this->render('level');
    }

    /**
     *特权说明
     */
    public function actionDetail()
    {
        return $this->render('detail');
    }

}