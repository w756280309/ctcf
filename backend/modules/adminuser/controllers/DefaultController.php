<?php

namespace app\modules\adminuser\controllers;

class DefaultController extends \backend\controllers\BaseController
{
    public $layout = 'frame';
    public function actionIndex()
    {
        return $this->render('index');
    }
}
