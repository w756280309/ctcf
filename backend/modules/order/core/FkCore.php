<?php

namespace backend\modules\order\core;

use Yii;
use common\lib\bchelp\BcRound;
use common\models\order\OnlineOrder;
use common\models\product\OnlineProduct;
use common\models\order\OnlineFangkuan;
use common\models\order\OnlineFangkuanDetail;
use common\models\order\OnlineRepaymentPlan;
use common\models\payment\PaymentLog;
use common\utils\TxUtils;

/**
 * Desc 放款core
 * Created by zhy.
 * User: zhy
 * Date: 15-11-19
 * Time: 下午4:02.
 */
class FkCore
{
    public function createFk($admin_id = 0, $pid = null, $status = 0)
    {
        bcscale(14);
        $bcround = new BcRound();

        $plancount = OnlineRepaymentPlan::find()->where(['online_pid' => $pid])->count();
        if (0 === (int) $plancount) {
            return ['res' => 0, 'msg' => '标的需要先进行确认计息操作'];
        }

        $orders = OnlineOrder::find()->where(['online_pid' => $pid, 'status' => OnlineOrder::STATUS_SUCCESS])->asArray()->all();
        $product = OnlineProduct::findOne($pid);
        $total = 0;
        $coupon_amount = 0;
        foreach ($orders as $val) {
            if ($val['userCoupon_id']) {
                $coupon_amount = bcadd($coupon_amount, $val['couponAmount']);
            }
            $total = bcadd($total, $val['order_money']);
        }

        /*生成放款批次 start*/
        $transaction = Yii::$app->db->beginTransaction();
        if (bccomp($coupon_amount, 0)) {
            $plog = new PaymentLog([
                'txSn' => TxUtils::generateSn('P'),
                'amount' => $coupon_amount,
                'toParty_id' => $product->borrow_uid,
                'loan_id' => $pid,
            ]);
            if (!$plog->save()) {
                $transaction->rollBack();

                return ['res' => 0, 'msg' => '交易记录生成失败'];
            }
        }

        $ofk = new OnlineFangkuan();
        $ofk->sn = OnlineFangkuan::createSN();
        $ofk->order_money = $bcround->bcround($total, 2);
        $ofk->online_product_id = $pid;
        $ofk->fee = 0;
        $ofk->uid = $product->borrow_uid;//借款人uid
        $ofk->status = $status;
        $ofk->remark = '';
        $ofk->admin_id = $admin_id;
        if (!$ofk->validate()) {
            $transaction->rollBack();

            return ['res' => 0, 'msg' => current($ofk->firstErrors)];
        }
        $fkre = $ofk->save();
        if (!$fkre) {
            $transaction->rollBack();

            return ['res' => 0, 'msg' => '放款批次异常2'];
        }
        $ofkd = new OnlineFangkuanDetail();
        $fkdstatus = $status;
        foreach ($orders as $order) {
            $ofkd_model = clone $ofkd;
            $ofkd_model->fangkuan_order_id = $ofk->id;
            $ofkd_model->product_order_id = $order['id'];
            $ofkd_model->order_money = $order['order_money'];
            $ofkd_model->online_product_id = $pid;
            $ofkd_model->order_time = $order['order_time'];
            $ofkd_model->admin_id = Yii::$app->user->id;
            $ofkd_model->status = $fkdstatus;
            if (!$ofkd_model->save() || !$ofkd_model->validate()) {
                $transaction->rollBack();

                return ['res' => 0, 'msg' => '放款批次详情异常'];
            }
        }

        //修改标的状态为还款中
        $opres = OnlineProduct::updateAll(['fk_examin_time' => time(), 'sort' => OnlineProduct::SORT_HKZ], ['id' => $pid]);//'status'=>OnlineProduct::STATUS_HUAN,
        if (!$opres) {
            $transaction->rollBack();

            return ['res' => 0, 'msg' => '标的状态更新失败'];
        }
        $transaction->commit();

        return ['res' => 1, 'msg' => '成功'];
    }
}
