<?php

namespace common\service;

use common\models\TradeLog;
use common\models\transfer\TransferTx;
use common\models\user\UserAccount;
use common\utils\TxUtils;
use Yii;
use common\models\user\RechargeRecord;
use common\lib\bchelp\BcRound;
use common\models\user\MoneyRecord;
use common\models\user\User;

class AccountService
{
    /**
     * 充值流水 将充值初始变为充值成功状态
     *
     * - 充值流水校验
     * - 修改充值状态
     * - 获得当前最新的user_account
     * - 添加资金流水
     * - 更新user_account记录（available_balance， in_sum, account_balance(此字段后期不再维护））
     *
     * @param RechargeRecord $recharge 充值流水
     *
     * @return bool
     */
    public function confirmRecharge(RechargeRecord $recharge)
    {
        //充值记录状态为成功，不做处理
        if (RechargeRecord::STATUS_YES === $recharge->status) {
            return true;
        }
        //充值记录状态不为成功，可能是未处理，可能是失败，但是联动可能将失败状态改为成功，所以都要做处理。
        $record = MoneyRecord::findOne(['osn' => $recharge->sn]);
        if (!is_null($record)) {
            return true;
        }

        //修改充值状态
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        $sql = "update recharge_record set status = :status where id = :id and status IN (0, 2)";
        $affectedRows = $db->createCommand($sql, [
            'id' => $recharge->id,
            'status' => RechargeRecord::STATUS_YES,
        ])->execute();
        if (0 === $affectedRows) {
            $transaction->rollBack();
            return false;
        }

        //添加资金流水
        //获得当前最新的user_account
        $user = $recharge->user;
        $userAccount = $user->type === User::USER_TYPE_PERSONAL ? $user->lendAccount : $user->borrowAccount;
        $userAccount->refresh();
        $bc = new BcRound();
        bcscale(14);
        $money_record = new MoneyRecord([
            'sn' => MoneyRecord::createSN(),
            'type' => (RechargeRecord::PAY_TYPE_POS === (int)$recharge->pay_type) ? MoneyRecord::TYPE_RECHARGE_POS : MoneyRecord::TYPE_RECHARGE,
            'osn' => $recharge->sn,
            'account_id' => $userAccount->id,
            'uid' => $user->id,
            'balance' => $bc->bcround(bcadd($userAccount->available_balance, $recharge->fund), 2),
            'in_money' => $recharge->fund,
        ]);
        if (!$money_record->save()) {
            $transaction->rollBack();
            return false;
        }

        //更新user_account记录
        //20170930 - account_balance字段不再维护
        $sql = "update user_account set available_balance = available_balance + :amount, in_sum = in_sum + :amount where id = :accountId";
        $res = $db->createCommand($sql, ['amount' => $recharge->fund, 'accountId' => $userAccount->id])->execute();
        if (0 === $res) {
            $transaction->rollBack();
            return false;
        }
        $transaction->commit();

        return true;
    }

    /**
     * 充值失败时,更新充值记录状态为失败
     */
    public function cancelRecharge(RechargeRecord $recharge)
    {
        if (RechargeRecord::STATUS_NO !== $recharge->status) {
            return true;
        }

        //修改充值状态
        $res = RechargeRecord::updateAll(['status' => RechargeRecord::STATUS_FAULT], ['id' => $recharge->id]);
        if (!$res) {
            return false;
        }

        return true;
    }

    /**
     * 给指定用户转账
     * @param User $user
     * @param float $money
     * @param string $orderSn
     * @return bool|MoneyRecord
     * @throws \Exception
     */
    public static function userTransfer(User $user, $money, $orderSn = '')
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            /**
             * @var UserAccount $account
             */
            $account = $user->lendAccount;
            if (is_null($account)) {
                throw new \Exception('用户资金账户不存在');
            }
            if (bccomp($money, 0, 2) <= 0) {
                throw new \Exception('转账金额不合法');
            }
            //更改用户账户资金
            $sql = "update `user_account` set `account_balance` = `account_balance` + :amount, `available_balance` = `available_balance` + :amount, `drawable_balance` = `drawable_balance` + :amount where id = :accountId and uid = :userId";
            $affectedRows = Yii::$app->db->createCommand($sql, [
                'amount' => $money,
                'accountId' => $account->id,
                'userId' => $user->id,
            ])->execute();
            if ($affectedRows !== 1) {
                throw new \Exception('更改用户账户资金失败');
            }
            $account->refresh();

