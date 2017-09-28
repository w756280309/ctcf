<?php
namespace console\controllers;

use common\jobs\DingtalkCorpMessageJob;
use yii\console\Controller;

class DingTestController extends Controller
{
    public function actionIndex()
    {
        $job = new DingtalkCorpMessageJob(\Yii::$app->params['ding_notify.user_list.create_note'], '钉钉发送测试，请忽略！谢谢！');
        \Yii::$app->queue->push($job);
    }
}