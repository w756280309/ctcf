<?php

namespace Xii\Crm\Command;


use common\utils\ExcelUtils;
use Xii\Crm\Model\Activity;
use Xii\Crm\Model\Engagement;
use Yii;
use yii\console\Controller;

class EngagementController extends Controller
{
    /**
     * 导入客服记录
     *
     * 1. 文件名为  'phone_call.xlsx', 文件位于 WDJF/console/runtime, 导入完成之后需要手工清理
     * 2. 输出成功条数、失败条数
     * 3. 记录失败数据到 'log_phone_call.txt' 中，记录失败电话及失败信息，文件位于 WDJF/console/runtime, 修复完成之后需要手工清理
     * 4. 限制导入10000行，真实数据7700行
     */
    public function actionCall()
    {
        $path = Yii::$app->getRuntimePath();
        $dataFile = $path . '/phone_call.xlsx';
        $logFile = $path . '/log_phone_call.txt';

        if (!file_exists($dataFile)) {
            throw new \Exception('文件　'.$dataFile.' 不存在');
        }

        $data = ExcelUtils::readExcelToArray($dataFile, 'G', 10000);
        if (empty($data)) {
            throw new \Exception('没有找到合适数据');
        }
        $count = count($data);
        $successCount = $errorCount = 0;
        $creatorId = 11;//孙雪瑞
        foreach ($data as $item) {
            if (!empty($item)) {
                list($day, $time, $name, $number, $duration, $content, $summary) = $item;
                $duration = intval($duration);//通话时长以分为单位
                $engagement = new Engagement([
                    'number' => $number,
                    'duration' => $duration,
                    'callTime' => $day . ' ' . $time,
                    'direction' => Engagement::TYPE_IN,
                    'callerName' => $name,
                    'content' => $content,
                    'summary' => $summary,
                    'creator_id' => $creatorId,
                    'activityType' => Activity::TYPE_PHONE_CALL,
                ]);

                if ($engagement->validate(['number']) && $engagement->addCall()) {
                    $successCount ++;
                } else {
                    file_put_contents($logFile, $number . ' | '.(current($engagement->getFirstErrors())) . PHP_EOL . json_encode($item) . PHP_EOL, FILE_APPEND);
                    $errorCount++;
                }
            }
        }
        $this->stdout("记录 $count , 成功记录　$successCount, 失败记录 $errorCount" . PHP_EOL);
    }

    /**
     * 导入门店客户接待记录
     *
     * 1. 文件名为  'engagement.xlsx', 文件位于 WDJF/console/runtime, 导入完成之后需要手工清理
     * 2. 输出成功条数、失败条数
     * 3. 记录失败数据到 'engagement.txt' 中，记录失败电话及失败信息，文件位于 WDJF/console/runtime, 修复完成之后需要手工清理
     * 4. 真实数据221行
     */
    public function actionRec()
    {
        $path = Yii::$app->getRuntimePath();
        $dataFile = $path . '/engagement.xlsx';
        $logFile = $path . '/engagement.txt';

        if (!file_exists($dataFile)) {
            throw new \Exception('文件　'.$dataFile.' 不存在');
        }

        $data = ExcelUtils::readExcelToArray($dataFile, 'F');
        if (empty($data)) {
            throw new \Exception('没有找到合适数据');
        }
        $count = count($data);
        $successCount = $errorCount = 0;
        foreach ($data as $item) {
            if (!empty($item)) {
                list($day, $name, $number, $content, $summary, $reception) = $item;
                $engagement = new Engagement([
                    'number' => $number,
                    'callTime' => $day,
                    'callerName' => $name,
                    'content' => $content,
                    'summary' => $summary,
                    'reception' => $reception,
                    'activityType' => Activity::TYPE_RECEPTION,
                ]);

                if (
                    (
                        empty($number)
                        || $engagement->validate(['number'])
                    )
                    && $engagement->addCall()
                ) {
                    $successCount ++;
                } else {
                    file_put_contents($logFile, $number . ' | '.(current($engagement->getFirstErrors())) . PHP_EOL . json_encode($item) . PHP_EOL, FILE_APPEND);
                    $errorCount++;
                }
            }
        }
        $this->stdout("记录 $count , 成功记录　$successCount, 失败记录 $errorCount" . PHP_EOL);
    }
}