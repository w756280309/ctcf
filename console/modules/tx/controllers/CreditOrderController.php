<?php

namespace console\modules\tx\controllers;

use common\models\tx\FinUtils;
use Tx\UmpClient as Client;
use common\models\order\BaoQuanQueue;
use common\models\tx\CreditNote;
use common\models\tx\CreditOrder;
use common\models\epay\EpayUser as FcUser;
use common\models\user\MoneyRecord;
use common\models\order\OnlineRepaymentPlan as RepaymentPlan;
use common\models\tx\SmsMessage;
use common\models\TradeLog;
use common\models\tx\Transfer;
use common\models\user\User;
use common\models\user\UserAccount;
use common\models\tx\UserAsset;
use common\models\user\UserInfo;
use common\models\tx\UserManager;
use Tx\PromoClient;
use Yii;
use yii\console\Controller;
use yii\helpers\ArrayHelper;

class CreditOrderController extends Controller
{
    //处理订单
    public function actionConfirm()
    {
        $umpClient = new Client(Yii::$app->params['ump']);
        $promoClient = new PromoClient(Yii::$app->params['promo_api_url']);
        $orders = CreditOrder::find()
            ->where(['status' => CreditOrder::STATUS_INIT])
            ->orderBy(['id' => SORT_ASC])
            ->limit(10)
            ->all();

        if (count($orders) > 0) {
            foreach ($orders as $order) {
                try {
                    \Yii::trace('============ 订单处理 订单ID:'.$order->id.PHP_EOL, 'credit_order');
                    $note = CreditNote::findOne($order->note_id);
                    if (null === $note) {
                        throw new \Exception('订单所属资产及债权未找到');
                    }
                    //债权超投
                    if (bccomp(bcadd($note->tradedAmount, $order->principal, 0), $note->amount, 0) > 0) {
                        $order->status = CreditOrder::STATUS_FAIL;
                        $order->save(false);
                        continue;
                    }

                    if ($order->buyerPaymentStatus === 0) {
                        $this->dealOrderPay($umpClient, $order);    //买方付款
                    }
                    $this->dealOrder($order);   //做订单成功处理流程
                    if ($order->sellerRefundStatus === 0) {
                        $this->dealOrderRepayment($umpClient, $order);  //执行卖家回款
                    }
                    if ($order->feeTransferStatus === 0) {
                        $this->dealOrderFee($umpClient, $order);    //执行手续费扣除
                    }

                    if (
                        in_array($order->status, [CreditOrder::STATUS_INIT, CreditOrder::STATUS_OTHER])
                        && $order->buyerPaymentStatus === 1
                        && $order->sellerRefundStatus === 1
                        && $order->feeTransferStatus === 1
                    ) {
                        $order->status = CreditOrder::STATUS_SUCCESS;
                        $order->save(false);
                        $this->insertBuyerBaoQuanQueue($order);//债权订单成功之后添加保全合同队列
                        //订单成功后更新当前购买人与转让人的持有数量
                        $this->updateSellerAndBuyerAmount($order);

                        //向温都金服发送转让成功消息，用于promo1212发红包
                        try {
                            $promoClient->send([
                                'id' => $order->user_id,
                            ]);
                        } catch (\Exception $ex) {

                        }
                    }
                    \Yii::trace('订单处理-结束;状态：成功'.PHP_EOL.PHP_EOL, 'credit_order');
                } catch (\Exception $ex) {
                    if (CreditOrder::STATUS_SUCCESS !== $order->status) {   //如果订单成功,则不再修改其转让订单状态,例如易宝全数据库操作异常
                        $order->status = CreditOrder::STATUS_OTHER;
                        $order->save(false);
                        \Yii::trace('订单处理-结束;状态：失败;失败信息:'.$ex->getMessage().PHP_EOL.PHP_EOL, 'credit_order');
                    }
                }
            }
        } else {
            sleep(3);
        }
        exit(0);
    }

    //处理异常订单
    public function actionCheck()
    {
        $date = date('Y-m-d', strtotime('-3day'));
        $umpClient = new Client(Yii::$app->params['ump']);
        $promoClient = new PromoClient(Yii::$app->params['promo_api_url']);
        $orders = CreditOrder::find()
            ->where(['status' => CreditOrder::STATUS_OTHER])
            ->andWhere(['>', 'createTime', $date])
            ->orderBy(['id' => SORT_ASC])
            ->limit(10)
            ->all();

        if (count($orders) > 0) {
            foreach ($orders as $order) {
                try {
                    \Yii::trace('============ 订单处理(异常订单) 订单ID:'.$order->id.PHP_EOL, 'credit_order');
                    $note = CreditNote::findOne($order->note_id);
                    if (null === $note) {
                        throw new \Exception('订单所属资产及债权未找到');
                    }
                    //债权超投
                    if (bccomp(bcadd($note->tradedAmount, $order->principal, 0), $note->amount, 0) > 0) {
                        $order->status = CreditOrder::STATUS_FAIL;
                        $order->save(false);
                        //异常订单中超投处理的回滚
                        if (in_array($order->buyerPaymentStatus, [1, 3])) {
                            $this->rollbackPay($order, $umpClient);
                        }
                        continue;
                    }

                    if ($order->buyerPaymentStatus === 3) {
                        $this->checkOrderPay($umpClient, $order);//处理异常订单
                    }
                    $this->dealOrder($order);   //做订单成功处理流程
                    if (in_array($order->sellerRefundStatus, [0, 2])) {
                        $this->dealOrderRepayment($umpClient, $order);  //执行卖家回款
                    } elseif ($order->sellerRefundStatus === 3) {
                        $this->checkOrderRefund($umpClient, $order);//处理异常订单
                    }
                    if (in_array($order->feeTransferStatus, [0, 2])) {
                        $this->dealOrderFee($umpClient, $order);    //执行手续费扣除
                    } elseif ($order->feeTransferStatus === 3) {
                        $this->checkOrderFee($umpClient, $order);//处理异常订单
                    }

                    if (
                        in_array($order->status, [CreditOrder::STATUS_INIT, CreditOrder::STATUS_OTHER])
                        && $order->buyerPaymentStatus === 1
                        && $order->sellerRefundStatus === 1
                        && $order->feeTransferStatus === 1
                    ) {
                        $order->status = CreditOrder::STATUS_SUCCESS;
                        $order->save(false);
                        $this->insertBuyerBaoQuanQueue($order);//债权订单成功之后添加保全合同队列
                        //订单成功后更新当前购买人与转让人的持有数量
                        $this->updateSellerAndBuyerAmount($order);

                        //向温都金服发送转让成功消息，用于promo1212发红包
                        try {
                            $promoClient->send([
                                'id' => $order->user_id,
                            ]);
                        } catch (\Exception $ex) {

                        }
                    }
                    \Yii::trace('订单处理-结束;状态：成功'.PHP_EOL.PHP_EOL, 'credit_order');
                } catch (\Exception $ex) {
                    if (CreditOrder::STATUS_SUCCESS !== $order->status) {   //如果订单成功,则不再修改其转让订单状态,例如易宝全数据库操作异常
                        $order->status = CreditOrder::STATUS_OTHER;
                        $order->save(false);
                        \Yii::trace('订单处理-结束;状态：失败;失败信息:'.$ex->getMessage().PHP_EOL.PHP_EOL, 'credit_order');
                    }
                }
            }
        } else {
            usleep(500000);
        }
        exit(0);
    }

