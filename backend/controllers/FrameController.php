<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;

class FrameController extends  \backend\controllers\BaseController
{
    public $layout = 'frame';
    public function actionIndex()
    {
        return $this->render('index');
    }
}
