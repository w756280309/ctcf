<?php

namespace common\models\order;

use Yii;
use common\models\product\OnlineProduct as Loan;
use common\lib\bchelp\BcRound;
use common\models\user\MoneyRecord;
use common\utils\TxUtils;
use yii\helpers\ArrayHelper;

/**
 * 订单manager.
 *
 * @author zhanghongyu<zhanghongyu@wangcaigu.com>
 */
class OrderManager
{
    /**
     * 用于标的满标超投部分撤销退款.
     *
     * @param OnlineOrder $order
     *
     * @return OnlineOrder
     *
     * @throws \Exception
     */
    public static function findInvalidOrders(Loan $loan)
    {
        bcscale(14);
        $orders = OnlineOrder::find()->where(['online_pid' => $loan->id, 'status' => OnlineOrder::STATUS_SUCCESS])->orderBy('id asc')->all();//升序排列
        //$overproof = array();
        $current_sum = 0;
        foreach ($orders as $ord) {
            $current_sum = bcadd($ord->order_money, $current_sum);
            if (bccomp($current_sum, $loan->money) > 0) {
                $returnMoney = null;
                if (bccomp(bcsub($current_sum, $loan->money), $ord->order_money) < 0) { //计算超标的第一个订单超标金额                    
                    $returnMoney = bcsub($current_sum, $loan->money);
                } else {
                    $returnMoney = null;
                }
                self::cancelOrder($ord, $returnMoney);
            }
        }
    }

    /**
     * 创建取消订单执行方法.
     *
     * @param OnlineOrder $order
     * @param type        $ret_money
     *
     * @return OnlineOrder
     *
     * @throws \Exception
     */
    public static function cancelOrder(OnlineOrder $order, $ret_money = null)
    {
        if (null !== $ret_money && bccomp($order->order_money, $ret_money) < 0) {
            throw new \Exception('申请退款金额超出订单金额');
        }
        $bc = new BcRound();
        bcscale(14);
        $resp = Yii::$container->get('ump')->getOrderInfo($order);
        if (!$resp->isSuccessful() || '2' !== $resp->get('tran_state')) {
            throw new \Exception('交易状态异常或者查询失败');
        }

        $transaction = Yii::$app->db->beginTransaction();
        $cancelOrder = CancelOrder::initForOrder($order, $ret_money);
        if (!$cancelOrder->save()) {
            $transaction->rollBack();
            throw new \Exception('交易创建失败');
        }

        //账户资金变动修改
        $ua = $order->user->lendAccount;

        //如果部分超标。不超标的部分可以投标
        if (null !== $ret_money) {
            $data = ArrayHelper::toArray($order);
            unset($data['id']);
            unset($data['sn']);
            $new_ord = new OnlineOrder($data);
            $new_ord->sn = TxUtils::generateSn();
            $new_ord->order_money = $bc->bcround(bcsub($order->order_money, $ret_money), 2);
            $new_ord->save();//创建一个订单

            $ua->available_balance = $bc->bcround(bcsub($ua->available_balance, $new_ord->order_money), 2);
            $ua->freeze_balance = $bc->bcround(bcadd($ua->freeze_balance, $new_ord->order_money), 2);
            $ua->out_sum = $bc->bcround(bcadd($ua->out_sum, $new_ord->order_money), 2);//ua有修改在判断外层save

            $mrmodel = new MoneyRecord();
            $mrmodel->account_id = $ua->id;
            $mrmodel->sn = TxUtils::generateSn('MR');
            $mrmodel->type = MoneyRecord::TYPE_ORDER;
            $mrmodel->osn = $new_ord->sn;
            $mrmodel->uid = $new_ord->uid;
            $mrmodel->balance = $ua->available_balance;
            $mrmodel->out_money = $new_ord->order_money;
            $mrmodel->remark = '资金流水号:'.$mrmodel->sn.',订单流水号:'.($order->sn).',账户余额:'.($ua->account_balance).'元，可用余额:'.($ua->available_balance).'元，冻结金额:'.$ua->freeze_balance.'元。';
            $mrmodel->save();//创建一个资金记录
        }

        $changeMoney = null === $ret_money ? $order->order_money : $ret_money;//计算更改的金额
        $ua->freeze_balance = $bc->bcround(bcsub($ua->freeze_balance, $changeMoney), 2);
        $ua->available_balance = $bc->bcround(bcadd($ua->available_balance, $changeMoney), 2);
        $ua->drawable_balance = $bc->bcround(bcadd($ua->drawable_balance, $changeMoney), 2);
        $ua->in_sum = $bc->bcround(bcadd($ua->in_sum, $changeMoney), 2);
        if (!$ua->save()) {
            $transaction->rollBack();
            throw new \Exception('账户变动失败');
        }
        $order->status = OnlineOrder::STATUS_CANCEL;
        if (!$order->save()) {
            $transaction->rollBack();
            throw new \Exception('订单状态修改失败');
        }

        $money_record = new MoneyRecord();
        $money_record->sn = TxUtils::generateSn('MR');
        $money_record->type = MoneyRecord::TYPE_ORDER;
        $money_record->osn = $order->sn;
        $money_record->account_id = $ua->id;
        $money_record->uid = $order->uid;
        $money_record->balance = $ua->available_balance;
        $money_record->in_money = $changeMoney;
        if (!$money_record->save()) {
            $transaction->rollBack();
            throw new \Exception('资金记录失败');
        }

        //联动标的转账
        $trans_resp = Yii::$container->get('ump')->loanTransferToLender($order);

        if (!$trans_resp->isSuccessful()) {
            $transaction->rollBack();
            exit;
            throw new \Exception('联动标的转账失败');
        }
        $transaction->commit();

        return $order;
    }

    /**
     * 定时确认撤销订单的状态
     *
     * @param CancelOrder $ord
     */
    public static function ackCancelOrder(CancelOrder $ord)
    {
        if (CancelOrder::ORDER_CANCEL_ACK !== $ord->txStatus) {
            throw new \Exception('状态异常');
        }
        $trans_resp = Yii::$container->get('ump')->getOrderInfo($ord);
        if ($trans_resp->isSuccessful()) {
            if ('2' === $trans_resp->get('tran_state')) {
                $ord->txStatus = CancelOrder::ORDER_CANCEL_SUCCESS;
            } elseif ('0' !== $trans_resp->get('tran_state')) {
                $ord->txStatus = CancelOrder::ORDER_CANCEL_FAIL;
            }
            $ord->save(false);

            return $ord;
        } else {
            throw new \Exception('联动一侧异常');
        }
    }
}
