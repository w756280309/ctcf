<?php

namespace common\models\draw;

use Yii;
use common\models\user\DrawRecord;
use common\models\user\MoneyRecord;
use common\lib\bchelp\BcRound;
use common\models\user\UserAccount;
use common\service\SmsService;

/**
 * draw form.
 */
class DrawManager
{
    /**
     * 创建一个提现申请.
     *
     * @param type $account
     * @param type $money
     * @param type $fee
     */
    public static function initDraw(UserAccount $account, $money, $fee = 0)
    {
        $money = DrawRecord::getDrawableMoney($account, $money, $fee);
        $user = $account->user;
        $ubank = $user->qpay;
        $draw = new DrawRecord();
        $draw->sn = DrawRecord::createSN();
        $draw->money = $money;
        $draw->fee = $fee;
        $draw->pay_id = 0; // 支付公司ID
        $draw->account_id = $account->id;
        $draw->uid = $user->id;
        $draw->pay_bank_id = '0'; // TODO
        $draw->bank_id = $ubank->bank_id;
        $draw->bank_name = $ubank->bank_name;
        $draw->bank_account = $ubank->card_number;
        $draw->identification_type = $ubank->account_type;
        $draw->identification_number = $user->idcard;
        $draw->user_bank_id = $ubank->id;
        $draw->mobile = $user->mobile;
        $draw->status = DrawRecord::STATUS_ZERO;
        if ($draw->validate() && $draw->save(false)) {
            return $draw;
        } else {
            throw new \Exception('提现申请失败');
        }
    }

    /**
     * 修改提现申请的状态为已受理.
     */
    public static function ackDraw(DrawRecord $draw)
    {
        if (DrawRecord::STATUS_ZERO !== (int) $draw->status) {
            throw new DrawException('审核受理失败,提现申请状态异常');
        }
        $user = $draw->user;
        $fee = $draw->fee;
        $account = $user->type == 1 ? $user->lendAccount : $user->borrowAccount;
        $transaction = Yii::$app->db->beginTransaction();

        $draw->status = DrawRecord::STATUS_EXAMINED;
        if (!$draw->save(false)) {
            $transaction->rollBack();
            throw new DrawException('审核受理失败');
        }

        //录入money_record记录
        $bc = new BcRound();
        bcscale(14);
        $account->available_balance = $bc->bcround(bcsub($account->available_balance, $draw->money), 2);
        $account->account_balance = $bc->bcround(bcsub($account->account_balance, $draw->money), 2);//提现受理成功之后账户余额要减去提现金额
        $money_record = new MoneyRecord();
        $money_record->sn = MoneyRecord::createSN();
        $money_record->type = MoneyRecord::TYPE_DRAW;
        $money_record->osn = $draw->sn;
        $money_record->account_id = $account->id;
        $money_record->uid = $user->id;
        $money_record->balance = $account->available_balance;
        $money_record->out_money = $draw->money;

        $account->available_balance = $bc->bcround(bcsub($account->available_balance, $fee), 2);
        $account->account_balance = $bc->bcround(bcsub($account->account_balance, $fee), 2);//160309提现受理成功之后账户余额要减去手续费
        if (!$money_record->save()) {
            $transaction->rollBack();
            throw new DrawException('提现申请失败');
        }
        if ($fee > 0) {
            $mrecord = clone $money_record;
            $mrecord->sn = MoneyRecord::createSN();
            $mrecord->type = MoneyRecord::TYPE_DRAW_FEE;
            $mrecord->balance = $account->available_balance;
            $mrecord->out_money = $fee;
            if (!$mrecord->save()) {
                $transaction->rollBack();
                throw new DrawException('提现申请失败');
            }
        }

        //录入user_acount记录
        //$account->available_balance = $account->available_balance;//多余的赋值，上边已经计算过了
        //$account->freeze_balance = $bc->bcround(bcadd($account->freeze_balance, bcadd($draw->money, $fee)), 2);160309提现受理成功之后不冻结，
        $account->out_sum = $bc->bcround(bcadd($account->out_sum, bcadd($draw->money, $fee)), 2);

        if (!$account->save()) {
            $transaction->rollBack();
            throw new DrawException('提现申请失败');
        }

        $message = [$user->real_name,  date('Y-m-d H:i', $draw->created_at), $draw->money, 'T+1', Yii::$app->params['contact_tel']];
        $templateId = Yii::$app->params['sms']['tixian_apply'];

        SmsService::send($user->mobile, $templateId, $message, $user);

        $transaction->commit();

        return $draw;
    }

