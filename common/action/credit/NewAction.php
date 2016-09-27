<?php

namespace common\action\credit;

use Yii;
use yii\base\Action;
use common\models\product\OnlineProduct;
use common\models\order\OnlineOrder;

class NewAction extends Action
{
    public function run($asset_id)
    {
        if (\Yii::$app->user->isGuest) {
            return $this->controller->redirect('/site/login');
        }
        //获取资产详情
        $txClient = \Yii::$container->get('txClient');
        $asset = $txClient->get('assets/detail', ['id' => $asset_id, 'validate' => true]);

        if (null === $asset) {
            throw $this->controller->ex404('没有找到指定资产');
        }
        if (false === $asset['validate']) {
            throw $this->controller->ex404('不满足发起条件');
        }
        $loan = OnlineProduct::findOne($asset['loan_id']);
        if (null === $loan || $loan->status !== 5) {
            throw $this->controller->ex404('没有找到合适标的');
        }
        $order = OnlineOrder::findOne($asset['order_id']);
        if (null === $order) {
            throw $this->controller->ex404('没有找到订单');
        }
        $apr = $order->yield_rate;

        return $this->controller->render('new', [
            'asset' => $asset,
            'loan' => $loan,
            'apr' => $apr,
        ]);
    }
}
