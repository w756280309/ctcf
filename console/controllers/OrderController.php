<?php
/**
 * 订单队列定时任务文件.
 * User: zhanghongyu
 * Date: 16-4-6
 */
namespace console\controllers;

use common\models\order\OnlineOrder;
use common\models\order\OrderManager;
use common\models\order\OrderQueue;
use Ding\DingNotify;
use yii\console\Controller;
use Yii;

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
                        //超过5秒订单做钉钉提醒
                        $order = $queue->order;
                        if ($order->order_time + 5 < time()) {
                            $user = $order->user;
                            if (!empty($user)) {
                                (new DingNotify('wdjf'))->sendToUsers('用户[' . $user->mobile . ']，于' . date('Y-m-d H:i:s') . ' 进行标的购买操作，操作失败，此订单在队列中超过5秒未被成功处理，订单ID:' . $order->id);
                            }
                        }
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

    /**
     * 撤标
     * @param $id
     */
    public function actionCancelOrder($id)
    {
        $order = OnlineOrder::findOne($id);
        try {
            OrderManager::cancelLoanOrder($order);
            Yii::trace('撤标成功', 'loan_order');
        } catch (\Exception $ex) {
            Yii::trace('订单撤销失败，失败信息：' . $ex->getMessage(), 'loan_order');
        }
    }
}
