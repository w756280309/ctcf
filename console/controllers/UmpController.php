<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;

class UmpController extends Controller
{
    public function actionUserInfo($umpUserId)
    {
        $resp = Yii::$container->get('ump')->getUserInfo($umpUserId);

        var_dump($resp);
    }
}
