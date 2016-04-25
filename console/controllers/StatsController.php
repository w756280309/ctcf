<?php

namespace console\controllers;

use common\models\LenderStats;
use yii\console\Controller;

class StatsController extends Controller
{
    /**
     * 更新投资用户统计表
     */
    public function actionUpdate()
    {
        LenderStats::updateData();
    }
}