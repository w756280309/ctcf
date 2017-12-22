<?php

namespace common\models\draw;

use common\models\message\DrawMessage;
use common\models\user\User;
use common\models\user\DrawRecord;
use common\models\user\MoneyRecord;
use common\lib\bchelp\BcRound;
use common\models\user\UserAccount;
use common\service\SmsService;
use common\utils\SecurityUtils;
use Ding\DingNotify;
use Lhjx\Noty\Noty;
use Yii;

class DrawManager
{
    /**
     * 创建一个提现申请
     *
     * @param UserAccount $account 账户信息
     * @param string      $money   提现金额
     * @param string      $fee     提现手续费
     *
     * @return DrawRecord
     */
    public static function initDraw(UserAccount $account, $money, $fee = '0')
    {
        $money = DrawRecord::getDrawableMoney($account, $money, $fee);

        return self::initNew($account, $money, $fee);
    }

    /**
     * 初始化提现记录
     *
     * @param UserAccount $account 账户信息
     * @param string      $money   提现金额
     * @param string      $fee     提现手续费
     *
     * @return DrawRecord
     */
    public static function initNew(UserAccount $account, $money, $fee = '0')
    {
        $user = $account->user;
        $ubank = $user->qpay;
        $draw = new DrawRecord();
        $draw->sn = DrawRecord::createSN();
        $draw->money = $money;
        $draw->fee = $fee;
        $draw->pay_id = 0;
        $draw->account_id = $account->id;
        $draw->uid = $user->id;
        $draw->pay_bank_id = '0';
        $draw->bank_id = $ubank->bank_id;
        $draw->bank_name = $ubank->bank_name;
        $draw->bank_account = $ubank->card_number;
        $draw->identification_type = $ubank->account_type;
        $draw->identification_number = SecurityUtils::decrypt($user->safeIdCard);
        $draw->user_bank_id = $ubank->id;
        $draw->status = DrawRecord::STATUS_ZERO;

        return $draw;
    }

    /**
     * 修改提现申请的状态为已受理并记录流水及余额变动
     * - 提现状态校验
     * - 更新提现状态 提现初始 0 -> 1 提现成功
     * - 记录money_record 1 及更新user_account - 提现金额
     * - 记录money_record 103 及更新user_account - 提现手续费
     * - 短信提醒 提现受理成功，模板号：71400
     *
     * @param DrawRecord $draw
     *
     * @return DrawRecord
     * @throws DrawException
     */
    public static function ackDraw(DrawRecord $draw)
    {
        //提现状态校验
        if (DrawRecord::STATUS_ZERO !== (int) $draw->status) {
            throw new DrawException('【提现受理】提现记录：当前提现状态失败');
        }

        //更新提现状态
        /* 事务开始 */
        /* 提现初始 0 -> 1 提现成功 */
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();
        $sql = "update draw_record set `status` = :drawStatus where `sn` = :drawSn and `status` = 0";
        $affectedRows = $db->createCommand($sql, [
            'drawStatus' => DrawRecord::STATUS_EXAMINED,
            'drawSn' => $draw->sn,
        ])->execute();
        if (0 === $affectedRows) {
            $transaction->rollBack();
            throw new DrawException('【提现受理】提现记录：更新受理状态失败');
        }

        /* 获得当前提现用户账户信息 */
        $draw->refresh();
        $user = $draw->user;
        $fee = $draw->fee;
        $account = $user->type == 1 ? $user->lendAccount : $user->borrowAccount;

        //记录money_record及更新user_account，分2块，提现金额、提现手续费
        /* 提现金额 */
        $bc = new BcRound();
        bcscale(14);
        $moneyRecord = new MoneyRecord();
        $moneyRecord->sn = MoneyRecord::createSN();
        $moneyRecord->type = MoneyRecord::TYPE_DRAW;
        $moneyRecord->osn = $draw->sn;
        $moneyRecord->account_id = $account->id;
        $moneyRecord->uid = $user->id;
        $moneyRecord->balance = $bc->bcround(bcsub($account->available_balance, $draw->money), 2);
        $moneyRecord->out_money = $draw->money;
        if (!$moneyRecord->save()) {
            $transaction->rollBack();
            throw new DrawException('【提现受理】资金流水记录：提现金额失败');
        }
        $sql = "update user_account set available_balance = available_balance - :amount, out_sum = out_sum - :amount where id = :accountId";
        $res = $db->createCommand($sql, [
            'amount' => $draw->money,
            'accountId' => $account->id,
        ])->execute();
        if (0 === $res) {
            $transaction->rollBack();
            throw new DrawException('【提现受理】账户余额更新：提现金额失败');
        }

        /* 提现手续费 */
        if ($fee > 0) {
            $account->refresh();
            $feeRecord = new MoneyRecord();
            $feeRecord->osn = $draw->sn;
            $feeRecord->account_id = $account->id;
            $feeRecord->uid = $user->id;
            $feeRecord->sn = MoneyRecord::createSN();
            $feeRecord->type = MoneyRecord::TYPE_DRAW_FEE;
            $feeRecord->balance = $bc->bcround(bcsub($account->available_balance, $draw->fee), 2);
            $feeRecord->out_money = $fee;
            if (!$feeRecord->save()) {
                $transaction->rollBack();
                throw new DrawException('【提现受理】资金流水记录：提现手续费失败');
            }
            $sql = "update user_account set available_balance = available_balance - :fee, out_sum = out_sum - :fee where id = :accountId";
            $res = $db->createCommand($sql, [
                'fee' => $draw->fee,
                'accountId' => $account->id,
            ])->execute();
            if (0 === $res) {
                $transaction->rollBack();
                throw new DrawException('【提现受理】账户余额更新：提现金额失败');
            }
        }

        //事务提交
        $transaction->commit();

        //短信提醒 提现受理成功 - 模板号：71400
        $message = [
            $user->real_name,
            date('Y-m-d H:i', $draw->created_at),
            $draw->money,
            'T+1',
            Yii::$app->params['platform_info.contact_tel'],
        ];
        $templateId = Yii::$app->params['sms']['tixian_apply'];
        SmsService::send(SecurityUtils::decrypt($user->safeMobile), $templateId, $message, $user);

        return $draw;
    }

