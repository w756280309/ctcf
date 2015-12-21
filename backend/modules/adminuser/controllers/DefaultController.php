<?php

namespace app\modules\adminuser\controllers;


use yii\web\Controller;

class DefaultController extends \backend\controllers\BaseController
{
    public $layout = 'frame';
    public function actionIndex()
    {
//        if (\Yii::$app->user->isGuest) {
//            return $this->goHome();
//        }
        return $this->render('index');
    }
}
