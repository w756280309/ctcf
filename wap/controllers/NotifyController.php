<?php

namespace app\controllers;

use common\models\NotifyLog;
use yii\web\Controller;

class NotifyController extends Controller
{
    public function actionReceive()
    {
        $query = \Yii::$app->request->getQueryString();

        $log = new NotifyLog(['query' => $query]);
        $log->save();
    }

    public function actionList()
    {
    }

    public function actionRelay($id)
    {
    }
}