    /**
     * 确定提现完成
     * - 状态检验
     * - 查询联动提现流水状态
     * - 成功 tranState 2
     *      - 更新提现状态为成功 受理中 1 -> 2 提现成功
     *      - 记录money_record 101
     *      - 写入微信推送队列
     * - 失败 tranState 3 5 15
     *      - 更新提现状态为失败 受理中 1 -> 11 提现失败
     *      - 执行提现撤销逻辑 记录money_record，并返还 提现金额 + 提现手续费
     *
     * @param DrawRecord $draw
     *
     * @return void
     * @throws DrawException
     */
    public static function commitDraw(DrawRecord $draw)
    {
        //状态检验
        $drawStatus = (int) $draw->status;
        if ($drawStatus !== DrawRecord::STATUS_EXAMINED) {
            throw new DrawException('【提现成功】提现记录状态：提现状态应为受理中');
        }

        //查询联动是否提现成功
        $resp = \Yii::$container->get('ump')->getDrawInfo($draw);
        $db = Yii::$app->db;
        if ($resp->isSuccessful()) {
            $tranState = (int) $resp->get('tran_state');
            if (2 === $tranState) {
                $transaction = $db->beginTransaction();
                $sql = "update draw_record set `status` = :drawStatus where `sn` = :drawSn and `status` = 1";
                $affectedRows = $db->createCommand($sql, [
                    'drawStatus' => DrawRecord::STATUS_SUCCESS,
                    'drawSn' => $draw->sn,
                ])->execute();
                if (0 === $affectedRows) {
                    $transaction->rollBack();
                    throw new DrawException('【提现成功】提现记录：更新提现状态失败');
                }

                //记录资金流水
                $userAccount = UserAccount::find()
                    ->where(['uid' => $draw->uid])
                    ->one();
                $moneyRecord = new MoneyRecord();
                $moneyRecord->uid = $draw->uid;
                $moneyRecord->sn = MoneyRecord::createSN();
                $moneyRecord->osn = $draw->sn;
                $moneyRecord->account_id = $userAccount->id;
                $moneyRecord->type = MoneyRecord::TYPE_DRAW_SUCCESS;
                $moneyRecord->balance = $userAccount->available_balance;
                $moneyRecord->out_money = bcadd($draw->money, $draw->fee, 2);
                if (!$moneyRecord->save()) {
                    $transaction->rollBack();
                    throw new DrawException('【提现成功】资金流水记录：提现手续费+提现金额失败');
                }
                $transaction->commit();
            } elseif ($tranState === 3 || $tranState === 5 || $tranState === 15) {
                $user = $draw->user;
                if (!empty($user)) {
                    //联动提现失败钉钉提醒
                    $msg = '用户['.$user->id.']，于'.date('Y-m-d H:i:s', $draw->created_at);
                    $msg .= ' 进行提现操作，操作失败，联动提现失败，失败信息:'.$resp->get('ret_msg');
                    (new DingNotify('wdjf'))->sendToUsers($msg);
                }
                //提现受理 -> 提现失败过程
                self::cancel($draw, DrawRecord::STATUS_DENY);
            }

            //提现成功 - 写入微信推送队列
            if (DrawRecord::STATUS_SUCCESS === $draw->status) {
                //如果提现成功，将对应的消息写入task
                Noty::send(new DrawMessage($draw));
            }
        }
    }

