<?php

namespace frontend\controllers;

use common\controllers\HelpersTrait;
use yii\web\Controller;

class Page404Controller extends Controller
{
    use HelpersTrait;

    public function actionIndex()
    {
        $this->layout = false;
        $exception = \Yii::$app->errorHandler->exception;
        if ($exception !== null) {
            if (404 === $exception->statusCode) {
                return $this->render("@frontend/views/404.php");
            }
        }
        return $this->render("@frontend/views/error.php");
    }
}