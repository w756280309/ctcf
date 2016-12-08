<?php
/**
 * 订单队列定时任务文件.
 * User: zhanghongyu
 * Date: 16-4-6
 */
namespace console\controllers;

use common\models\order\OrderManager;
use common\models\order\OrderQueue;
use common\models\promo\Promo1212;
use wap\modules\promotion\models\RankingPromo;
use yii\console\Controller;

class OrderController extends Controller
{
    public function actionQueue()
    {

        $promo = RankingPromo::findOne(['key' => 'promo_12_12_21']);
        $promo1212 = new Promo1212($promo);

        for ($i = 0; $i < 10;++$i) {
            $queues = OrderQueue::find()->where(['status' => 0])->orderBy('created_at asc')->limit(3)->all();//没有查到数据会返回空数组
            if (count($queues)) {
                //循环处理单个任务
                foreach ($queues as $queue) {
                    try {
                        if (!OrderManager::cancelNoPayOrder($queue->order)) {
                            //cancelNoPayOrder返回值false代表订单成立
                            $bool = OrderManager::confirmOrder($queue->order);

                            //promo_12_12活动，未投资用户首投返现金红包
                            if ($bool) {
                                $promo1212->sendRedPacket($queue->order->user);
                            }
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
