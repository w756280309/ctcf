<?php
namespace common\models\draw;

use Yii;
use common\models\user\DrawRecord;
use common\models\user\MoneyRecord;
use common\lib\bchelp\BcRound;

/**
 * draw form
 */
class DrawManager
{
    /**
     * 发起提现
     *
     */
    public static function init($user, $money, $fee = 0) {
        $draw = DrawRecord::initForAccount($user, $money);
        if (!$draw->validate()) {
            throw new DrawException(current($draw->firstErrors));
        }
        $transaction = Yii::$app->db->beginTransaction();

        //录入draw_record记录
        if (!$draw->save()) {
            $transaction->rollBack();
            throw new DrawException("提现申请失败");
        }

        //录入money_record记录
        $bc = new BcRound();
        bcscale(14);
        $user->lendAccount->available_balance = $bc->bcround(bcsub($user->lendAccount->available_balance, $draw->money), 2);
        $money_record = new MoneyRecord();
        $money_record->sn = MoneyRecord::createSN();
        $money_record->type = MoneyRecord::TYPE_DRAW;
        $money_record->osn = $draw->sn;
        $money_record->account_id = $user->lendAccount->id;
        $money_record->uid = $user->id;
        $money_record->balance = $user->lendAccount->available_balance;
        $money_record->out_money = $draw->money;

        $user->lendAccount->available_balance = $bc->bcround(bcsub($user->lendAccount->available_balance, $fee), 2);
        $mrecord = clone $money_record;
        $mrecord->sn = MoneyRecord::createSN();
        $mrecord->type = MoneyRecord::TYPE_DRAW_FEE;
        $mrecord->balance = $user->lendAccount->available_balance;
        $mrecord->out_money = $fee;

        if (!$money_record->save() || !$mrecord->save()) {
            $transaction->rollBack();
            throw new DrawException("提现申请失败");
        }

        //录入user_acount记录
        $user->lendAccount->available_balance = $user->lendAccount->available_balance;
        $user->lendAccount->freeze_balance = $bc->bcround(bcadd($user->lendAccount->freeze_balance, bcadd($draw->money, $fee)), 2);

        if (!$user->lendAccount->save()) {
            $transaction->rollBack();
            throw new DrawException("提现申请失败");
        }

        $transaction->commit();
        return $draw;
    }
    
    public static function audit($draw, $status) {
        if ((int) $draw->status !== DrawRecord::STATUS_ZERO) {
            throw new DrawException("必须是未审核的");
        }
        $mrfee = MoneyRecord::findOne(['osn' => $draw->sn, 'type' => MoneyRecord::TYPE_DRAW_FEE]); //获取提现手续费的记录
        $bc = new BcRound();
        bcscale(14);
        $transaction = Yii::$app->db->beginTransaction();
        $draw->status = $status;
        if (!$draw->save()) {
            $transaction->rollBack();
            throw new DrawException("审核失败");
        }
        if (DrawRecord::STATUS_DENY === (int) $status) { //处理如果不通过的情况
            $draw->user->lendAccount->available_balance = $bc->bcround(bcadd($draw->user->lendAccount->available_balance, $draw->money), 2);
            $money_record = new MoneyRecord([
                'sn' => MoneyRecord::createSN(),
                'type' => MoneyRecord::TYPE_DRAW_CANCEL,
                'osn' => $draw->sn,
                'account_id' => $draw->user->lendAccount->id,
                'uid' => $draw->user->lendAccount->uid,
                'balance' => $draw->user->lendAccount->available_balance,
                'in_money' => $draw->money,
            ]);
            if (null !== $mrfee) { //如果存在提现手续费,将冻结提现手续费的金额解冻
                $draw->money = bcadd($mrfee->out_money, $draw->money); //将手续费也加入到解冻金额中
                $draw->user->lendAccount->available_balance = $bc->bcround(bcadd($draw->user->lendAccount->available_balance, $mrfee->out_money), 2);
                $fee_record = clone $money_record;
                $fee_record->type = MoneyRecord::TYPE_DRAW_FEE_RETURN;
                $fee_record->in_money = $mrfee->out_money;
                $fee_record->balance = $draw->user->lendAccount->available_balance;
                $fee_record->save(false);
            }
            $draw->user->lendAccount->drawable_balance = $bc->bcround(bcadd($draw->user->lendAccount->drawable_balance, $draw->money), 2);
            $draw->user->lendAccount->in_sum = $bc->bcround(bcadd($draw->user->lendAccount->in_sum, $draw->money), 2);
            $draw->user->lendAccount->freeze_balance = $bc->bcround(bcsub($draw->user->lendAccount->freeze_balance, $draw->money), 2);
            if (!$money_record->save() || !$draw->user->lendAccount->save()) {
                $transaction->rollBack();
                throw new DrawException("审核失败");
            }
        }
        $transaction->commit();
        return $draw;
    }

}
