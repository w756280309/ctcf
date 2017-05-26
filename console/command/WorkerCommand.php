<?php

namespace console\command;


use Symfony\Component\Process\Process;
use yii\base\Action;

class WorkerCommand extends Action
{
    const PROCESS_CHECK_SPACE = 300000;//子进程状态查询间隔, 单位为microseconds
    const PROCESS_INTERVAL = 2000;//进程开启间隔时间, 单位为microseconds
    const MAX_PROCESS_COUNT = 10;//最大子进程数量
    const MAIN_PROCESS_MIN_RUNNING_TIME = 3600;//主进程最低运行时间, 单位为秒 3600
    const MAIN_PROCESS_MAX_RUNNING_TIME = 7200;//主进程最多运行时间，单位为秒，超过之后不再接收新任务 7200


    private $queue;//队列服务

    private $totalRunProcessCount = 0;//累计运行多少个任务
    private $totalSuccessProcessCount = 0;//累计成功进程个数
    private $totalFailProcessCount = 0;//累计异常进程个数

    private $lastRunAt;//主进程上次运行时间, 时间戳

    private $mainProcessStartAt;//主进程开始时间, 时间戳
    private $mainProcessEndAt = 0;//主进程结束时间，时间戳

    private $taskList = [];//当前处理着的消息列表
    private $processList = [];//当前运行着的进程
    private $runningProcessCount = 0;//运行着的进程数量

    private $stopSignal = false;//收到退出信号

    public function beforeRun()
    {
        \Yii::info('开始运行队列主进程', 'queue');
        $this->queue = \Yii::$container->get('db_queue');
        $this->mainProcessStartAt = time();

        return true;
    }

    public function run()
    {
        $this->setSignalHandler();
        //判断主进程是否应该运行
        while ($this->needToRun()) {
            //启动所有子进程
            $this->runTasks();

            //监控所有子进程
            $this->monitorProcesses();

            \Yii::info('更新主进程的最新运行时间并准备再次尝试运行' . PHP_EOL, 'queue');
            $this->lastRunAt = time();//更新主进程上次运行时间
            usleep(self::PROCESS_CHECK_SPACE);//休息
        }

        exit(0);
    }

    private function signalHandler($signalNo)
    {
        switch ($signalNo) {
            case SIGTERM:
                // 处理kill
                $this->stopSignal = true;
                break;
            case SIGHUP:
                //处理SIGHUP信号
                break;
            case SIGINT:
                //处理ctrl+c
                $this->stopSignal = true;
                break;
            default:
        }
    }

    private function setSignalHandler()
    {
        declare(ticks = 1);
        pcntl_signal(SIGTERM, [$this, "signalHandler"]);
        pcntl_signal(SIGHUP, [$this, "signalHandler"]);
        pcntl_signal(SIGINT, [$this, "signalHandler"]);
    }

    //更新运行着的消息列表
    private function updateTaskList()
    {
        $availableProcessCount = self::MAX_PROCESS_COUNT - $this->runningProcessCount;
        if ($availableProcessCount > 0) {
            \Yii::info('需要补充[' . $availableProcessCount . ']个消息', 'queue');
            $taskList = $this->queue->fetchQueueTask($availableProcessCount);
            if (!empty($taskList)) {
                \Yii::info('已获取[' . count($taskList) . ']个消息', 'queue');
                foreach ($taskList as $task) {
                    \Yii::info('正在将消息标记为处理中并将该消息添加到处理队列中, 消息ID[' . $task->id . ']', 'queue');
                    $this->queue->markRunning($task);//将消息标记为处理中
                    array_push($this->taskList, $task);//更新消息队列
                }
            }
        }
    }