    //回滚买方支付
    private function rollbackPay(CreditOrder $order, Client $umpClient)
    {
        if (
            $order->status === CreditOrder::STATUS_OTHER
            && in_array($order->buyerPaymentStatus, [1, 3])
        ) {
            $asset = $order->asset;
            $transfer = Transfer::find()->where([
                'type' => 'buy_note',
                'sourceType' => CreditOrder::tableName(),
                'sourceTxSn' => $order->id,
                'fromAccount' => $order->user_id,
                'toAccount' => $asset->loan_id,
            ])->one();
            if (empty($transfer)) {
                throw  new \Exception('没有找到买方支付记录');
            }
            $umpRequestData = [
                'sn' => $transfer->sn,
                'date' => strtotime($transfer->createTime),
            ];
            $res = $umpClient->getOrderInfo($umpRequestData);
            if ($res['ret_code'] === '0000') {
                if ($res['tran_state'] === '2') {
                    $transaction = Yii::$app->db_tx->beginTransaction();
                    try {
                        $fcUser = $order->fcUser;
                        $time1 = time();
                        $transfer = Transfer::initByCreditOrder($order, $order->amount, $asset->loan_id, $order->user_id, 'buy_note_rollback');
                        $umpRequestData = [
                            'sn' => $transfer->sn,
                            'date' => time(),
                            'loanId' => $asset->loan_id,
                            'fcUserId' => $fcUser->epayUserId,
                            'amount' => $order->amount,
                        ];
                        $res = $umpClient->noteFangkuan($umpRequestData);

                        $time2 = time();
                        $tradeLog = new TradeLog([
                            'txType' => 'project_transfer',
                            'direction' => 2,
                            'txSn' => $order->id,
                            'uid' => $order->user_id,
                            'requestData' => json_encode($umpRequestData),
                            'rawRequest' => json_encode($umpRequestData),
                            'responseCode' => $res['ret_code'],
                            'rawResponse' => json_encode($res),
                            'responseMessage' => $res['ret_msg'],
                            'duration' => $time2 - $time1,
                            'txDate' => date('Y-m-d H:i:s'),
                        ]);
                        $tradeLog->save(false);

                        if ($res['ret_code']  === '0000') {
                            $order->buyerPaymentStatus = 4;
                            $order->save(false);
                            $transfer->status = Transfer::STATUS_SUCCESS;
                            $transfer->save(false);
                            \Yii::trace('订单处理-买方支付：支付成功'.PHP_EOL, 'credit_order');
                        } elseif ($res['ret_code'] !== '00240000') {
                            $this->backMaxTradableAmount($order);
                            $order->buyerPaymentStatus = 5;
                            $order->save(false);
                            $transfer->status = Transfer::STATUS_FAIL;
                            $transfer->save(false);
                            \Yii::trace('订单处理-买方支付：支付失败'.PHP_EOL, 'credit_order');
                        } else {
                            $transfer->status = Transfer::STATUS_OTHER;
                            $transfer->save(false);
                            $order->buyerPaymentStatus = 6;
                            $order->save(false);
                            \Yii::trace('订单处理-买方支付：[[[支付异常]]]'.PHP_EOL, 'credit_order');
                        }
                        $transaction->commit();
                    } catch (\Exception $ex) {
                        $transaction->rollBack();
                        \Yii::trace('订单处理-买方支付：支付失败;失败信息：'.$ex->getMessage().PHP_EOL, 'credit_order');
                        throw $ex;
                    }
                }
            }
        }
    }

    //回滚温都数据库(暂时未用到)
    private function rollbackWdjf(CreditOrder $order)
    {
        $transaction = Yii::$app->db_tx->beginTransaction();
        try {
            $asset = $order->asset;
            $principal = bcdiv($order->principal, 100, 2);//以元为单位的购买本金
            $amount = bcdiv($order->amount, 100, 2);//以元为单位的实际支付金额
            $fee = bcdiv($order->fee, 100, 2);//以元为单位的手续费
            $interest = bcdiv($order->interest, 100, 2);//以元为单位的应付利息

            $userAccount = UserAccount::find()->where(['uid' => $order->user_id])->one();

            if (null === $userAccount) {
                throw new \Exception('没有找到用户账户');
            }

            //恢复温都买方账号信息
            $userAccount->available_balance = bcadd($userAccount->available_balance, $amount, 2);
            $userAccount->investment_balance = bcsub($userAccount->investment_balance, $principal, 2);
            $userAccount->save(false);

            //删除买方支付资金流水
            $moneyRecord = MoneyRecord::find()->where([
                'account_id' => $userAccount->id,
                'type' => MoneyRecord::TYPE_CREDIT_NOTE,
                'osn' => $order->id,
                'uid' => $order->user_id,
                'out_money' => $amount,
            ])->one();
            if (null === $moneyRecord) {
                throw new \Exception('没有找到买方支付对应的资金流水');
            }
            $moneyRecord->delete();

            //更改温都买方账户信息
            $userAccount = UserAccount::find()->where(['uid' => $asset->user_id])->one();
            $userAccount->available_balance = bcsub($userAccount->available_balance, $amount, 2);
            $userAccount->profit_balance = bcsub($userAccount->profit_balance, $interest, 2);
            $userAccount->investment_balance = bcadd($userAccount->investment_balance, $principal, 2);
            $userAccount->save(false);
            //删除卖方回款资金流水
            $moneyRecord = MoneyRecord::find()->where([
                'account_id' => $userAccount->id,
                'type' => MoneyRecord::TYPE_CREDIT_REPAID,
                'osn' => $order->id,
                'uid' => $asset->user_id,
                'in_money' => $amount,
            ])->one();
            if (null === $moneyRecord) {
                throw new \Exception('没有找到卖方回款对应的资金流水');
            }
            $moneyRecord->delete();

            //恢复卖方账户信息
            $userAccount = UserAccount::find()->where(['uid' => $asset->user_id])->one();
            $userAccount->available_balance = bcadd($userAccount->available_balance, $fee, 2);
            $userAccount->save(false);
            //删除手续费资金流水
            $moneyRecord = MoneyRecord::find()->where([
                'account_id' => $userAccount->id,
                'type' => MoneyRecord::TYPE_CREDIT_NOTE_FEE,
                'osn' => $order->id,
                'uid' => $asset->user_id,
                'out_money' => $fee,
            ])->one();
            if (null === $moneyRecord) {
                throw new \Exception('没有找到卖方手续费对应的资金流水');
            }
            $moneyRecord->delete();

            //9)更改原还款计划添加新还款计划
            $this->rollbackRepaymentPlan($order);
            $transaction->commit();
        } catch (\Exception $ex) {
            $transaction->rollBack();
            throw $ex;
        }
    }

