<?php
namespace backend\modules\datatj\controllers;

use yii\web\Controller;

class DefaultController extends \backend\controllers\BaseController
{
    public $layout = 'frame';
    public function actionIndex()
    {
        return $this->render('index');
    }
}
