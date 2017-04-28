<?php

namespace Xii\Crm\Command;


use common\utils\ExcelUtils;
use Xii\Crm\Model\Activity;
use Xii\Crm\Model\Contact;
use Xii\Crm\Model\Engagement;
use Yii;
use yii\console\Controller;

class EngagementController extends Controller
{
    //导入客服记录, 临时代码
    public function actionImport()
    {
        $path = Yii::$app->getRuntimePath();
        $dataFile = $path . '/phone_call.xlsx';

        if (!file_exists($dataFile)) {
            throw new \Exception('文件　' . $dataFile . ' 不存在');
        }

        $data = ExcelUtils::readExcelToArray($dataFile, 'J', 10000);
        if (empty($data)) {
            throw new \Exception('没有找到合适数据');
        }
        $count = count($data);
        $successCount = $errorCount = 0;
        foreach ($data as $key => $item) {
            if (!empty($item)) {
                list($a, $b, $c, $d, $e, $content, $summary, $h, $i, $j) = $item;
                $r = Yii::$app->db->createCommand("insert into crm_test (`a`,`b`,`c`,`d`,`e`,`content`,`summary`,`h`,`i`,`j`, `type`) VALUE (:a,:b,:c,:d,:e,:content,:summary,:h,:i,:j, 'phone_call')", [
                    'a' => $a,
                    'b' => $b,
                    'c' => $c,
                    'd' => $d,
                    'e' => $e,
                    'content' => $content,
                    'summary' => $summary,
                    'h' => $h,
                    'i' => $i,
                    'j' => $j,
                ])->execute();
                if ($r) {
                    $successCount++;
                } else {
                    var_dump($item);
                    $errorCount++;
                }
            }
        }
        $this->stdout("总记录 $count , 导入成功记录　$successCount, 失败记录 $errorCount" . PHP_EOL);
    }

    //修复客服记录中时间字段和姓名字段颠倒的数据　临时代码
    public function actionRepair($run = false)
    {
        $data = Yii::$app->db->createCommand("select `id`, `b`, `c` from crm_test where  `type` = 'phone_call'")->queryAll();
        $count = 0;
        $successCount = 0;
        foreach ($data as $value) {
            $time = trim($value['b']);
            $name = trim($value['c']);
            if (!is_numeric(mb_substr($time, 0, 1)) && is_numeric(mb_substr($name, 0, 1))) {

                if ($run) {
                    $res = Yii::$app->db->createCommand("update `crm_test` set `b` = :time , `c` = :name where `id` = :id", [
                        'time' => $name,
                        'name' => $time,
                        'id' => $value['id'],
                    ])->execute();
                    if ($res) {
                        $successCount++;
                    } else {
                        echo "修复失败 time: $time  |  name: $name" . PHP_EOL;
                    }
                } else {
                    echo "time: $time  |  name: $name" . PHP_EOL;
                }
                $count++;
            }
        }

        echo "总共 $count 条交叉数据" . PHP_EOL;
        if ($successCount > 0) {
            echo "成功修复 $successCount 条交叉数据" . PHP_EOL;
        }
    }

    /**
     * 从临时表中添加客服记录
     *
     * 查找 crm_test 表中　type='phone_call', status !=1的数据
     */
    public function actionCall()
    {
        $data = Yii::$app->db->createCommand("select `id`, `a`, `b`, `c`, `d`, `e`,`summary`, `content`, `j` from crm_test where `status` != 1 and `type` = 'phone_call'")->queryAll();
        foreach ($data as $key => $item) {
            if (!empty($item)) {
                list($id, $day, $time, $name, $number, $duration, $content, $summary, $reception) = array_values($item);
                $duration = intval($duration);//通话时长以分为单位
                $dateTime = $day . ' ' . $time;
                $dateTime = str_replace('；', ':', $dateTime);
                $dateTime = str_replace(';', ':', $dateTime);
                $dateTime = str_replace('：', ':', $dateTime);
                $dateTime = str_replace('  ', ' ', $dateTime);

                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    $engagement = new Engagement([
                        'number' => $number,
                        'duration' => $duration,
                        'callTime' => (new \DateTime($dateTime))->format('Y-m-d H:i:s'),
                        'direction' => Engagement::TYPE_IN,
                        'callerName' => $name,
                        'content' => $content,
                        'summary' => $summary,
                        'reception' => $reception,
                        'type' => Activity::TYPE_PHONE_CALL,
                    ]);
                    if ($engagement->validate(['number'])) {
                        $contact = Contact::fetchOneByNumber($engagement->number);
                        if (is_null($contact)) {
                            $contact = Contact::initNew($engagement->number);
                        }
                        $engagement->setContact($contact);
                    } else {
                        $contact = Contact::initNew($engagement->number);
                        $contact->creator_id = $engagement->creator_id;
                        $contact->save(false);
                        $engagement->contact_id = $contact->id;
                    }

                    $engagement->setIdentity();
                    $engagement->setActivity();
                    $engagement->save(false);

                    Yii::$app->db->createCommand('update `crm_test` set `status` = 1 where `id` = :id', ['id' => $id])->execute();
                    $transaction->commit();
                } catch (\Exception $e) {
                    $transaction->rollBack();

                    echo $id . ' | ' . $e->getMessage() . PHP_EOL;

                    Yii::$app->db->createCommand('update `crm_test` set `status` = 2, `error`=:errorMessage where `id` = :id ', [
                        'errorMessage' => urlencode($e->getMessage()),
                        'id' => $id,
                    ])->execute();
                }
            }
        }
    }

    /**
     * 导入门店客户接待记录
     *
     * 1. 文件名为  'engagement.xlsx', 文件位于 WDJF/console/runtime, 导入完成之后需要手工清理
     * 2. 输出成功条数、失败条数
     * 3. 记录失败数据到 'engagement.txt' 中，记录失败电话及失败信息，文件位于 WDJF/console/runtime, 修复完成之后需要手工清理
     * 4. 真实数据221行
     */
    /*    public function actionRec()
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
            foreach ($data as $key => $item) {
                if (!empty($item)) {
                    list($day, $name, $number, $content, $summary, $reception) = $item;
                    $engagement = new Engagement([
                        'number' => $number,
                        'callTime' => $day,
                        'callerName' => $name,
                        'content' => $content,
                        'summary' => $summary,
                        'reception' => $reception,
                        'type' => Activity::TYPE_RECEPTION,
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
                        file_put_contents($logFile, $key . ' | ' .$number . ' | '.(current($engagement->getFirstErrors())) . PHP_EOL, FILE_APPEND);
                        $errorCount++;
                    }
                }
            }
            $this->stdout("记录 $count , 成功记录　$successCount, 失败记录 $errorCount" . PHP_EOL);
        }*/
}