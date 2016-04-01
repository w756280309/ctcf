<?php
/**
 * 定时任务文件.
 * User: xmac
 * Date: 15-3-19
 * Time: 下午3:51.
 */
namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\models\order\OrderQueue;
use common\service\OrderService;

class OrdercrontabController extends Controller
{
    public function actionQueue()
    {
        $data = OrderQueue::find()->where(['status' => 0])->all();
        foreach ($data as $queue) {
            OrderService::confirmOrder($queue->order);
        }
        
    }
}
