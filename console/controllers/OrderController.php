<?php
/**
 * 订单队列定时任务文件.
 * User: zhanghongyu
 * Date: 16-4-6
 */
namespace console\controllers;

use common\models\order\OnlineOrder;
use common\models\user\UserInfo;
use yii\console\Controller;
use common\models\order\OrderQueue;
use common\models\order\OrderManager;
use wap\modules\promotion\models\RankingPromo;
use wap\modules\promotion\promo\Promo160707;

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
                        //投资完成之后计算抽奖机会
                        $promoConfig = RankingPromo::find()->where(['key' => 'PC_LAUNCH_160707'])->one();
                        if ($promoConfig) {
                            $time = time();
                            $promo = new Promo160707($promoConfig);
                            if ($time > $promo->startAt && $time < $promo->endAt) {
                                $promo->onInvested($queue->order);
                            }
                        }

                        //投资成功之后更新用户信息
                        $order = $queue->order;
                        if ($order->status == 1) {
                            $info = UserInfo::find()->where(['user_id' => $order['uid']])->one();
                            if (null === $info) {
                                $info = new UserInfo();
                                $info->user_id = $order['uid'];
                            }
                            if (!$info->isInvested) {
                                $info->isInvested = 1;
                            }
                            if (!$info->firstInvestAmount) {
                                $info->firstInvestAmount = $order['order_money'];
                            }
                            if (!$info->firstInvestDate) {
                                $info->firstInvestDate = date('Y-m-d', $order['order_time']);
                            }
                            $info->investCount = $info->investCount + 1;
                            $info->investTotal = $info->investTotal + $order['order_money'];
                            $info->averageInvestAmount = $info->investTotal / $info->investCount;
                            $info->lastInvestAmount = $order['order_money'];
                            $info->lastInvestDate = date('Y-m-d', $order['order_time']);
                            $info->save();
                        }
                    } catch (\Exception $ex) {
                        //TODO
                    }
                }
            } else {
                usleep(100000);
            }
        }
        exit(0);
    }
}
