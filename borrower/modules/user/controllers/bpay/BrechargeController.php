<?php

namespace app\modules\user\controllers\bpay;

use Yii;
use common\lib\bchelp\BcRound;
use yii\web\Controller;
use common\models\user\UserAccount;

class BrechargeController extends Controller
{

    /**
     * 融资用户充值前台通知接口
     */
    public function actionFrontendNotify()
    {

    }

    /**
     * 融资用户充值后台通知接口
     */
    public function actionBackendNotify()
    {

    }

    /**
     * 1.融资用户入金
     * 2.记录充值流水
     * 3.更新充值记录状态
     */
    protected function is_updateRechargedb(RechargeRecord $recharge, User $user)
    {
        if ($recharge->status === RechargeRecord::STATUS_YES) {
            return true;
        } else {
            $uid = $user->id;
            $user_acount = UserAccount::findOne(['type' => UserAccount::TYPE_BORROW, 'uid' => $uid]);

            $bc = new BcRound();
            bcscale(14);
            $transaction = Yii::$app->db->beginTransaction();
            //修改充值状态
            $res = RechargeRecord::updateAll(['status' => 1, 'bankNotificationTime' => $recharge->bankNotificationTime], ['id' => $recharge->id]);
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
                'uid' => $uid,
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
        return false;
    }

}
