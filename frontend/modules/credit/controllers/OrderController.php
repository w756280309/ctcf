<?php

namespace frontend\modules\credit\controllers;

use common\lib\credit\CreditNote;
use common\models\order\OnlineOrder;
use common\models\product\OnlineProduct;
use common\models\user\User;
use frontend\controllers\BaseController;
use yii\filters\AccessControl;

class OrderController extends BaseController
{
    public function behaviors()
    {
        return [
            'access' => [    //登录控制,如果没有登录,则跳转到登录页面
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    //确认购买页
    public function actionConfirm($id, $amount)
    {
        bcscale(14);
        //获取资产详情
        $amount = floatval($amount);
        $txClient = \Yii::$container->get('txClient');
        $note = $txClient->get('credit-note/detail', ['id' => $id, 'is_long' => true]);
        if (null === $note || !isset($note['asset'])) {
            $this->ex404('没有找到指定债权');
        }
        $rate = bcdiv($note['discountRate'], 100);
        $asset = $note['asset'];
        $loan = OnlineProduct::findOne($asset['loan_id']);
        $order = OnlineOrder::findOne($asset['order_id']);
        if (null === $loan || null === $order) {
            $this->ex404('没有找到指定债权');
        }
        $interest = bcdiv(bcmul($note['currentInterest'], $amount), $note['amount'], 2);//应付利息
        $profit = bcdiv(bcmul($note['remainingInterest'], $amount), $note['amount'], 2);//预期收益
        $payAmount = bcmul(bcadd($amount, $interest), bcsub(1, $rate), 2);//实际支付金额
        return $this->render('confirm', [
            'note' => $note,
            'order' => $order,
            'loan' => $loan,
            'interest' => $interest,
            'profit' => $profit,
            'amount' => $amount,
            'payAmount' => $payAmount,
        ]);
    }

    /**
     * ajax请求新建债权订单
     */
    public function actionNew()
    {
        $request = \Yii::$app->request;
        $userId = $request->post('user_id');
        $noteId = $request->post('note_id');
        $principal = $request->post('principal');//实际购买本金
        $user = User::findOne($userId);
        if (null === $user) {
            return ['code' => 0, 'url' => '', 'message' => '无法找到该用户'];
        }
        $creditNote = new CreditNote();
        $checkResult = $creditNote->check($noteId, $principal, $user);
        if (1 === $checkResult['code']) {
            $checkResult['url'] = '';
            return $checkResult;
        }
        try {
            $txClient = \Yii::$container->get('txClient');
            $res = $txClient->post('credit-order/new', [
                'user_id' => $userId,
                'note_id' => $noteId,
                'principal' => bcmul($principal, 100, 0),
            ]);
            if (isset($res['id'])) {
                return ['code' => 0, 'url' => '/credit/order/wait?order_id=' . $res['id']];
            } else {
                return ['code' => 1, 'url' => '/info/fail?source=credit_order'];
            }
        } catch (\Exception $ex) {
           return ['code' => 1, 'url' => '', 'message' => $ex->getMessage()];
        }
    }

    //购买债权等待页
    public function actionWait()
    {
        $request = \Yii::$app->request;
        $order_id = intval($request->get('order_id'));
        if ($request->isPost) {
            $order_id = intval($request->post('order_id'));
        }
        $txClient = \Yii::$container->get('txClient');
        $order = $txClient->get('credit-order/detail', ['id' => $order_id]);
        if (null === $order) {
            throw $this->ex404('没有找到指定订单');
        }
        if ($request->isPost) {
            if ($order['status'] === 1) {
                return ['code' => 0, 'url' => '/info/success?source=credit_order&jumpUrl=/user/user/myorder'];
            } elseif ($order['status'] === 2) {
                return ['code' => 0, 'url' => '/info/fail?source=credit_order'];
            } else {
                return ['code' => 1];
            }
        } else {
            return $this->render('wait', [
                'order_id' => $order_id,
            ]);
        }
    }
}