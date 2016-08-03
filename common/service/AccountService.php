<?php

namespace common\service;

use common\models\TradeLog;
use common\models\user\UserAccount;
use common\utils\TxUtils;
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
            'type' => (RechargeRecord::PAY_TYPE_POS === (int)$recharge->pay_type) ? MoneyRecord::TYPE_RECHARGE_POS : MoneyRecord::TYPE_RECHARGE,
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

    /**
     * 给指定用户转账
     * @param User $user
     * @param float $money
     * @return bool
     * @throws \Exception
     */
    public static function userTransfer(User $user, $money)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $account = $user->lendAccount;
        if (!$account) {
            throw new \Exception('用户资金账户不存在');
        }
        //更改用户账户资金
        $bcround = new BcRound();
        $account->account_balance = $bcround->bcround(bcadd($account->account_balance, $money), 2);
        $account->available_balance = $bcround->bcround(bcadd($account->available_balance, $money), 2);
        $account->drawable_balance = $bcround->bcround(bcadd($account->drawable_balance, $money), 2);
        $account->in_sum = $bcround->bcround(bcadd($account->in_sum, $money), 2);
        if (!$account->save()) {
            $transaction->rollBack();
            return false;
        }
        $sn = TxUtils::generateSn('CG');
        //记流水账
        $mre_model = new MoneyRecord();
        $mre_model->type = MoneyRecord::TYPE_CASH_GIFT;
        $mre_model->sn = MoneyRecord::createSN();
        $mre_model->osn = $sn;
        $mre_model->account_id = $account->id;
        $mre_model->uid = $user->id;
        $mre_model->in_money = $money;
        $mre_model->remark = $user->real_name . ' 的 ' . $money . '元现金红包已发放';
        $mre_model->balance = $account->available_balance;
        if (!$mre_model->save()) {
            $transaction->rollBack();
            return false;
        }
        //请求联动,接口编号 transfer
        $time = time();
        $epayUserId = $user->epayUser->epayUserId;
        $ret = Yii::$container->get('ump')->transferToUser($sn, $epayUserId, $money, $time);
        if (!$ret->isSuccessful()) {
            $transaction->rollBack();
            return false;
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
        if (!$log->save()) {
            $transaction->rollBack();
            return false;
        }
        $transaction->commit();
        return true;
    }
}
