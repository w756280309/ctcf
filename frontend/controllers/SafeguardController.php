<?php

namespace frontend\controllers;

use common\controllers\HelpersTrait;
use yii\web\Controller;

class SafeguardController extends Controller
{
    use HelpersTrait;

    public function actionIndex()
    {
        return $this->render("@frontend/views/safeguard.php");
    }
}
