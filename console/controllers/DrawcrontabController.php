<?php

namespace console\controllers;
use common\models\draw\DrawManager;
use common\models\user\DrawRecord;
use yii\console\Controller;

/**
 * 提现结果通知
 *
 * @author zhanghongyu <zhanghongyu@wangcaigu.com>
 */
class DrawcrontabController extends Controller
{
    /**
     * 确认提现结果通知
     */
    public function actionConfirm()
    {
        $records = DrawRecord::find()->where(['status'=>DrawRecord::STATUS_EXAMINED])->all();
        foreach ($records as $record) {
            DrawManager::commitDraw($record);
        }
    }
}