    //回滚还款计划(暂时未用到)
    private function rollbackRepaymentPlan(CreditOrder $order)
    {
        Yii::trace('订单处理-回滚还款计划,订单ID：'.$order->id.PHP_EOL, 'credit_order');

        $principal = bcdiv($order->principal, 100, 2);//实际购买本金，以元为单位
        Yii::trace('订单本金:'.$principal.PHP_EOL, 'credit_order');

        $asset = UserAsset::findOne($order->asset_id);

        if (null === $asset) {
            throw new \Exception('没有找到资产信息');
        }
        $loan = $asset->loan;
        $loanOrder = $asset->order;

        $assetAmount = bcdiv($asset->amount, 100, 2);//资产的剩余金额，以元为单位
        Yii::trace('原资产购买之后剩余金额:'.$assetAmount.PHP_EOL, 'credit_order');

        $query = RepaymentPlan::find()
            ->where([
                'online_pid' => $asset->loan_id,
                'order_id' => $asset->order_id,
                'uid' => $asset->user_id,
            ])
            ->andWhere(['>', 'refund_time', strtotime($order->createTime)]);

        if ($asset->hasTransferred()) {
            $query->andWhere(['asset_id' => $asset->id]);
        } else {
            $query->andWhere(['asset_id' => null]);
        }

        $repayments = $query->all();

        $count = count($repayments);
        Yii::trace('原资产还款计划数量:'.$count.PHP_EOL, 'credit_order');

        if ($count <= 0) {
            throw new \Exception('没找到待还款的还款计划');
        }

        $newAsset = UserAsset::find()->where(['credit_order_id' => $order->id])->one();
        if (null === $newAsset) {    //判断订单对应的新增用户资产是否存在
            throw new \Exception('未找到新建的订单用户资产');
        }

        //回滚还款计划（删除新建还款计划，恢复更改过的还款计划，添加删除的还款计划）
        if (bccomp($assetAmount, 0, 0) > 0) {
            $newRepayments = $loan->getRepaymentPlan($order->principal, $loanOrder->apr);
            foreach ($newRepayments as $key => $newRepayment) {
                if ($newRepayment['date'] <= $order->createTime) {
                    unset($newRepayments[$key]);
                }
            }
            $newRepayments = array_values($newRepayments);
            $newCount = count($newRepayments);
            Yii::trace('用订单金额新生成还款计划期数:'.$newCount.PHP_EOL, 'credit_order');
            if ($newCount !== $count) {
                throw new \Exception('新还款计划期数和旧回款计划期数不一样');
            }
            $newTotalLixi = array_sum(array_column($newRepayments, 'interest'));//新还款计划的总利息
            Yii::trace('新还款计划总利息:'.$newTotalLixi.PHP_EOL, 'credit_order');
            Yii::trace('原资产未被购买完，更新旧还款计划，生成新还款计划；还原旧还款计划，删除新还款计划:'.PHP_EOL, 'credit_order');
            $totalLixi = 0;//新还款计划累计金额
            //删除新还款计划
            $newRepaymentPlans = RepaymentPlan::find()->where([
                'uid' => $order->user_id,
                'asset_id' => $newAsset->id,
            ])->all();
            if (empty($newRepaymentPlans)) {
                throw new \Exception('没有找到购买债权新建的还款计划');
            }
            foreach ($newRepaymentPlans as $plan) {
                $plan->delete();
            }
            foreach ($repayments as $key => $repayment) {
                if ($key === $count - 1) {
                    $newLixi = bcsub($newTotalLixi, $totalLixi, 2);
                    Yii::trace('新还款计划利息:'.$newLixi.PHP_EOL, 'credit_order');
                    $repayment->benjin = bcadd($assetAmount, $principal, 2);
                } else {
                    $newLixi = $newRepayments[$key]['interest'];
                    Yii::trace('新还款计划利息:'.$newLixi.PHP_EOL, 'credit_order');
                    $repayment->benjin = 0;
                }
                $totalLixi = bcadd($totalLixi, $newLixi, 2);

                $repayment->lixi = bcadd($repayment->lixi, $newLixi, 2);
                $repayment->benxi = bcadd($repayment->lixi, $repayment->benjin, 2);
                $repayment->save(false);
                Yii::trace('原还款计划ID'.$repayment->id.'本金:'.$repayment->benjin.';利息:'.$repayment->lixi.';user_id:'.$repayment->uid.';loan_id:'.$repayment->online_pid.';order_id'.$repayment->order_id.PHP_EOL, 'credit_order');
            }
        } else {
            foreach ($repayments as $repayment) {
                Yii::trace('原资产被买完，有新生成还款计划，需要删除新还款计划，还原原还款计划:'.PHP_EOL, 'credit_order');
                $newRepayment = new RepaymentPlan();
                $attributes = $repayment->getAttributes();
                unset($attributes['id']);
                $newRepayment->setAttributes($attributes, false);
                $newRepayment->sn = FinUtils::generateSn('HP');
                $newRepayment->uid = $asset->user_id;
                if ($asset->note_id) {
                    $newRepayment->asset_id = $asset->id;//原资产是购买债权得到的
                } else {
                    $newRepayment->asset_id = null;  //原资产是购买标的得到的
                }

                $newRepayment->save(false);

                Yii::trace('还原的还款计划'.$newRepayment->id.'本金:'.$newRepayment->benjin.';利息:'.$newRepayment->lixi.PHP_EOL, 'credit_order');
                //删除原还款计划
                $repayment->delete();
            }
        }
    }

    //处理买方支付异常订单
    private function checkOrderPay(Client $umpClient, CreditOrder $order)
    {
        if ($order->buyerPaymentStatus === 3) {
            $transfer = Transfer::find()->where(['type' => 'buy_note', 'sourceType' => CreditOrder::tableName(), 'sourceTxSn' => $order->id])->one();
            if (empty($transfer)) {
                throw  new \Exception('没有找到买方支付记录');
            }
            $umpRequestData = [
                'sn' => $transfer->sn,
                'date' => strtotime($transfer->createTime),
            ];
            $transaction = Yii::$app->db_tx->beginTransaction();
            try {
                $res = $umpClient->getOrderInfo($umpRequestData);
                if ($res['ret_code'] === '0000') {
                    if ($res['tran_state'] === '2') {
                        //交易成功
                        $order->buyerPaymentStatus = 1;
                        $transfer->status = Transfer::STATUS_SUCCESS;
                    } elseif (in_array($res['tran_state'], ['3', '5'])) {
                        //交易失败或关闭
                        $order->buyerPaymentStatus = 2;
                        $order->status = CreditOrder::STATUS_FAIL;
                        $transfer->status = Transfer::STATUS_FAIL;
                    } else {
                        return;
                    }
                    $order->save(false);
                    $transfer->save(false);
                    \Yii::trace('订单处理-买方支付(处理异常支付状态)：支付'.($res['tran_state'] === '2' ? '成功' : '失败').PHP_EOL, 'credit_order');
                    $transaction->commit();
                }
            } catch (\Exception $ex) {
                $transaction->rollBack();
                \Yii::trace('订单处理-买方支付(处理异常支付状态)：处理失败;失败信息：'.$ex->getMessage().PHP_EOL, 'credit_order');
                throw $ex;
            }
        }
    }

