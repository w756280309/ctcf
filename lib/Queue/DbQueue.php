<?php

namespace Queue;


use common\models\queue\Job;
use common\models\queue\QueueTask;

/**
 * 数据库队列
 *
 * Class DbQueue
 * @package Queue
 */
class DbQueue
{
    public function pub(Job $job, $weight = 1)
    {
        $model = QueueTask::initNew($job, $weight);
        return $model->save(false);
    }

    //获取需要运行的队列
    public function fetchQueueTask($length = 5)
    {
        if ($length <= 0) {
            return [];
        }

        return QueueTask::find()
            ->where(['nextRunTime' => null])
            ->orWhere(['<', 'nextRunTime', date('Y-m-d H:i:s')])
            ->andWhere(['status' => QueueTask::STATUS_PENDING])
            ->andWhere('runCount < runLimit')
            ->orderBy(['weight' => SORT_DESC, 'createTime' => SORT_ASC, 'id' => SORT_ASC])
            ->limit($length)
            ->all();
    }

    //将消息标记为等待处理
    public function markPending(QueueTask $queueTask)
    {
        $queueTask->status = QueueTask::STATUS_PENDING;
        $queueTask->save();
    }

    //将消息标记为处理中
    public function markRunning(QueueTask $queueTask)
    {
        $queueTask->status = QueueTask::STATUS_RUNNING;
        $queueTask->save();
    }

    //将消息标记为成功
    public function markSuccess(QueueTask $queueTask)
    {
        $dateTime = date('Y-m-d H:i:s');
        $queueTask->finishTime = $dateTime;
        $queueTask->status = QueueTask::STATUS_SUCCESS;
        RETURN $queueTask->save();
    }

    //将消息标记为失败
    public function markFail(QueueTask $queueTask)
    {
        $queueTask->status = QueueTask::STATUS_FAIL;
        return $queueTask->save();
    }
}