<?php

namespace api\modules\v1\controllers\rest;

use Yii;
use common\models\order\OnlineOrder;
use common\models\order\CancelOrder;
use api\modules\v1\controllers\Controller;

/**
 * 投标类交易API.
 *
 * @author zhanghongyu<zhanghongyu@wangcaigu.com>
 */
class OrdertxController extends Controller
{
    private function ensureOrder($type, $id)
    {
        $orderTx = null;
        if (1 === (int) $type) {
            $orderTx = OnlineOrder::find()->where(['id' => $id, 'status' => [1, 2]])->one();
        } elseif (2 === (int) $type) {
            $orderTx = CancelOrder::findOne($id);
        } else {
            throw $this->ex400('type is wrong');
        }
        if (null === $orderTx) {
            throw $this->ex404();
        }

        return $orderTx;
    }

    public function actionUmp($type, $id)
    {
        $orderTx = $this->ensureOrder($type, $id);
        $resp = Yii::$container->get('ump')->getOrderInfo($orderTx);

        return [
            'mer_id' => $resp->get('mer_id'),
            'order_id' => $resp->get('order_id'),
            'trade_no' => $resp->get('trade_no'),
            'amount' => $resp->get('amount'),
            'orig_amt' => $resp->get('orig_amt'),
            'com_amt' => $resp->get('com_amt'),
            'mer_check_date' => $resp->get('mer_check_date'),
            'mer_date' => $resp->get('mer_date'),
            'trade_no' => $resp->get('trade_no'),
            'tran_state' => $resp->get('tran_state'),
        ];
    }
}
