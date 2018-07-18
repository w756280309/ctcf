<?php

namespace console\modules\tx\controllers;

use common\models\tx\Loan;
use common\models\tx\Order;
use common\models\tx\UserAsset;
use Yii;
use yii\console\Controller;
use yii\helpers\ArrayHelper;

class UserAssetController extends Controller
{
    /**
     * 同步原有的用户资产存量数据，可支持单个同步.
     *
     * @param null|integer $loanId 标的ID
     *
     * @throws \Exception
     */
    public function actionLoad($loanId = null)
    {
        $o = Order::tableName();
        $l = Loan::tableName();

        $query = Order::find()
            ->innerJoinWith('loan')
            ->where(["$o.status" => 1, "$l.is_jixi" => true]);

        if (null === $loanId) {
            $orders = $query->all();
        } else {
            $orders = $query->andWhere(["$l.id" => $loanId])
                ->all();
        }

        $loanIds = array_unique(ArrayHelper::getColumn($orders, 'online_pid'));
        $this->stdout('待同步的标的'.count($loanIds).'个');

        /** 事务开始 */
        $transaction = Yii::$app->db_tx->beginTransaction();

        try {
            foreach ($orders as $order) {
                $loan = $order->loan;
                $asset = new UserAsset([
                    'user_id' => $order->user_id,
                    'loan_id' => $order->loan_id,
                    'order_id' => $order->id,
                    'isRepaid' => 6 === $loan->status,
                    'amount' => $order->amount,
                    'orderTime' => $order->orderTime,
                    'maxTradableAmount' => 5 === $loan->status ? $order->amount : '0',
                    'isTrading' => false,
                    'isTest' => $loan->isTest,
                    'isInvalid' => false,
                    'allowTransfer' => $loan->allowTransfer,
                ]);

                if (!$asset->save()) {
                    throw new \Exception(current($asset->getFirstErrors()));
                }
            }

            $transaction->commit();

            $assetCount = UserAsset::find()
                ->where(['in', 'loan_id', $loanIds])
                ->count();
            $this->stdout('共新增资产'.$assetCount.'个。');
        } catch (\Exception $ex) {
            $transaction->rollBack();
            throw $ex;
        }
        /** 事务结束 */
    }
}
