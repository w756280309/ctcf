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
use common\models\user\User;
use common\lib\bchelp\BcRound;
use common\models\checkaccount\CheckaccountWdjf;
use common\models\user\RechargeRecord;
use common\models\user\Jiesuan;
use PayGate\Cfca\Settlement\AccountSettlement;
use PayGate\Cfca\Message\Request1341;
use PayGate\Cfca\Message\Request1350;
use common\models\TradeLog;
use common\lib\cfca\Cfca;

class SettlementcrontabController extends Controller
{
    /**
     * 发起今日结算请求【建议频率高些】.
     */
    public function actionLaunchsettlement()
    {
        $data = RechargeRecord::find()->where(['status' => 1, 'settlement' => 0])->all(); //找到所有未结算的
        $cfca = new Cfca();
        foreach ($data as $dat) {
            $cpuser = User::findOne($dat->uid); //目前只针对投资用户发起结算
            if (null !== $cpuser->lendAccount) {
                $asettlement = new AccountSettlement($dat);
                $rq1341 = new Request1341(Yii::$app->params['cfca']['institutionId'], $asettlement);
                $jiesuan = new Jiesuan([
                    'sn' => $rq1341->getSettlementSn(),
                    'osn' => $dat->sn,
                    'pay_id' => 0, //0代表中金
                    'type' => 1,
                    'amount' => $dat->fund,
                    'bank_id' => Request1341::BANK_ID, //本平台赋予的银行的id
                    'pay_bank_id' => Request1341::BANK_ID, //支付公司银行id，
                    'accountname' => Request1341::ACCOUNT_NAME,
                    'accountnumber' => Request1341::ACCOUNT_NUMBER,
                    'branchname' => Request1341::BRANCH_NAME,
                    'province' => Request1341::PROVINCE,
                    'city' => Request1341::CITY,
                ]);
                if ($jiesuan->validate() && $jiesuan->save()) {
                    //成功之后发起结算
                    $resp = $cfca->request($rq1341);

                    //记录日志
                    $log = new TradeLog($cpuser, $rq1341, $resp);
                    $log->save();

                    if ($resp->isSuccess()) {
                        RechargeRecord::updateAll(['settlement' => RechargeRecord::SETTLE_ACCEPT], ['id' => $dat->id]); //修改为已经受理
                    }
                }
            }
        }
    }

    /**
     * 批处理结算订单的状态修改【建议频率高些】.
     */
    public function actionBatchsettlement()
    {
        $data = Jiesuan::find()->where(['status' => [Jiesuan::STATUS_NO, Jiesuan::STATUS_ACCEPT, Jiesuan::STATUS_IN]])->select('id,sn,osn,amount')->orderBy('id desc')->all();//
        if (!empty($data)) {
            $bcround = new BcRound();
            $cfca = new Cfca();
            $date = date('Y-m-d', strtotime('-1 day'));//获取前日
            foreach ($data as $dat) {
                $rq1350 = new Request1350(\Yii::$app->params['cfca']['institutionId'], $dat->sn);
                $resp = $cfca->request($rq1350);
                $resp1350 = new Response1350($resp->getText());
                if ($resp1350->isDone()) {
                    Jiesuan::updateAll(['status' => $resp1350->getStatus()], ['id' => $dat->id]);
                    RechargeRecord::updateAll(['settlement' => $resp1350->getStatus()], ['sn' => $dat->osn]);
                    $wdjf_model = new CheckaccountWdjf(['order_no' => $dat->osn, 'tx_date' => $date, 'tx_type' => 1341, 'tx_sn' => $dat->sn, 'tx_amount' => ($dat->amount), 'payment_amount' => 0, 'institution_fee' => 0, 'bank_notification_time' => '0']);
                    $wdjf_model->save();
                    if ($resp1350->isSuccess()) {
                        $recharge = RechargeRecord::findOne(['sn' => $dat->osn]);
                        $lendAccount = $recharge->user->lendAccount;
                        $lendAccount->drawable_balance = $bcround->bcround(bcadd($lendAccount->drawable_balance, $dat->amount), 2);//结算成功之后方可更新可提现金额
                        $lendAccount->save();
                    }
                }
            }
        } else {
        }
    }
}