    /**
     * 确定提现完成
     *
     * @param DrawRecord $draw
     *
     * @throws \Exception
     */
    public static function commitDraw(DrawRecord $draw)
    {
        $drawStatus = (int) $draw->status;

        if ($drawStatus !== DrawRecord::STATUS_EXAMINED) {
            throw new DrawException('必须是受理成功的');
        }
        $resp = \Yii::$container->get('ump')->getDrawInfo($draw);

        if ($resp->isSuccessful()) {
            $bc = new BcRound();
            $tranState = (int) $resp->get('tran_state');
            if (2 === $tranState) {
                $transaction = Yii::$app->db->beginTransaction();
                $money = bcadd($draw->money, $draw->fee);
                $userAccount = UserAccount::find()->where('uid = '.$draw->uid)->one();
                $draw->status = DrawRecord::STATUS_SUCCESS;

                $momeyRecord = new MoneyRecord();
                $momeyRecord->uid = $draw->uid;
                $momeyRecord->sn = MoneyRecord::createSN();
                $momeyRecord->osn = $draw->sn;
                $momeyRecord->account_id = $userAccount->id;
                $momeyRecord->type = MoneyRecord::TYPE_DRAW_SUCCESS;
                $momeyRecord->balance = $userAccount->available_balance;
                $momeyRecord->out_money = $bc->bcround($money, 2);

                if ($draw->save(false) && $momeyRecord->save(false) && $userAccount->save(false)) {
                    $transaction->commit();
                } else {
                    $transaction->rollBack();
                }
            } elseif ($tranState === 3 || $tranState === 5 || $tranState === 15) {
                //失败的代码
                self::cancel($draw, DrawRecord::STATUS_DENY);
            }
        }
    }

    /**
     * @param DrawRecord $draw
     * @param type       $status
     *
     * @return DrawRecord
     *
     * @throws DrawException
     */
    public static function cancel(DrawRecord $draw, $status)
    {
        $user = $draw->user;
        $account = $user->type == 1 ? $user->lendAccount : $user->borrowAccount;

        $mrfee = MoneyRecord::findOne(['osn' => $draw->sn, 'type' => MoneyRecord::TYPE_DRAW_FEE]); //获取提现手续费的记录
        $bc = new BcRound();
        bcscale(14);
        $transaction = Yii::$app->db->beginTransaction();
        $draw->status = $status;
        if (!$draw->save(false)) {
            $transaction->rollBack();
            throw new DrawException('审核失败');
        }
        if (DrawRecord::STATUS_DENY === (int) $status) { //处理如果不通过的情况
            $account->available_balance = $bc->bcround(bcadd($account->available_balance, $draw->money), 2);
            $account->account_balance = $bc->bcround(bcadd($account->account_balance, $draw->money), 2);//160309账户总额在提现结果失败之后增加
            $money_record = new MoneyRecord([
                'sn' => MoneyRecord::createSN(),
                'type' => MoneyRecord::TYPE_DRAW_CANCEL,
                'osn' => $draw->sn,
                'account_id' => $account->id,
                'uid' => $account->uid,
                'balance' => $account->available_balance,
                'in_money' => $draw->money,
            ]);
            $fee_record = null;//返还手续费对象
            if (null !== $mrfee) { //如果存在提现手续费,将冻结提现手续费的金额解冻
                $draw->money = bcadd($mrfee->out_money, $draw->money); //将手续费也加入到解冻金额中
                $account->available_balance = $bc->bcround(bcadd($account->available_balance, $mrfee->out_money), 2);
                $account->account_balance = $bc->bcround(bcadd($account->account_balance, $mrfee->out_money), 2);//160309账户总额在提现结果失败之后增加手续费
                $fee_record = clone $money_record;
                $fee_record->type = MoneyRecord::TYPE_DRAW_FEE_RETURN;
                $fee_record->in_money = $mrfee->out_money;
                $fee_record->balance = $account->available_balance;
            }
            $account->drawable_balance = $bc->bcround(bcadd($account->drawable_balance, $draw->money), 2);
            $account->in_sum = $bc->bcround(bcadd($account->in_sum, $draw->money), 2);
            if (!$money_record->save() || !$account->save() || (null !== $fee_record && !$fee_record->save(false))) {
                $transaction->rollBack();
                throw new DrawException('审核失败');
            }
        }
        $transaction->commit();

        return $draw;
    }
}
