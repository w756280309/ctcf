<?php
/**
 * 定时任务文件.
 * User: xmac
 * Date: 15-3-19
 * Time: 下午3:51.
 */
namespace console\controllers;

use Yii;
use yii\console\Controller;
use common\models\user\UserAccount;
use common\lib\bchelp\BcRound;
use common\models\user\MoneyRecord;
use common\models\user\RechargeRecord;
use PayGate\Cfca\Message\Request1320;
use common\lib\cfca\Cfca;
use PayGate\Cfca\Response\Response1320;
use common\models\sms\SmsMessage;

class BrechargecrontabController extends Controller
{
    /**
     * 发起未充值成功的充值查询.
     */
    public function actionLaunch()
    {
        $recharges = RechargeRecord::find()->where(['status' => RechargeRecord::STATUS_NO, 'pay_type' => RechargeRecord::PAY_TYPE_NET])->orderBy('id desc')->all();
        $cfca = new Cfca();
        $bc = new BcRound();
        bcscale(14);
        foreach ($recharges as $rc) {
            $rq1320 = new Request1320(Yii::$app->params['cfca']['institutionId'], $rc->sn);
            $resp = $cfca->request($rq1320);
            $rp1320 = new Response1320($resp->getText());
            if ($rp1320->isSuccess()) {
                $mr = MoneyRecord::findOne(['type' => MoneyRecord::TYPE_RECHARGE, 'osn' => $rc->sn]);
                if ($mr === null) {
                    $user_acount = UserAccount::findOne(['type' => UserAccount::TYPE_LEND, 'uid' => $rc->uid]);

                    //添加交易流水
                    $money_record = new MoneyRecord();
                    $money_record->sn = MoneyRecord::createSN();
                    $money_record->type = MoneyRecord::TYPE_RECHARGE;
                    $money_record->osn = $rc->sn;
                    $money_record->account_id = $user_acount->id;
                    $money_record->uid = $rc->uid;
                    $money_record->balance = $bc->bcround(bcadd($user_acount->available_balance, $rc->fund), 2);
                    $money_record->in_money = $rc->fund;

                    //录入user_acount记录
                    $user_acount->uid = $user_acount->uid;
                    $user_acount->account_balance = $bc->bcround(bcadd($user_acount->account_balance, $rc->fund), 2);
                    $user_acount->available_balance = $bc->bcround(bcadd($user_acount->available_balance, $rc->fund), 2);
                    $user_acount->in_sum = $bc->bcround(bcadd($user_acount->in_sum, $rc->fund), 2);

                    $user_acount->save();
                    $money_record->save();
                    $rc->status = RechargeRecord::STATUS_YES;
                    $rc->save();
                    RechargeRecord::updateAll(['status' => 1, 'bankNotificationTime' => $rp1320->getBanknotificationtime()], ['id' => $rc->id]);

                    $user = $user_acount->user;
                    $message = [
                        $user->real_name,
                        $rc->fund,
                        Yii::$app->params['contact_tel']
                    ];
                    $sms = new SmsMessage([
                        'uid' => $user->id,
                        'template_id' => Yii::$app->params['sms']['recharge'],
                        'mobile' => $user->mobile,
                        'message' => json_encode($message),
                    ]);
                    $sms->save();
                }
            }
        }
    }
}
