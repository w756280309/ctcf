<?php

namespace console\modules\tx\controllers;

use common\models\tx\Loan;
use common\models\tx\Order;
use common\models\tx\UserAsset;
use Yii;
use yii\console\Controller;

class UserAssetController extends Controller
{
    /**
     * 同步原有的用户资产存量数据.
     *
     * @throws Exception
     */
    public function actionLoad()
    {
        $o = Order::tableName();
        $l = Loan::tableName();

        $orders = Order::find()
            ->innerJoinWith('loan')
            ->where(["$o.status" => 1, "$l.is_jixi" => true])
            ->all();

        $transaction = Yii::$app->db_tx->beginTransaction();

        try {
            foreach ($orders as $order) {
                $asset = new UserAsset([
                    'user_id' => $order->user_id,
                    'loan_id' => $order->loan_id,
                    'order_id' => $order->id,
                    'isRepaid' => 6 === $order->loan->status,
                    'amount' => $order->amount,
                    'orderTime' => $order->orderTime,
                    'maxTradableAmount' => 5 === $order->loan->status ? $order->amount : '0',
                    'isTrading' => false,
                    'isTest' => $order->loan->isTest,
                    'isInvalid' => false,
                ]);

                if (!$asset->save()) {
                    throw new \Exception();
                }
            }

            $transaction->commit();
        } catch (\Exception $ex) {
            $transaction->rollBack();
            throw $ex;
        }
    }
}
