<?php
/**
 * 保全定时任务
 */
namespace console\controllers;

use common\models\order\BaoQuanQueue;
use common\models\product\OnlineProduct;
use EBaoQuan\Client;
use yii\base\Exception;
use yii\console\Controller;

class BaoQuanController extends Controller
{
    //根据保全队列批量添加保全
    public function actionIndex()
    {
        $queues = BaoQuanQueue::find()->where(['status' => BaoQuanQueue::STATUS_SUSPEND])->orderBy(['id' => SORT_DESC])->all();
        if (count($queues) > 0) {
            $client = new Client();
            foreach ($queues as $queue) {
                $proId = $queue['proId'];
                $product = OnlineProduct::findOne($proId);
                if (null !== $product) {
                    try {
                        $client->createBq($product);
                        $queue->status = BaoQuanQueue::STATUS_SUCCESS;//处理成功
                        $queue->save(false);
                    } catch (Exception $e) {
                        $queue->status = BaoQuanQueue::STATUS_FAILED;//处理失败
                        $queue->save(false);
                    }
                }
            }
        } else {
            sleep(3);
        }
    }

    //测试保全是否联通
    public function actionPing()
    {
        $client = new Client();
        $client->ping();
    }
}