            $sn = empty($orderSn) ?  TxUtils::generateSn('CG') : $orderSn;
            //记流水账
            $moneyRecord = new MoneyRecord([
                'type' => MoneyRecord::TYPE_CASH_GIFT,
                'sn' => MoneyRecord::createSN(),
                'osn' => $sn,
                'account_id' => $account->id,
                'uid' => $user->id,
                'in_money' => $money,
                'remark' => $user->getName() . ' 的 ' . $money . '元现金红包已发放',
                'balance' => $account->available_balance,
            ]);
            if (!$moneyRecord->save(false)) {
                throw new \Exception('资金流水添加失败');
            }
            //请求联动,接口编号 transfer
            $time = time();
            $epayUserId = $user->epayUser->epayUserId;
            $ret = Yii::$container->get('ump')->transferToUser($sn, $epayUserId, $money, $time);
            Yii::info("[user_transfer]用户 {$user->id} 现金转账，转账金额 {$money} 元，联动信息 {$ret->get('ret_code')} ： {$ret->get('ret_msg')} ", 'user_log');
            if (!$ret->isSuccessful()) {
                throw new \Exception('联动转账失败, 失败信息:' . $ret->get('ret_msg'));
            }
            //记录tradeLog
            $log = new TradeLog([
                'txType' => 'transfer',
                'direction' => '2',
                'txSn' => $sn,
                'txDate' => date('Y-m-d H:i:s', $time),
            ]);
            $log->requestData = json_encode([
                'service' => 'transfer',
                'order_id' => $sn,
                'mer_date' => date('Ymd', $time),
                'partic_user_id' => $epayUserId,
                'partic_acc_type' => '01',//对私，向个人账户转账
                'trans_action' => '02',//p2p平台向用户转账
                'amount' => $money * 100,
            ]);//存储没有进行签名的数据
            $log->responseMessage = $ret;
            $log->responseCode = $ret->get('ret_code');
            $log->rawResponse = json_encode($ret->toArray());
            $log->responseMessage = $ret->get('ret_msg');
            $log->duration = 0;
            $log->uid = $user->id;
            $log->save(false);

            $transaction->commit();
            return $moneyRecord;
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::info("[user_transfer][exception]用户 {$user->id} 现金转账失败，转账金额 {$money} 元，失败信息 :{$e->getMessage()} ", 'user_log');
            throw $e;
        }
    }

    /**
     * 确认授权转账成功
     */
    public function confirmTransfer(TransferTx $transferTx)
    {
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        try {
            if (in_array($transferTx->status, [TransferTx::STATUS_SUCCESS, TransferTx::STATUS_FAILURE])) {
                throw new \Exception('待处理订单状态【'.$transferTx->getStatusLabel().'】错误');
            }
            //当前订单为初始状态时，更新状态为处理中
            if (0 === $transferTx->status) {
                //转账订单变为处理中
                $sql = "update transfer_tx set status = 1 where status = 0 and sn=:sn limit 1";
                $affectedRows = $db->createCommand($sql, [
                    'sn' => $transferTx->sn,
                ])->execute();
                if ($affectedRows <= 0) {
                    throw new \Exception('更新流水记录状态为处理中时失败');
                }
            }
            $transferTx->refresh();
            if (1 === $transferTx->status) {
                //更新交易状态
                $sql = "update transfer_tx set status = 2 where status = 1 and sn=:sn limit 1";
                $transferRows = $db->createCommand($sql, [
                    'sn' => $transferTx->sn,
                ])->execute();
                if ($transferRows <= 0) {
                    throw new \Exception('更新流水记录状态为成功时失败');
                }

                //更新用户余额
                $sqlAccount = "update user_account set available_balance=available_balance-:availableBalance where uid = :uid limit 1";
                $accountRows = $db->createCommand($sqlAccount, [
                    'uid' => $transferTx->userId,
                    'availableBalance' => $transferTx->money,
                ])->execute();
                if ($accountRows <= 0) {
                    throw new \Exception('更新用户余额失败');
                }

                //添加资金流水 - 温都余额扣减
                $ua = $transferTx->user->lendAccount;
                $moneyRecord = new MoneyRecord([
                    'sn' => TxUtils::generateSn('MR'),
                    'type' => MoneyRecord::TYPE_AUTHORIZED_TRANSFER,
                    'osn' => $transferTx->sn,
                    'uid' => $transferTx->userId,
                    'account_id' => $ua->id,
                    'balance' => $ua->available_balance,
                    'out_money' => $transferTx->money,
                    'remark' => '投资南金中心',
                ]);
                $moneyRecord->save(false);

                $transaction->commit();
            }
        } catch (\Exception $ex) {
            $transaction->rollBack();
            Yii::info('授权转账失败处理订单sn：'.$transferTx->sn, 'user_log');
        }
    }
}
