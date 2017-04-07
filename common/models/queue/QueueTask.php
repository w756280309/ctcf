<?php

namespace common\models\queue;

use Yii;

/**
 * 队列
 *
 * This is the model class for table "queue_task".
 *
 * @property integer    $id
 * @property string     $sn             每个消息核心参数(时间取毫秒)进行哈希，保证没有重复消息
 * @property string     $topic          消息标签
 * @property string     $command        消息指令, 如果需要参数，要带参数，如 "php yii math/add 1 1"
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

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'weight', 'runCount', 'runLimit'], 'integer'],
            [['lastRunTime', 'createTime', 'finishTime', 'nextRunTime'], 'safe'],
            [['sn'], 'string', 'max' => 255],
            [['command'], 'string'],
            [['topic'], 'string', 'max' => 32],
            ['sn', 'unique'],
        ];
    }

    public static function createNewTask($topic, $command, $runLimit = 20, $weight = 1)
    {
        $queueTask = new self([
            'topic' => $topic,
            'command' => $command,
            'weight' => $weight,
            'createTime' => date('Y-m-d H:i:s'),
            'status' => self::STATUS_PENDING,
            'runLimit' => $runLimit,
        ]);
        $queueTask->sn = md5(json_encode([
            'time' => microtime(true),
            'topic' => $queueTask->topic,
            'command' => $queueTask->command,
            'createTime' => $queueTask->createTime,
        ]));

        return $queueTask;
    }

    public function getTopic()
    {
        return $this->getAttribute('topic');
    }

    public function getCommand()
    {
        return $this->getAttribute('command');
    }

    public function getSn()
    {
        return $this->getAttribute('sn');
    }
}
