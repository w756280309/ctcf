<?php

namespace common\models\queue;

use Yii;

/**
 * 队列
 *
 * This is the model class for table "queue_task".
 *
 * @property integer    $id
 * @property string     $runnable       job名称，默认存类名
 * @property string     $params         参数，默认存json
 * @property integer    $status         0，等待运行;1，处理中；2，处理失败；9，处理成功
 * @property integer    $weight         权重，默认1
 * @property integer    $runCount       已处理次数
 * @property string     $lastRunTime    上次处理时间
 * @property string     $createTime     消息创建时间
 * @property string     $finishTime     消息结束时间
 * @property integer    $runLimit       最大尝试运行次数
 * @property string     $nextRunTime    下次运行时间
 */
class QueueTask extends \yii\db\ActiveRecord
{
    const STATUS_PENDING = 0;   //等待运行
    const STATUS_RUNNING = 1;   //处理中，消息被占用
    const STATUS_FAIL = 2;      //处理失败
    const STATUS_SUCCESS = 9;   //处理成功

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'queue_task';
    }

    public static function initNew(Job $job, $weight = 1, $runLimit = 1)
    {
        $queueTask = new self([
            'runnable' => get_class($job),
            'params' => json_encode($job->getParams()),
            'weight' => $weight,
            'createTime' => date('Y-m-d H:i:s'),
            'status' => self::STATUS_PENDING,
            'runLimit' => $runLimit,
        ]);

        return $queueTask;
    }
}
