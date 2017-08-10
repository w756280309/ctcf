<?php

namespace backend\modules\product\controllers;

use backend\controllers\BaseController;
use common\models\offline\OfflineLoan;
use common\models\offline\OfflineOrder;
use common\models\offline\OfflineUser;
use common\models\order\OnlineOrder;
use common\models\product\OnlineProduct;
use common\models\user\User;

class GrowthController extends BaseController
{
    /**
     * 打印确认函预览页面
     * @return string
     */
    public function actionLetter()
    {
        $orderId = \Yii::$app->request->get('orderId');
        $isOnline = \Yii::$app->request->get('isOnline');
        $loanId = \Yii::$app->request->get('loanId');
        $date = date('Y-m-d');
        $data = [];
        if ($isOnline) {
            /**
             * @var User $user
             * @var OnlineProduct $loan
             * @var OnlineOrder $order
             */
            if (empty($orderId)) {
                $this->ex404();
            }
            $order = OnlineOrder::findOne($orderId);
            if (is_null($order)) {
                $this->ex404();
            }
            $loan = $order->loan;
            $user = $order->user;
            $duration = $loan->getDuration();

            $data[] = [
                'userName' => $user->getName(),
                'orderDate' => $order->getOrderDate(),
                'title' => $loan->title,
                'idcard' => $user->getIdNo(),
                'startDate' => $loan->getStartDate(),
                'endDate' => $loan->getEndDate(),
                'duration' => $duration['value'].$duration['unit'],
                'orderMoney' => $order->order_money,
                'rate' => bcmul($order->yield_rate, 100, 2) . '%',
                'refundMethod' => \Yii::$app->params['refund_method'][$loan->getRefundMethod()],
                'date' => $date,
            ];
        } else {
            if (empty($loanId)) {
                $this->ex404();
            }
            /**
             * @var OfflineLoan     $loan
             * @var OfflineOrder    $order
             * @var OfflineUser     $user
             */
            $loan = OfflineLoan::findOne($loanId);
            if (is_null($loanId)) {
                $this->ex404();
            }
            $orders = OfflineOrder::find()->where(['loan_id' => $loan->id])->orderBy(['id' => SORT_ASC])->with('user')->all();
            foreach ($orders as $order) {
                $user = $order->user;
                $data[] = [
                    'userName' => $user->realName,
                    'orderDate' => $order->orderDate,
                    'title' => $loan->title,
                    'idcard' => $user->idCard,
                    'startDate' => $order->valueDate,
                    'endDate' => (new \DateTime($loan->finish_date))->format('Y-m-d'),
                    'duration' => $loan->expires.$loan->unit,
                    'orderMoney' => bcmul($order->money, 10000, 2),
                    'rate' => bcmul($order->apr, 100, 2) . '%',
                    'refundMethod' => \Yii::$app->params['refund_method'][$loan->repaymentMethod],
                    'date' => $date,
                ];
            }
        }

        return $this->renderFile('@backend/modules/product/views/growth/letter.php', [
            'data' => $data,
        ]);
    }
}