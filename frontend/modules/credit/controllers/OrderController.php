<?php

namespace frontend\modules\credit\controllers;


use common\models\order\OnlineOrder;
use common\models\product\OnlineProduct;
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
        //获取资产详情
        $amount = floatval($amount);
        $txClient = \Yii::$container->get('txClient');
        $note = $txClient->get('credit-note/detail', ['id' => $id]);
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
        $currentInterest = bcdiv($asset['currentInterest'], 100);
        $remainingInterest = bcdiv($asset['remainingInterest'], 100);
        $maxTradableAmount = bcdiv($asset['maxTradableAmount'], 100);
        $interest = bcdiv(bcmul($currentInterest, $amount), $maxTradableAmount);//应付利息
        $profit = bcdiv(bcmul($remainingInterest, $amount), $maxTradableAmount);//预期收益
        $payAmount = bcmul(bcadd($amount, $interest), bcsub(1, $rate));//实际支付金额
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
}