    //处理卖方回款异常订单
    private function checkOrderRefund(Client $umpClient, CreditOrder $order)
    {
        if ($order->sellerRefundStatus === 3) {
            $transfer = Transfer::find()->where(['type' => 'note_fangkuan', 'sourceType' => CreditOrder::tableName(), 'sourceTxSn' => $order->id])->one();
            if (empty($transfer)) {
                throw  new \Exception('没有找到卖方回款记录');
            }
            $umpRequestData = [
                'sn' => $transfer->sn,
                'date' => strtotime($transfer->createTime),
            ];
            $transaction = Yii::$app->db_tx->beginTransaction();
            try {
                $res = $umpClient->getOrderInfo($umpRequestData);
                if ($res['ret_code'] === '0000') {
                    if ($res['tran_state'] === '2') {
                        //交易成功
                        $order->sellerRefundStatus = 1;
                        $transfer->status = Transfer::STATUS_SUCCESS;
                    } elseif (in_array($res['tran_state'], ['3', '5'])) {
                        //交易失败或关闭
                        $order->sellerRefundStatus = 2;
                        $transfer->status = Transfer::STATUS_FAIL;
                    } else {
                        return;
                    }
                    $order->save(false);
                    $transfer->save(false);
                    \Yii::trace('订单处理-卖方回款(处理异常支付状态)：回款'.($res['tran_state'] === '2' ? '成功' : '失败').PHP_EOL, 'credit_order');
                    $transaction->commit();
                }
            } catch (\Exception $ex) {
                $transaction->rollBack();
                \Yii::trace('订单处理-卖方回款(处理异常支付状态)：处理失败;失败信息：'.$ex->getMessage().PHP_EOL, 'credit_order');
                throw $ex;
            }
        }
    }

    //处理手续费异常订单
    private function checkOrderFee(Client $umpClient, CreditOrder $order)
    {
        if ($order->feeTransferStatus === 3) {
            $transfer = Transfer::find()->where(['type' => 'note_fee', 'sourceType' => CreditOrder::tableName(), 'sourceTxSn' => $order->id])->one();
            if (empty($transfer)) {
                throw  new \Exception('没有找到手续费记录');
            }
            $umpRequestData = [
                'sn' => $transfer->sn,
                'date' => strtotime($transfer->createTime),
            ];
            $transaction = Yii::$app->db_tx->beginTransaction();
            try {
                $res = $umpClient->getOrderInfo($umpRequestData);
                if ($res['ret_code'] === '0000') {
                    if ($res['tran_state'] === '2') {
                        //交易成功
                        $order->feeTransferStatus = 1;
                        $transfer->status = Transfer::STATUS_SUCCESS;
                    } elseif (in_array($res['tran_state'], ['3', '5'])) {
                        //交易失败或关闭
                        $order->feeTransferStatus = 2;
                        $transfer->status = Transfer::STATUS_FAIL;
                    } else {
                        return;
                    }
                    $order->save(false);
                    $transfer->save(false);
                    \Yii::trace('订单处理-手续费(处理异常支付状态)：扣除'.($res['tran_state'] === '2' ? '成功' : '失败').PHP_EOL, 'credit_order');
                    $transaction->commit();
                }
            } catch (\Exception $ex) {
                $transaction->rollBack();
                \Yii::trace('订单处理-手续费(处理异常支付状态)：处理失败;失败信息：'.$ex->getMessage().PHP_EOL, 'credit_order');
                throw $ex;
            }
        }
    }

