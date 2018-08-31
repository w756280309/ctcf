<?php

namespace common\helpers;

use common\exception\TransferException;
use common\models\transfer\MoneyTransfer As Transfer;
use common\models\user\MoneyRecord;
use Yii;

class TransferHelper
{
    /**
     * 执行转账
     *
     * @param Transfer $transfer 转账业务流水记录
     *
     * @return Transfer $transfer
     */
    public function perform(Transfer $transfer)
    {
        $transfer->save(); //留痕 status=init
        //todo 目前只支持了商户转账（融资商户->平台现金，平台->融资商户，融资商户->融资商户）
        if (!$transfer->isMerToMer()) {
            $transfer->status = 'fail';
            $transfer->retCode = -1;
            $transfer->retMsg = '【' . $transfer->sn . '】暂不支持非企业商户间转账';
            $transfer->save();
        }

        try {
            $this->merToMer($transfer);
            $transfer->status = 'success';
            $transfer->retMsg = '成功';
            $transfer->save();
        } catch (TransferException $ext) {
            $transfer->status = 'fail';
            $transfer->retCode = $ext->getCode();
            $transfer->retMsg = $ext->getMessage();
            $transfer->save();
        } catch (\Exception $ex) {
            $transfer->status = 'fail';
            $transfer->retCode = '99999';
            $transfer->retMsg = $ex->getMessage();
            $transfer->save();
            Yii::error($ex->getMessage());
        }

        return $transfer;
    }

    /**
     * 转账（融资商户->平台现金，平台->融资商户，融资商户->融资商户）
     *
     * @param Transfer $transfer 转账业务流水
     *
     * @return Transfer
     * @throws TransferException
     */
    private function merToMer(Transfer $transfer)
    {
        //检查transfer数据并生成规则的转账参数数组
        $transferInfo = $this->check($transfer);

        //留痕-将转账状态改为处理中
        $transfer->status = 'pending';
        $transfer->retCode = '2000';
        $transfer->retMsg = '第三方支付已转账成功，转账订单号为' . $transfer->sn;
        $transfer->save();

        //融资者商户向平台现金账户转账
        if (Transfer::TYPE_BORROWER === $transfer->fromType
            && Transfer::TYPE_PLATFORM === $transfer->toType) {
            $this->merToPlat($transferInfo);
        //平台现金账户向融资者商户转账
        } elseif (Transfer::TYPE_BORROWER === $transfer->toType
            && Transfer::TYPE_PLATFORM === $transfer->fromType) {
            $this->platToMer($transferInfo);
        //商户向商户转账分两步：1）融资者商户向平台现金账户转账 2）平台现金账户向融资者商户转账
        } else {
            $this->merToPlat($transferInfo);
            $this->platToMer($transferInfo);
        }

        return $transfer;
    }

    private function check($transfer)
    {
        if (!is_numeric($transfer->amount) || $transfer->amount <= 0) {
            throw new TransferException($transfer, '转账金额不合法', '4000');
        }

        //判断转账双方是否在平台存在
        $from = $transfer->getTransferObject('from');
        $to = $transfer->getTransferObject('to');
        if (null === $from || null === $to) {
            throw new TransferException($transfer, '未找到付款方或者收款方', '4001');
        }

        //判断付款方是否为商户，判断商户在系统平台的账户余额
        if (Transfer::TYPE_BORROWER === $transfer->fromType) {
            $borrowerAccount = $from->borrowAccount;
            if (bccomp($borrowerAccount->available_balance, $transfer->amount, 2) < 0) {
                throw new TransferException($transfer, '付款方在平台账户余额不足', '4002');
            }
        }

        //判断转账双方账户是否在第三方支付平台存在
        $fromEpayUserId = $from->getEpayUserId();
        $toEpayUserId = $to->getEpayUserId();
        if (null === $fromEpayUserId || null === $toEpayUserId) {
            throw new TransferException($transfer, '未找到付款方或者收款方在第三方支付平台的账户号', '4003');
        }

        //判断是否为自己向自己转账
        if ($fromEpayUserId === $toEpayUserId) {
            throw new TransferException($transfer, '不允许自己向自己转账', '4004');
        }

        return [
            'from' => $from,
            'to' => $to,
            'fromEpayUserId' => $fromEpayUserId,
            'toEpayUserId' => $toEpayUserId,
            'amount' => $transfer->amount,
            'sn' => $transfer->sn,
            'record' =>  $transfer,
        ];
    }

