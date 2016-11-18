<?php
/**
 * 订单队列定时任务文件.
 * User: zhanghongyu
 * Date: 16-4-6
 */
namespace console\controllers;

use common\models\order\OrderManager;
use common\models\order\OrderQueue;
use yii\console\Controller;

class OrderController extends Controller
{
    public function actionQueue()
    {

        for ($i = 0; $i < 10;++$i) {
            $queues = OrderQueue::find()->where(['status' => 0])->orderBy('created_at asc')->limit(3)->all();//没有查到数据会返回空数组
            if (count($queues)) {
                //循环处理单个任务
                foreach ($queues as $queue) {
                    try {
                        if (!OrderManager::cancelNoPayOrder($queue->order)) {
                            //cancelNoPayOrder返回值false代表订单成立
                            OrderManager::confirmOrder($queue->order);
                        }
                    } catch (\Exception $ex) {
                        $msg = '标的订单处理：订单号-'.$queue->order->id.';异常信息-'.$ex->getMessage();
                        \Yii::trace($msg, 'loan_order');
                    }
                }
            } else {
                usleep(500000);
            }
        }
        exit(0);
    }
}