    /**
     * 更新原还款计划，生成新还款计划.
     */
    private function updateRepaymentPlan(CreditOrder $order, UserAsset $newAsset)
    {
        Yii::trace('更新还款计划,订单ID：'.$order->id.PHP_EOL, 'credit_order');

        $principal = bcdiv($order->principal, 100, 2);//实际购买本金，以元为单位
        Yii::trace('实际购买本金:'.$principal.PHP_EOL, 'credit_order');

        $asset = UserAsset::findOne($order->asset_id);

        if (null === $asset) {
            throw new \Exception('没有找到资产信息');
        }
        $loan = $asset->loan;
        $loanOrder = $asset->order;

        $assetAmount = bcdiv($asset->amount, 100, 2);//资产的剩余金额，以元为单位
        Yii::trace('原资产购买之后剩余金额:'.$assetAmount.PHP_EOL, 'credit_order');

        $query = RepaymentPlan::find()
            ->where([
                'online_pid' => $asset->loan_id,
                'order_id' => $asset->order_id,
                'uid' => $asset->user_id,
            ])
            ->andWhere(['>', 'refund_time', strtotime($order->createTime)]);

        if ($asset->hasTransferred()) {
            $query->andWhere(['asset_id' => $asset->id]);
        } else {
            $query->andWhere(['asset_id' => null]);
        }

        $repayments = $query->all();
        Yii::trace('查找回款计划；user_id:'.$asset->user_id.';loan_id:'.$asset->loan_id.';order_id:'.$asset->order_id.PHP_EOL, 'credit_order');

        $count = count($repayments);
        Yii::trace('原资产还款计划数量:'.$count.PHP_EOL, 'credit_order');

        if ($count <= 0) {
            throw new \Exception('没找到待还款的还款计划');
        }

        $previousAmount = $repayments[$count - 1]['benjin'];
        Yii::trace('原还款计划本金:'.$previousAmount.PHP_EOL, 'credit_order');
        Yii::trace('删除原还款计划逻辑判断结果:'.bccomp($assetAmount, 0, 0), 'credit_order');

        if (null === $newAsset) {    //判断订单对应的新增用户资产是否存在
            throw new \Exception('未找到新建的订单用户资产');
        }

        //9)更新旧还款计划，生成新还款计划
        if (bccomp($assetAmount, 0, 0) > 0) {
            $previousTotalLixi = array_sum(ArrayHelper::getColumn($repayments, 'lixi'));//原总利息
            Yii::trace('原还款计划剩余总利息:'.$previousTotalLixi.PHP_EOL, 'credit_order');
            $newRepayments = $loan->getRepaymentPlan($order->principal, $loanOrder->apr);
            foreach ($newRepayments as $key => $newRepayment) {
                if ($newRepayment['date'] <= $order->createTime) {
                    unset($newRepayments[$key]);
                }
            }
            $newRepayments = array_values($newRepayments);
            $newCount = count($newRepayments);
            Yii::trace('用订单金额新生成还款计划期数:'.$newCount.PHP_EOL, 'credit_order');
            if ($newCount !== $count) {
                throw new \Exception('新还款计划期数和旧回款计划期数不一样');
            }
            $newTotalLixi = array_sum(array_column($newRepayments, 'interest'));//新还款计划的总利息
            Yii::trace('新还款计划总利息:'.$newTotalLixi.PHP_EOL, 'credit_order');

            $totalLixi = 0;//新还款计划累计金额
            //更改原还款计划
            foreach ($repayments as $key => $repayment) {
                Yii::trace('原资产未被购买完，更新旧还款计划，生成新还款计划:'.PHP_EOL, 'credit_order');

                //新建新的还款计划
                $newRepayment = new RepaymentPlan();
                $attributes = $repayment->getAttributes();
                unset($attributes['id']);
                $newRepayment->setAttributes($attributes, false);
                $newRepayment->sn = FinUtils::generateSn('HP');
                $newRepayment->uid = $order->user_id;
                if ($key === $count - 1) {
                    $newLixi = bcsub($newTotalLixi, $totalLixi, 2);
                    Yii::trace('新还款计划利息:'.$newLixi.PHP_EOL, 'credit_order');
                } else {
                    $newLixi = $newRepayments[$key]['interest'];
                    Yii::trace('新还款计划利息:'.$newLixi.PHP_EOL, 'credit_order');
                }
                $totalLixi = bcadd($totalLixi, $newLixi, 2);
                $newRepayment->lixi = $newLixi;
                $newRepayment->asset_id = $newAsset->id;  //将订单对应的新增用户资产ID存入还款计划中
                if ($key === $count - 1) {
                    //最后一期
                    $newRepayment->benjin = $principal;
                    $repayment->benjin = $assetAmount;
                } else {
                    $repayment->benjin = 0;
                    $newRepayment->benjin = 0;
                }
                $newRepayment->benxi = bcadd($newRepayment->lixi, $newRepayment->benjin, 2);
                $newRepayment->save(false);

                Yii::trace('新还款计划ID'.$newRepayment->id.'本金:'.$newRepayment->benjin.';利息:'.$newRepayment->lixi.PHP_EOL, 'credit_order');
                //更新原有还款计划
                $repayment->lixi = bcsub($repayment->lixi, $newLixi, 2);
                $repayment->benxi = bcadd($repayment->lixi, $repayment->benjin, 2);
                $repayment->save(false);
                Yii::trace('原还款计划ID'.$repayment->id.'本金:'.$repayment->benjin.';利息:'.$repayment->lixi.';user_id:'.$repayment->uid.';loan_id:'.$repayment->online_pid.';order_id'.$repayment->order_id.PHP_EOL, 'credit_order');
            }
        } else {
            foreach ($repayments as $repayment) {
                Yii::trace('原资产被买完，删除原还款计划，生成新还款计划:'.PHP_EOL, 'credit_order');
                //新建新的还款计划
                $newRepayment = new RepaymentPlan();
                $attributes = $repayment->getAttributes();
                unset($attributes['id']);
                $newRepayment->setAttributes($attributes, false);
                $newRepayment->sn = FinUtils::generateSn('HP');
                $newRepayment->uid = $order->user_id;
                $newRepayment->asset_id = $newAsset->id;  //将订单对应的新增用户资产ID存入还款计划中
                $newRepayment->save(false);

                Yii::trace('新还款计划ID'.$newRepayment->id.'本金:'.$newRepayment->benjin.';利息:'.$newRepayment->lixi.PHP_EOL, 'credit_order');
                //删除原还款计划
                $repayment->delete();
            }

            $this->updateAsset($asset);  //当用户资产全部转出,并且原有订单还存留有还款计划时,修改该资产为已回款,否则标记该记录失效
        }
    }

    /**
     * 当用户资产全部转出,并且原有订单还存留有还款计划时,修改该资产为已回款,否则标记该记录失效.
     */
    private function updateAsset(UserAsset $asset)
    {
        if (bccomp($asset->amount, 0, 0) === 0) {
            $cond = [
                'online_pid' => $asset->loan_id,
                'uid' => $asset->user_id,
            ];

            if ($asset->note_id !== null) {
                $cond['asset_id'] = $asset->id;
            } else {
                $cond['order_id'] = $asset->order_id;
            }

            $count = RepaymentPlan::find()
                ->where($cond)
                ->count();

            if (!$count) {
                $asset->isInvalid = true;
            } else {
                $asset->isRepaid = true;
            }

            $asset->save(false);
        }
    }

    //订单完成之后添加买方保全队列
    private function insertBuyerBaoQuanQueue(CreditOrder $order)
    {
        $queue = new BaoQuanQueue([
           'status' => BaoQuanQueue::STATUS_SUSPEND,
            'itemId' => $order->id,
            'itemType' => BaoQuanQueue::TYPE_CREDIT_ORDER,
        ]);
        $queue->save(false);
    }

    //债权债权满标之后添加卖方保全队列
    private function insertSellerBaoQuanQueue(CreditNote $creditNote)
    {
        $queue = new BaoQuanQueue([
            'status' => BaoQuanQueue::STATUS_SUSPEND,
            'itemId' => $creditNote->id,
            'itemType' => BaoQuanQueue::TYPE_CREDIT_NOTE,
        ]);
        $queue->save(false);
    }

    /**
     * 买方支付完成之后，卖方还未回款之前更新数据库.
     *
     * @throws \Exception
     */
    private function dealOrder(CreditOrder $order)
    {
        if (
           in_array($order->status, [CreditOrder::STATUS_INIT, CreditOrder::STATUS_OTHER])
           && $order->buyerPaymentStatus === 1
           && $order->sellerRefundStatus === 0
        ) {
            //更新交易系统
            $transaction = Yii::$app->db_tx->beginTransaction();
            try {
                $asset = $order->asset;
                //1)更新原资产信息
                $txRes = $this->updateTxWithOrder($order, $asset);
                $isNoteFull = $txRes['isNoteFull'];
                $newAsset = $txRes['newAsset'];
                $creditNote = $txRes['creditNote'];
                $transaction->commit();
                \Yii::trace('订单处理-更新交易系统数据库：更新成功'.PHP_EOL, 'credit_order');
            } catch (\Exception $ex) {
                $transaction->rollBack();
                \Yii::trace('订单处理-更新交易系统数据库：更新失败;失败信息'.$ex->getMessage().PHP_EOL, 'credit_order');
                throw $ex;
            }

            $transaction = \Yii::$app->db->beginTransaction();
            try {
                //----更改温都表-----
                $this->updateWdjfWithOrder($order, $asset, $newAsset);
                $transaction->commit();
                Yii::trace('订单处理-更新温都数据库：更新成功'.PHP_EOL, 'credit_order');
            } catch (\Exception $ex) {
                $transaction->rollBack();
                Yii::trace('订单处理-更新温都数据库：更新失败;错误信息：'.$ex->getMessage().PHP_EOL, 'credit_order');
                //回滚交易系统数据库
                $this->rollBackTx($order, $newAsset);
                throw $ex;
            }
            //当债权满标时候添加保全队列（转让卖方）
            if ($isNoteFull) {
                $this->insertSellerBaoQuanQueue($creditNote);
            }
            //当债权满标时发送短信
            if ($isNoteFull) {
                $user = User::findOne($creditNote->user_id);
                if (null === $user) {
                    throw new \Exception('用户信息不存在');
                }
                $this->sendSmsForNoteFull($user, $creditNote);
            }
        }
    }