    /**
     * 提现失败 - 撤销提现金额及提现手续费
     * - 获得用户账户信息
     * - 更新提现状态 受理中 1 -> 失败 11
     * - 提现金额退回 money_record 100
     * - 提现手续费退回 money_record 104
     *
     * @param DrawRecord $draw   提现流水
     * @param int        $status 状态
     *
     * @return DrawRecord
     * @throws DrawException
     */
    public static function cancel(DrawRecord $draw, $status)
    {
        /* 获得用户账户信息 */
        $user = $draw->user;
        $account = $user->type == 1 ? $user->lendAccount : $user->borrowAccount;
        $bc = new BcRound();
        bcscale(14);

        /* 事务开始 */
        $db = Yii::$app->db;
        $transaction = $db->beginTransaction();

        /* 更新提现状态 受理中 1 -> 失败 11 */
        $sql = "update draw_record set `status` = :drawStatus where `sn` = :drawSn and `status` = 1";
        $affectedRows = $db->createCommand($sql, [
            'drawStatus' => $status,
            'drawSn' => $draw->sn,
        ])->execute();
        if (0 === $affectedRows) {
            $transaction->rollBack();
            throw new DrawException('【提现失败】提现记录：更新提现状态失败');
        }

        if (DrawRecord::STATUS_DENY === (int) $status) {
            /* 提现金额退回 */
            $moneyRecord = new MoneyRecord([
                'sn' => MoneyRecord::createSN(),
                'type' => MoneyRecord::TYPE_DRAW_CANCEL,
                'osn' => $draw->sn,
                'account_id' => $account->id,
                'uid' => $account->uid,
                'balance' => $bc->bcround(bcadd($account->available_balance, $draw->money), 2),
                'in_money' => $draw->money,
            ]);
            if (!$moneyRecord->save()) {
                $transaction->rollBack();
                throw new DrawException('【提现失败】资金流水记录：提现金额失败');
            }
            $sql = "update user_account set available_balance = available_balance + :amount, in_sum = in_sum + :amount where id = :accountId";
            $res = $db->createCommand($sql, [
                'amount' => $draw->money,
                'accountId' => $account->id,
            ])->execute();
            if (0 === $res) {
                $transaction->rollBack();
                throw new DrawException('【提现失败】提现流水：提现金额退回失败');
            }

            /* 提现手续费退回 */
            //如果存在提现手续费,将冻结提现手续费的金额解冻
            $mrFee = MoneyRecord::findOne(['osn' => $draw->sn, 'type' => MoneyRecord::TYPE_DRAW_FEE]);
            if (null !== $mrFee) {
                $account->refresh();
                $feeRecord = new MoneyRecord();
                $feeRecord->osn = $draw->sn;
                $feeRecord->account_id = $account->id;
                $feeRecord->uid = $account->uid;
                $feeRecord->sn = MoneyRecord::createSN();
                $feeRecord->type = MoneyRecord::TYPE_DRAW_FEE_RETURN;
                $feeRecord->balance = $bc->bcround(bcadd($account->available_balance, $draw->fee), 2);
                $feeRecord->in_money = $mrFee->out_money;
                if (!$feeRecord->save()) {
                    $transaction->rollBack();
                    throw new DrawException('【提现失败】资金流水记录：提现手续费失败');
                }
                $sql = "update user_account set available_balance = available_balance + :fee, in_sum = in_sum + :fee where id = :accountId";
                $res = $db->createCommand($sql, [
                    'fee' => $mrFee->out_money,
                    'accountId' => $account->id,
                ])->execute();
                if (0 === $res) {
                    $transaction->rollBack();
                    throw new DrawException('【提现失败】账户余额更新：提现手续费退回失败');
                }
            }
        }
        $transaction->commit();

        return $draw;
    }
}
