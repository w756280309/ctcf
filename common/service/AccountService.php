<?php

namespace common\service;

use Yii;
use common\models\user\RechargeRecord;
use common\lib\bchelp\BcRound;
use common\models\sms\SmsMessage;
use common\models\user\MoneyRecord;
use common\models\user\User;

class AccountService
{

    /**
     * 充值成功时,作如下操作:
     * 1.融资用户入金
     * 2.记录充值流水
     * 3.更新充值记录状态为成功
     */
    public function confirmRecharge(RechargeRecord $recharge)
    {
        if (RechargeRecord::STATUS_NO !== $recharge->status) {
            return true;
        }

        $user = $recharge->user;
        $user_acount = $user->type === User::USER_TYPE_PERSONAL ? $user->lendAccount : $user->borrowAccount;

        $bc = new BcRound();
        bcscale(14);
        $transaction = Yii::$app->db->beginTransaction();
        //修改充值状态
        $res = RechargeRecord::updateAll(['status' => RechargeRecord::STATUS_YES], ['id' => $recharge->id]);
        if (!$res) {
            $transaction->rollBack();
            return false;
        }
        //添加交易流水
        $money_record = new MoneyRecord([
            'sn' => MoneyRecord::createSN(),
            'type' => MoneyRecord::TYPE_RECHARGE,
            'osn' => $recharge->sn,
            'account_id' => $user_acount->id,
            'uid' => $user->id,
            'balance' => $bc->bcround(bcadd($user_acount->available_balance, $recharge->fund), 2),
            'in_money' => $recharge->fund,
        ]);

        if (!$money_record->save()) {
            $transaction->rollBack();
            return false;
        }

        //录入user_acount记录
        $user_acount->uid = $user_acount->uid;
        $user_acount->account_balance = $bc->bcround(bcadd($user_acount->account_balance, $recharge->fund), 2);
        $user_acount->available_balance = $bc->bcround(bcadd($user_acount->available_balance, $recharge->fund), 2);
        $user_acount->in_sum = $bc->bcround(bcadd($user_acount->in_sum, $recharge->fund), 2);
        if (!$user_acount->save()) {
            $transaction->rollBack();
            return false;
        }

        $message = [
            $user->real_name,
            $recharge->fund,
            Yii::$app->params['contact_tel']
        ];
        $sms = new SmsMessage([
            'uid' => $user->id,
            'template_id' => Yii::$app->params['sms']['recharge'],
            'mobile' => $user->mobile,
            'level' => SmsMessage::LEVEL_LOW,
            'message' => json_encode($message)
        ]);
        $sms->save();

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

}