    /**
     * 商户向平台转账
     *
     * @param array $transfer 转账信息
     *
     * @return Transfer
     * @throws TransferException
     */
    private function merToPlat($transfer)
    {
        $moneyTransfer = $transfer['record'];
        //请求联动处理转账
        $ump = Yii::$container->get('ump');
        $ret = $ump->platformTransfer($transfer['sn'], $transfer['fromEpayUserId'], $transfer['amount'], time());
        if (!$ret->isSuccessful()) {
            throw new TransferException($moneyTransfer, '【UMP】' . $ret->get('ret_msg'), $ret->get('ret_code'));
        }

        //开启事务
        $transaction = Yii::$app->db->beginTransaction();

        //更改付款方账户余额
        $sql = "update user_account set available_balance = available_balance - :amount where uid = :uid limit 1";
        $affectedRows = Yii::$app->db->createCommand($sql, [
            'amount' => $transfer['amount'],
            'uid' => $transfer['from']->id,
        ])->execute();
        if (!$affectedRows) {
            $transaction->rollBack();
            throw new TransferException($moneyTransfer, '更新付款方账户余额失败', 5000);
        }

        //添加付款方资金流水
        $borrowerAccount = $transfer['from']->borrowAccount;
        $moneyRecord = new MoneyRecord([
            'account_id' => $borrowerAccount->id,
            'sn' => MoneyRecord::createSN(),
            'type' => MoneyRecord::TYPE_BORROWER_TO_PLATFORM,
            'osn' => $transfer['sn'],
            'uid' => $borrowerAccount->uid,
            'out_money' => $transfer['amount'],
            'balance' => $borrowerAccount->available_balance,
            'remark' => '商户向平台转移' . $transfer['amount'] . '元',
        ]);
        $recordSave = $moneyRecord->save();
        if (!$recordSave) {
            $transaction->rollBack();
            throw new TransferException($moneyTransfer, '添加付款方转出流水失败', 5001);
        }

        //事务结束
        $transaction->commit();

        return $moneyTransfer;
    }

    /**
     * 平台向商户转账
     *
     * @param array $transfer 转账信息
     *
     * @return Transfer
     * @throws TransferException
     */
    private function platToMer($transfer)
    {
        $moneyTransfer = $transfer['record'];
        //请求联动处理转账
        $ump = Yii::$container->get('ump');
        $ret = $ump->orgTransfer('PM' . $transfer['sn'], $transfer['toEpayUserId'], $transfer['amount'], time());
        if (!$ret->isSuccessful()) {
            throw new TransferException($moneyTransfer, '【UMP】' . $ret->get('ret_msg'), $ret->get('ret_code'));
        }

        //开启事务
        $transaction = Yii::$app->db->beginTransaction();

        //更改付款方账户余额
        $sql = "update user_account set available_balance = available_balance + :amount where uid = :uid limit 1";
        $affectedRows = Yii::$app->db->createCommand($sql, [
            'amount' => $transfer['amount'],
            'uid' => $transfer['to']->id,
        ])->execute();
        if (!$affectedRows) {
            $transaction->rollBack();
            throw new TransferException($moneyTransfer, '更新付款方账户余额失败', 5002);
        }

        //添加收款方资金流水
        $borrowerAccount = $transfer['to']->borrowAccount;
        $moneyRecord = new MoneyRecord([
            'account_id' => $borrowerAccount->id,
            'sn' => MoneyRecord::createSN(),
            'type' => MoneyRecord::TYPE_BORROWER_TO_PLATFORM,
            'osn' => $transfer['sn'],
            'uid' => $borrowerAccount->uid,
            'in_money' => $transfer['amount'],
            'balance' => $borrowerAccount->available_balance,
            'remark' => '平台向商户转移' . $transfer['amount'] . '元',
        ]);
        $recordSave = $moneyRecord->save();
        if (!$recordSave) {
            $transaction->rollBack();
            throw new TransferException($moneyTransfer, '添加收款方转入流水失败', 5003);
        }

        //事务结束
        $transaction->commit();

        return $moneyTransfer;
    }
}