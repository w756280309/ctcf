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
use common\models\order\OrderManager;

class OrderController extends Controller
{
    public function actionQueue()
    {
        bcscale(14);
        $loans = OrderQueue::findQueue();
        foreach ($loans as $loan) {
            $ordmoney = 0;
            foreach ($loan['data'] as $ord) {
                if (bccomp($loan['money'], bcadd($ordmoney, $ord['order_money'])) < 0) {
                    //超过的需要撤标
                    OrderManager::cancelNoPayOrder($ord['orderSn']);
                    continue;
                }
                $ordmoney = bcadd($ordmoney, $ord['order_money']);
                OrderManager::confirmOrder($ord['orderSn']);
            }
        }
    }
}
