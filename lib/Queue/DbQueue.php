<?php

namespace Queue;


use common\models\queue\QueueTask;

/**
 * 数据库队列
 *
 * 注意：
 * 1. 队列的command命令带参数，参数需要使用base64编码。如需要用yii的queue/identity-notify 命令处理开户失败通知消息， 那么 command = "queue/identity-notify ".base64_encode(json_encode(['realName' => '姓名', 'idCard' => '身份证', 'userId' => '用户ID', 'message' => '错误消息']))
 *
 *
 * Class DbQueue
 * @package Queue
 */
class DbQueue
{
    public function push(QueueTask $queueTask)
    {
        return $queueTask->save();
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