    //订单成功更新温都金服数据库
    private function updateWdjfWithOrder(CreditOrder $order, UserAsset $asset, UserAsset $newAsset)
    {
        $principal = bcdiv($order->principal, 100, 2);//以元为单位的购买本金
        $amount = bcdiv($order->amount, 100, 2);//以元为单位的实际支付金额
        $fee = bcdiv($order->fee, 100, 2);//以元为单位的手续费
        $interest = bcdiv($order->interest, 100, 2);//以元为单位的应付利息

        $userAccount = UserAccount::find()->where(['uid' => $order->user_id])->one();

        if (null === $userAccount) {
            throw new \Exception('没有找到用户账户');
        }

        //5)更改温都账户信息
        $userAccount->available_balance = bcsub($userAccount->available_balance, $amount, 2);
        $userAccount->investment_balance = bcadd($userAccount->investment_balance, $principal, 2);
        $userAccount->save(false);

        //6)添加温都资金流水（买方付款资金流水）
        $sn = FinUtils::generateSn('MR');
        $moneyRecord = new MoneyRecord([
            'account_id' => $userAccount->id,
            'sn' => $sn,
            'type' => MoneyRecord::TYPE_CREDIT_NOTE,
            'osn' => $order->id,
            'uid' => $order->user_id,
            'balance' => $userAccount->available_balance,
            'out_money' => $amount,
            'remark' => '购买债权。资金流水号:'.$sn.',债权订单ID:'.($order->id).',可用余额:'.($userAccount->available_balance).'元。',
        ]);
        $moneyRecord->save(false);

        //7)添加温都资金流水（卖方回款资金流水）
        $userAccount = UserAccount::find()->where(['uid' => $asset->user_id])->one();
        $userAccount->available_balance = bcadd($userAccount->available_balance, $amount, 2);
        $userAccount->profit_balance = bcadd($userAccount->profit_balance, $interest, 2);
        $userAccount->investment_balance = bcsub($userAccount->investment_balance, $principal, 2);
        $userAccount->save(false);

        $sn = FinUtils::generateSn('MR');
        $moneyRecord = new MoneyRecord([
            'account_id' => $userAccount->id,
            'sn' => $sn,
            'type' => MoneyRecord::TYPE_CREDIT_REPAID,
            'osn' => $order->id,
            'uid' => $asset->user_id,
            'balance' => $userAccount->available_balance,
            'in_money' => $amount,
            'remark' => '债权返款；资金流水号:'.$sn.',债权订单ID:'.($order->id).',可用余额:'.($userAccount->available_balance).'元。',
        ]);
        $moneyRecord->save(false);

        //8)添加温都资金流水（手续费资金流水）
        $userAccount = UserAccount::find()->where(['uid' => $asset->user_id])->one();
        $userAccount->available_balance = bcsub($userAccount->available_balance, $fee, 2);
        $userAccount->save(false);

        $sn = FinUtils::generateSn('MR');
        $moneyRecord = new MoneyRecord([
            'account_id' => $userAccount->id,
            'sn' => $sn,
            'type' => MoneyRecord::TYPE_CREDIT_NOTE_FEE,
            'osn' => $order->id,
            'uid' => $asset->user_id,
            'balance' => $userAccount->available_balance,
            'out_money' => $fee,
            'remark' => '债权手续费；资金流水号:'.$sn.',债权订单ID:'.($order->id).',可用余额:'.($userAccount->available_balance).'元。',
        ]);
        $moneyRecord->save(false);

        //9)更改原还款计划添加新还款计划
        $this->updateRepaymentPlan($order, $newAsset);

        //10)更新用户转让购买信息
        $this->updateBuyerCreditInfo($order);
    }

    private function updateBuyerCreditInfo($order)
    {
        if (CreditOrder::STATUS_SUCCESS === $order->status) {
            $sql = "update user_info set creditInvestCount=creditInvestCount+1, creditInvestTotal=creditInvestTotal+:creditInvestTotal where user_id = :userId";
            $affectedRows = \Yii::$app->db->createCommand($sql, [
                'creditInvestTotal' => $order->principal/100,
                'userId' => $order->user_id,
            ])->execute();
            if (0 === $affectedRows) {
                throw new \Exception('更新用户转让购买信息失败');
            }
        }
    }

    //更新卖方用户统计信息（暂时未被调用）
    private function updateUserInfoWithOrder(CreditOrder $order)
    {
        $date = date('Y-m-d', strtotime($order->createTime));
        $principal = bcdiv($order->principal, 100, 2);//以元为单位的购买本金
        $userInfo = UserInfo::find()->where(['user_id' => $order->user_id])->one();
        if (empty($userInfo)) {
            throw new \Exception('没有找到用户信息');
        }
        $userInfo->isInvested = true;
        $userInfo->investCount = bcadd($userInfo->investCount, 1, 0);
        $userInfo->investTotal = bcadd($userInfo->investTotal, $principal, 2);
        if (empty($userInfo->firstInvestDate) || empty($userInfo->firstInvestAmount)) {
            $userInfo->firstInvestDate = $date;
            $userInfo->firstInvestAmount = $principal;
        }
        $userInfo->lastInvestDate = $date;
        $userInfo->lastInvestAmount = $principal;
        $userInfo->averageInvestAmount = bcdiv($userInfo->investTotal, $userInfo->investCount, 2);
        $userInfo->save();
    }