    //判断主进程是否该运行着
    private function needToRun()
    {
        \Yii::info('判断主进程是否应该继续运行', 'queue');
        $runningTime = time() - $this->mainProcessStartAt;
        \Yii::info('当前有[' . $this->runningProcessCount . ']个子进程正在运行;主进程已经运行[' . $runningTime . ']秒.', 'queue');
        if (
            $runningTime <= self::MAIN_PROCESS_MAX_RUNNING_TIME//超过主进程最大运行时间之后就不再接收新任务
            && !$this->stopSignal//收到结束信号后不再接收新任务
        ) {
            $this->updateTaskList();//更新需要处理的消息列表
        }
        if ($runningTime > self::MAIN_PROCESS_MAX_RUNNING_TIME) {
            \Yii::info('主进程运行超过最大运行时间[' . self::MAIN_PROCESS_MAX_RUNNING_TIME . ']秒, 不再接收新任务.', 'queue');
        }
        if ($this->stopSignal) {
            \Yii::info('收到结束信号, 不再接收新任务.', 'queue');
        }

        if (
            !empty($this->taskList) //工作队列不为空
            || (
                $runningTime <= self::MAIN_PROCESS_MIN_RUNNING_TIME //没达到最低运行时间
                && !$this->stopSignal//没有收到结束信号
            )
        ) {
            return true;
        } else {
            return false;
        }
    }

    //运行所有需要运行的子进程
    private function runTasks()
    {
        if (!empty($this->taskList) && count($this->processList) < count($this->taskList)) {
            \Yii::info('为当前每个待处理的消息['.count($this->taskList).'个]分配子进程', 'queue');
            foreach ($this->taskList as $num => $task) {
                //进程没有被开启过
                if (!isset($this->processList[$num])) {
                    $this->runningProcessCount++;//增加正在运行着的进程数
                    $this->totalRunProcessCount++;//增加累计运行进程数

                    $task->runCount++;
                    $task->lastRunTime = date('Y-m-d H:i:s');
                    $task->save();

                    $process = new Process('php ' . __DIR__ . '/../../yii queue/process ' . $task->id);
                    $this->processList[$num] = $process;
                    $process->start();
                    \Yii::info('num为[' . $num . '] ID为[' . $task->id . ']的消息的子进程编号为[' . $num . ']', 'queue');

                    usleep(self::PROCESS_INTERVAL);
                }
            }
        }
    }

    //监控所有子进程
    private function monitorProcesses()
    {
        if (!empty($this->processList)) {
            \Yii::info('正在监控所有运行着的子进程[' . count($this->processList) . '个]', 'queue');
            foreach ($this->processList as $num => $process) {
                if ($process->isRunning()) {
                    \Yii::info('num为[' . $num . ']的子进程正在运行', 'queue');
                    $outPut = $process->getIncrementalOutput();
                    if (!empty($outPut)) {
                        echo $outPut . PHP_EOL;
                    }
                } else {
                    $this->updateTask($num);//更新队列状态
                }
            }
        }
    }

    //更新消息状态
    private function updateTask($num)
    {
        $task = $this->taskList[$num];
        $process = $this->processList[$num];

        if ($process->isSuccessful()) {
            \Yii::info('num为[' . $num . ']的子进程运行成功', 'queue');
            $this->queue->markSuccess($task);
            $this->totalSuccessProcessCount++;//增加总运行成功进程数
        } else {
            if ($task->runCount < $task->runLimit) {
                \Yii::info('num为[' . $num . ']的子进程运行失败，但是只运行了' . $task->runCount . '次，最多可运行' . $task->runLimit . '此，此消息将继续运行, 下次运行时间为' . $task->nextRunTime, 'queue');
                $this->queue->markPending($task);
            } else {
                \Yii::info('num为[' . $num . ']的子进程运行失败，运行了' . $task->runCount . '次，最多可运行' . $task->runLimit . '此', 'queue');
                $this->queue->markFail($task);
                $this->totalFailProcessCount++;//增加总运行失败进程数
            }
        }

        $this->runningProcessCount--;//更新正在运行着的进程数
        unset($this->processList[$num]);
        unset($this->taskList[$num]);
    }

    public function afterRun()
    {
        $this->mainProcessEndAt = time();

        //todo 保存本次主进程运行数据
    }
}