<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use backend\models\AddAdminForm;

class FrameController extends  \backend\controllers\BaseController
{
    public $layout = 'frame';
    public function actionIndex()
    {
        return $this->render('index');
    }
}
