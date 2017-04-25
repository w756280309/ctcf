<?php

namespace console\command;


use common\models\queue\QueueTask;
use yii\base\Action;

class ProcessCommand extends Action
{
    public function run($id)
    {
        $queueTask = QueueTask::findOne($id);
        if (is_null($queueTask) || empty($queueTask->runnable)) {
            exit(1);
        }
        $jobClass = $queueTask->runnable;
        $job = new $jobClass(json_decode($queueTask->params, true));
        $job->run();
    }
}