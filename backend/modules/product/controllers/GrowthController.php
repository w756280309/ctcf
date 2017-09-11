<?php

namespace backend\modules\product\controllers;

use backend\controllers\BaseController;
use common\models\offline\OfflineLoan;
use common\models\offline\OfflineOrder;
use common\models\offline\OfflineUser;
use common\models\order\OnlineOrder;
use common\models\product\OnlineProduct;
use common\models\tx\CreditOrder;
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
                throw $this->ex404();
            }
            $order = OnlineOrder::findOne($orderId);
            if (is_null($order)) {
                throw $this->ex404();
            }
            $loan = $order->loan;
            $user = $order->user;
            $duration = $loan->getDuration();

            $data[] = [
                'userName' => $user->getName(),
                'orderDate' => $order->getOrderDate(),
                'title' => $loan->title,
                'idcard' => $user->getIdcard(),
                'startDate' => new \DateTime($loan->getStartDate()),//起息日
                'fullDate' => new \DateTime($loan->getStartDate()),//成立日
                'endDate' => new \DateTime($loan->getEndDate()),
                'duration' => $duration['value'].$duration['unit'],
                'orderMoney' => $order->order_money,
                'rate' => bcmul($order->yield_rate, 100, 2) . '%',
                'refundMethod' => \Yii::$app->params['refund_method'][$loan->getRefundMethod()],
                'date' => $date,
            ];
        } else {
            if (empty($loanId)) {
                throw $this->ex404();
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
            $orders = OfflineOrder::find()->where(['loan_id' => $loan->id])->andWhere(['isDeleted' => false])->orderBy(['id' => SORT_ASC])->with('user')->all();
            foreach ($orders as $order) {
                $user = $order->user;
                $data[] = [
                    'userName' => $user->realName,
                    'orderDate' => $order->orderDate,
                    'title' => $loan->title,
                    'idcard' => $user->idCard,
                    'startDate' => (new \DateTime($order->valueDate)),//起息日
                    'fullDate' => (new \DateTime($loan->jixi_time)),//成立日
                    'endDate' => (new \DateTime($loan->finish_date)),
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

    public function actionOrderCert()
    {
        $orderId = \Yii::$app->request->get('orderId');

        if (empty($orderId)) {
            throw $this->ex404();
        }
        $order = OnlineOrder::findOne($orderId);
        $loanOrder = $order;
        if (is_null($order)) {
            throw $this->ex404();
        }

        $loan = $order->loan;
        if (is_null($loan)) {
            throw $this->ex404();
        }

        return $this->createData($loan, $order, $loanOrder);
    }

    public function actionTransferCert()
    {
        $orderId = \Yii::$app->request->get('orderId');

        if (empty($orderId)) {
            throw $this->ex404();
        }
        $order = CreditOrder::findOne($orderId);
        if (is_null($order)) {
            throw $this->ex404();
        }
        $note = $order->note;
        if (is_null($note)) {
            throw $this->ex404();
        }
        $loanOrder = $note->order;
        if (is_null($loanOrder) || is_null($loan = OnlineProduct::findOne($loanOrder->getLoan_id()))) {
            throw $this->ex404();
        }

        return $this->createData($loan, $order, $loanOrder);
    }

    private function createData($loan, $order, $loanOrder)
    {
        $user = $order->user;
        $duration = $loan->getDuration();
        $ebaoquan = $order->fetchBaoQuan();
        if (null === $ebaoquan) {
            throw $this->ex404();
        }
        $orderInfo = $this->getOrderInfo($order);
        $data = [
            'ebaoquanId' => $ebaoquan->baoId,
            'ebaoquanDate' => (new \DateTime(date('Y-m-d H:i:s',$ebaoquan->updated_at))),
            'userName' => $user->getName(),
            'idcard' => $user->getIdcard(),
            'title' => $order instanceof CreditOrder ? '【转让】 '.$loan->title : $loan->title,
            'duration' => $duration['value'].$duration['unit'],
            'rate' => bcmul($loanOrder->yield_rate, 100, 2) . '%',
            'orderMoney' => $orderInfo['orderMoney'],
            'refundMethod' => \Yii::$app->params['refund_method'][$loan->getRefundMethod()],
            'orderDate' => $orderInfo['orderDate'],
            'date' => (new \DateTime()),
        ];

        return $this->renderFile('@backend/modules/product/views/growth/certificate.php', [
            'data' => $data,
        ]);
    }

    private function getOrderInfo($order)
    {
        if ($order instanceof OnlineOrder) {
            $orderInfo['orderMoney'] = $order->order_money;
            $orderInfo['orderDate'] = (new \DateTime($order->orderDate));
        } else {
            $orderInfo['orderMoney'] = $order->principal;
            $orderInfo['orderDate'] = (new \DateTime($order->createTime));
        }

        return $orderInfo;
    }
}
