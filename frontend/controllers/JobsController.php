<?php

namespace frontend\controllers;

use common\controllers\HelpersTrait;
use yii\web\Controller;

class JobsController extends Controller
{
    use HelpersTrait;

    public function actionIndex()
    {
        return $this->render('@frontend/views/jobs.php');
    }
}
