<?php

namespace console\command;

use common\models\queue\QueueTask;
use yii\base\Action;

/**
 * 消息队列处理程序公共父类
 *
 * Class BaseCommand
 * @package console\command
 */
abstract class BaseCommand extends Action
{
    public function run($base64EncodeData, $taskSn)
    {
        $task = QueueTask::findOne(['sn' => $taskSn]);
        if (is_null($task)) {
            exit(1);
        }
        $this->updateNextRunTime($task);//更新下次执行时间
        $data = json_decode(base64_decode($base64EncodeData), true);
        $res = $this->doMyJob($data);
        if ($res) {
            exit(0);
        }

        exit(1);
    }

    /**
     * 队列处理程序
     *
     * todo 不同进程处理逻辑需要重载这个方法
     *
     * @param array $data   消息队列命令附带参数
     * @return bool
     */
    abstract public function doMyJob($data);

    /**
     * 获取执行次数和下次执行时间的关系
     *
     * todo 不同子进程需要重载此方法，用来配置执行次数和下次执行时间之间关系
     *
     * @return array
     */
    public function nextRunTimeConfig()
    {
        return [
            10 => 3 * 60 * 60,//已经运行过10次，下次运行时间要 3 * 60 * 60 秒之后
            8 => 60 * 60,
            6 => 30 * 60,
            4 => 5 * 60,
            2 => 60,
        ];
    }

    /**
     * 更新下次执行时间
     *
     * @param QueueTask $task
     */
    public function updateNextRunTime(QueueTask $task)
    {
        foreach ($this->nextRunTimeConfig() as $count => $period) {
            if ($task->runCount >= $count) {
                $lastRunAt = empty($task->lastRunTime) ? time() : strtotime($task->lastRunTime);
                $task->nextRunTime = date('Y-m-d H:i:s', $lastRunAt + $period);
                $task->save();
                break;
            }
        }
    }
}