    //订单成功之后更新交易系统数据库
    private function updateTxWithOrder(CreditOrder $order, UserAsset $asset)
    {
        $isNoteFull = false;  //挂牌记录是否满标标志位
        Yii::$app->db_tx->createCommand(
            'UPDATE `user_asset` SET  `amount` =  `amount` - :amount WHERE id = :id',
            [
                'amount' => $order->principal,
                'id' => $order->asset_id,
            ]
        )->execute();

        //2)添加新资产
        $newAsset = UserAsset::initNew();
        $newAsset->setAttributes([
            'user_id' => $order->user_id,
            'loan_id' => $asset->loan_id,
            'asset_id' => $asset->id,
            'order_id' => $asset->order_id,
            'note_id' => $order->note_id,
            'amount' => $order->principal,
            'orderTime' => $order->createTime,
            'tradeCount' => $asset->tradeCount + 1,
            'maxTradableAmount' => $order->principal,
            'isTest' => $asset->isTest,
            'credit_order_id' => $order->id,
        ], false);

        $newAsset->save(false);

        //3）更改债权信息
        Yii::$app->db_tx->createCommand(
            'UPDATE `credit_note` SET  `tradedAmount` =  `tradedAmount` + :amount WHERE id = :id',
            [
                'amount' => $order->principal,
                'id' => $order->note_id,
            ]
        )->execute();

        //4)关闭债权
        $creditNote = CreditNote::findOne($order->note_id);

        if (null === $creditNote) {
            throw new \Exception('没有找到债权信息');
        }

        if (bccomp($creditNote->tradedAmount, $creditNote->amount, 0) === 0) {
            //债权结束
            $creditNote->isClosed = true;
            $creditNote->closeTime = date('Y-m-d H:i:s');
            $creditNote->save(false);
            $isNoteFull = true;
        }

        return ['newAsset' => $newAsset, 'isNoteFull' => $isNoteFull, 'creditNote' => $creditNote];
    }

    //回滚交易系统数据库
    private function rollBackTx(CreditOrder $order, UserAsset $newAsset)
    {
        //1)更新原资产信息
        Yii::$app->db_tx->createCommand(
            'UPDATE `user_asset` SET  `amount` =  `amount` + :amount WHERE id = :id',
            [
                'amount' => $order->principal,
                'id' => $order->asset_id,
            ]
        )->execute();

        //2)删除新资产
        $newAsset->delete();

        //3）更改债权信息
        Yii::$app->db_tx->createCommand(
            'UPDATE `credit_note` SET  `tradedAmount` =  `tradedAmount` - :amount WHERE id = :id',
            [
                'amount' => $order->principal,
                'id' => $order->note_id,
            ]
        )->execute();

        //4)关闭债权
        $creditNote = CreditNote::findOne($order->note_id);

        if (null === $creditNote) {
            throw new \Exception('没有找到债权信息');
        }

        if (bccomp($creditNote->tradedAmount, $creditNote->amount, 0) < 0) {
            //债权结束
            $creditNote->isClosed = false;
            $creditNote->closeTime = null;
            $creditNote->save(false);
        }
        Yii::trace('订单处理-回滚交易系统数据库 成功'.PHP_EOL, 'credit_order');
    }

    /**
     * 在债权满标的时候发送短信.
     */
    private function sendSmsForNoteFull(User $user, CreditNote $creditNote)
    {
        return (new SmsMessage())->initNew(
            $user,
            118096,
            [
                $creditNote->createTime,
                bcdiv($creditNote->amount, 100, 2),
            ]
        )->save();
    }

