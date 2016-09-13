<?php

namespace console\controllers;

use common\models\draw\DrawManager;
use common\models\user\DrawRecord;
use yii\console\Controller;

/**
 * 提现结果通知.
 *
 * @author zhanghongyu <zhanghongyu@wangcaigu.com>
 */
class DrawcrontabController extends Controller
{
    /**
     * 确认提现结果通知.
     */
    public function actionConfirm()
    {
        $records = DrawRecord::find()
            ->where(['lastCronCheckTime' => null])
            ->orWhere(['<', 'lastCronCheckTime', time() - 5 * 60])   //查询间隔为五分钟
            ->andWhere(['status' => DrawRecord::STATUS_EXAMINED])
            ->orderBy(['id' => SORT_DESC])
            ->all();

        foreach ($records as $record) {
            $record->lastCronCheckTime = time();
            DrawManager::commitDraw($record);
        }
    }
}
