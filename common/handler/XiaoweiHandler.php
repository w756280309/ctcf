<?php

namespace common\handler;

use common\jobs\XiaoweiFkStatusJob;
use common\jobs\XiaoweiHkJob;
use common\jobs\XiaoweiHkPlanJob;
use Yii;

class XiaoweiHandler
{
    public static function onFkSuccess($event)
    {
        //添加两个job，使用queue2
        $queue = Yii::$app->queue2;
        //放款状态回调job
        $queue->push(new XiaoweiFkStatusJob([
            'loan' => $event->loan,
        ]));
        //还款计划回调job
        $queue = Yii::$app->queue2;
        $queue->push(new XiaoweiHkPlanJob([
            'loan' => $event->loan,
        ]));
    }

    public static function onHkSuccess($event)
    {
        //还款回调job
        $queue = Yii::$app->queue2;
        $queue->push(new XiaoweiHkJob([
            'loan' => $event->loan,
            'term' => $event->term,
        ]));
    }
}