    /**
     * 购买方付款.
     *
     * @param Client $umpClient
     *
     * @throws \Exception
     */
    private function dealOrderPay(Client $umpClient, CreditOrder $order)
    {
        if (
            in_array($order->status, [CreditOrder::STATUS_INIT, CreditOrder::STATUS_OTHER])
            && $order->buyerPaymentStatus === 0
        ) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $asset = $order->asset;
                if (null === $asset) {
                    throw new \Exception('订单所属资产及债权未找到');
                }

                $fcUser = $order->fcUser;
                if (null === $fcUser) {
                    throw new \Exception('没有找到用户在联动的账户');
                }

                if (!$order->amount) {
                    throw new \Exception('转让订单交易金额为0');
                }

                $time1 = time();
                $transfer = Transfer::initByCreditOrder($order, $order->amount, $order->user_id, $asset->loan_id, 'buy_note');

                $umpRequestData = [
                    'sn' => $transfer->sn,
                    'date' => time(),
                    'loanId' => $asset->loan_id,
                    'fcUserId' => $fcUser->epayUserId,
                    'amount' => $order->amount,
                ];
                $res = $umpClient->buyNote($umpRequestData);

                $time2 = time();
                $tradeLog = new TradeLog([
                    'txType' => 'project_transfer_nopwd',
                    'direction' => 1,
                    'txSn' => $order->id,
                    'uid' => $order->user_id,
                    'requestData' => json_encode($umpRequestData),
                    'rawRequest' => json_encode($umpRequestData),
                    'responseCode' => $res['ret_code'],
                    'rawResponse' => json_encode($res),
                    'responseMessage' => $res['ret_msg'],
                    'duration' => $time2 - $time1,
                    'txDate' => date('Y-m-d H:i:s'),
                ]);
                $tradeLog->save(false);

                if ($res['ret_code']  === '0000') {
                    $order->buyerPaymentStatus = 1;
                    $order->save(false);
                    $transfer->status = Transfer::STATUS_SUCCESS;
                    $transfer->save(false);
                    \Yii::trace('订单处理-买方支付：支付成功'.PHP_EOL, 'credit_order');
                } elseif ($res['ret_code'] !== '00240000') {
                    $order->buyerPaymentStatus = 2;
                    $order->status = CreditOrder::STATUS_FAIL;
                    $order->save(false);
                    $transfer->status = Transfer::STATUS_FAIL;
                    $transfer->save(false);
                    \Yii::trace('订单处理-买方支付：支付失败'.PHP_EOL, 'credit_order');
                } else {
                    $transfer->status = Transfer::STATUS_OTHER;
                    $transfer->save(false);
                    $order->buyerPaymentStatus = 3;
                    $order->status = CreditOrder::STATUS_OTHER;
                    $order->save(false);
                    \Yii::trace('订单处理-买方支付：[[[支付异常]]]'.PHP_EOL, 'credit_order');
                }
                $transaction->commit();
            } catch (\Exception $ex) {
                $transaction->rollBack();
                \Yii::trace('订单处理-买方支付：支付失败;失败信息：'.$ex->getMessage().PHP_EOL, 'credit_order');
                throw $ex;
            }
        }
    }

    /**
     * 转让这资金回款.
     *
     * @param Client $umpClient
     *
     * @throws \Exception
     */
    private function dealOrderRepayment(Client $umpClient, CreditOrder $order)
    {
        if (
            in_array($order->status, [CreditOrder::STATUS_INIT, CreditOrder::STATUS_OTHER])
            && $order->buyerPaymentStatus === 1
            && in_array($order->sellerRefundStatus, [2, 0])) {
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                $asset = $order->asset;
                if (null === $asset) {
                    throw new \Exception('订单所属资产及债权未找到');
                }
                $fcUser = FcUser::find()->where(['appUserId' => $asset->user_id])->one();
                if (empty($fcUser)) {
                    throw new \Exception('没有找到用户在联动的账户');
                }

                $amount = bcsub($order->amount, $order->fee, 0);
                $transfer = Transfer::find()->where(['type' => 'note_fangkuan', 'sourceType' => CreditOrder::tableName(), 'sourceTxSn' => $order->id])->one();
                if (empty($transfer)) {
                    $transfer = Transfer::initByCreditOrder($order, $amount, $asset->loan_id, $asset->user_id, 'note_fangkuan');
                }

                if ($order->interest > 0) {
                    $time1 = time();
                    $umpRequestData = [
                        'sn' => $transfer->sn,
                        'date' => time(),
                        'loanId' => $asset->loan_id,
                        'fcUserId' => $fcUser->epayUserId,
                        'amount' => $amount,
                    ];
                    $res = $umpClient->noteFangkuan($umpRequestData);

                    $time2 = time();
                    $tradeLog = new TradeLog([
                        'txType' => 'project_transfer',
                        'direction' => 2,
                        'txSn' => $order->id,
                        'uid' => $order->user_id,
                        'requestData' => json_encode($umpRequestData),
                        'rawRequest' => json_encode($umpRequestData),
                        'responseCode' => $res['ret_code'],
                        'rawResponse' => json_encode($res),
                        'responseMessage' => $res['ret_msg'],
                        'duration' => $time2 - $time1,
                        'txDate' => date('Y-m-d H:i:s'),
                    ]);
                    $tradeLog->save(false);

                    if ($res['ret_code']  === '0000') {
                        $order->sellerRefundStatus = 1;
                        $order->save(false);
                        $transfer->status = Transfer::STATUS_SUCCESS;
                        $transfer->save(false);
                        \Yii::trace('订单处理-转让方回款：回款成功'.PHP_EOL, 'credit_order');
                    } elseif ($res['ret_code'] !== '00240000') {
                        $order->sellerRefundStatus = 2;
                        $order->status = CreditOrder::STATUS_OTHER;
                        $order->save(false);
                        $transfer->status = Transfer::STATUS_FAIL;
                        $transfer->save(false);
                        \Yii::trace('订单处理-转让方回款：回款失败'.PHP_EOL, 'credit_order');
                    } else {
                        $transfer->status = Transfer::STATUS_OTHER;
                        $transfer->save(false);
                        $order->sellerRefundStatus = 3;
                        $order->status = CreditOrder::STATUS_OTHER;
                        $order->save(false);
                        \Yii::trace('订单处理-转让方回款：[[[回款异常]]]'.PHP_EOL, 'credit_order');
                    }
                } else {
                    $order->sellerRefundStatus = 1;
                    $order->save(false);
                    $transfer->status = Transfer::STATUS_SUCCESS;
                    $transfer->save(false);
                }
                $transaction->commit();
            } catch (\Exception $ex) {
                $transaction->rollBack();
                \Yii::trace('订单处理-转让方回款：回款失败;失败信息：'.$ex->getMessage().PHP_EOL, 'credit_order');
                throw $ex;
            }
        }
    }

    /**
     * 平台收取手续费.
     *
     * @param Client $umpClient
     *
     * @throws \Exception
     */
    private function dealOrderFee(Client $umpClient, CreditOrder $order)
    {
        if (
            in_array($order->status, [CreditOrder::STATUS_INIT, CreditOrder::STATUS_OTHER])
            && $order->buyerPaymentStatus === 1
            && $order->sellerRefundStatus === 1
            && in_array($order->feeTransferStatus, [2, 0])
        ) {
            $transaction = \Yii::$app->db->beginTransaction();
            try {
                $asset = $order->asset;
                if (null === $asset) {
                    throw new \Exception('订单所属资产及债权未找到');
                }
                $fcUser = $order->fcUser;
                if (null === $fcUser) {
                    throw new \Exception('没有找到用户在联动的账户');
                }
                $transfer = Transfer::find()->where(['type' => 'note_fee', 'sourceType' => CreditOrder::tableName(), 'sourceTxSn' => $order->id])->one();
                if (empty($transfer)) {
                    $transfer = Transfer::initByCreditOrder($order, $order->fee, $asset->loan_id, 0, 'note_fee');
                }

                if ($order->fee > 0) {
                    $time1 = time();
                    $umpRequestData = [
                        'sn' => $transfer->sn,
                        'date' => time(),
                        'loanId' => $asset->loan_id,
                        'amount' => $order->fee,
                    ];
                    $res = $umpClient->noteFee($umpRequestData);

                    $time2 = time();
                    $tradeLog = new TradeLog([
                        'txType' => 'project_transfer',
                        'direction' => 2,
                        'txSn' => $order->id,
                        'uid' => $order->user_id,
                        'requestData' => json_encode($umpRequestData),
                        'rawRequest' => json_encode($umpRequestData),
                        'responseCode' => $res['ret_code'],
                        'rawResponse' => json_encode($res),
                        'responseMessage' => $res['ret_msg'],
                        'duration' => $time2 - $time1,
                        'txDate' => date('Y-m-d H:i:s'),
                    ]);
                    $tradeLog->save(false);

                    if ($res['ret_code']  === '0000') {
                        $order->feeTransferStatus = 1;
                        $order->save(false);
                        $transfer->status = Transfer::STATUS_SUCCESS;
                        $transfer->save(false);
                        Yii::trace('订单处理-平台收取手续费：收取成功'.PHP_EOL, 'credit_order');
                    } elseif ($res['ret_code'] !== '00240000') {
                        $order->feeTransferStatus = 2;
                        $order->status = CreditOrder::STATUS_OTHER;
                        $order->save(false);
                        $transfer->status = Transfer::STATUS_FAIL;
                        $transfer->save(false);
                        Yii::trace('订单处理-平台收取手续费：收取失败'.PHP_EOL, 'credit_order');
                    } else {
                        $transfer->status = Transfer::STATUS_OTHER;
                        $transfer->save(false);
                        $order->feeTransferStatus = 3;
                        $order->status = CreditOrder::STATUS_OTHER;
                        $order->save(false);
                        Yii::trace('订单处理-平台收取手续费：[[[收取异常]]]'.PHP_EOL, 'credit_order');
                    }
                } else {
                    $order->feeTransferStatus = 1;
                    $order->save(false);
                    $transfer->status = Transfer::STATUS_SUCCESS;
                    $transfer->save(false);
                }

                $transaction->commit();
            } catch (\Exception $ex) {
                $transaction->rollBack();
                Yii::trace('订单处理-平台收取手续费：收取失败;失败信息:'.$ex->getMessage().PHP_EOL, 'credit_order');
                throw $ex;
            }
        }
    }

    /**
     * 订单成功后根据order更新当前购买人与转让人的持有数量
     */
    private function updateSellerAndBuyerAmount(CreditOrder $order)
    {
        $userManager = new UserManager();
        $loan_id = $order->note->loan_id;
        $order->buyerAmount = $userManager->getLoanAmountByUser($loan_id, User::findOne($order->user_id));
        $order->sellerAmount = $userManager->getLoanAmountByUser($loan_id, User::findOne($order->note->user_id));
        $order->settleTime = date('Y-m-d H:i:s');
        $order->save(false);
    }
}
