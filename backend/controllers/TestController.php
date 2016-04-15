<?php
/**
 * Created by PhpStorm.
 * User: yang
 * Date: 2016/4/15
 * Time: 8:56
 */

namespace backend\controllers;


use common\lib\user\UserStats;
use common\models\LenderStats;
use yii\web\Controller;

class TestController extends Controller
{
    public function actionUpdate()
    {
        LenderStats::updateData();
    }

    public function actionNewExport()
    {
        LenderStats::createCsvFile();
    }

    public function actionOldExport()
    {
        $data = UserStats::collectLenderData();
        UserStats::createCsvFile($data);
    }
}