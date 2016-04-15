<?php

namespace backend\modules\system\controllers;

class DefaultController extends \backend\controllers\BaseController
{
    public $layout = 'frame';
    public function actionIndex()
    {
        return $this->render('index');
    }
}
