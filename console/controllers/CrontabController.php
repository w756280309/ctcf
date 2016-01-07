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
use common\models\product\OnlineProduct;
use common\models\order\OnlineOrder;
use common\models\user\User;
use common\models\user\UserAccount;
use common\lib\bchelp\BcRound;
use common\models\user\MoneyRecord;
use common\models\checkaccount\CheckaccountCfca;
use common\models\checkaccount\CheckaccountWdjf;
use common\models\checkaccount\CheckaccountHz;
use common\models\user\RechargeRecord;
use common\models\user\Jiesuan;
use PayGate\Cfca\Message\Request1510;
use PayGate\Cfca\Message\Request1520;
use PayGate\Cfca\Settlement\AccountSettlement;
use PayGate\Cfca\Message\Request1341;
use PayGate\Cfca\Message\Request1320;
use PayGate\Cfca\Message\Request1350;
use PayGate\Cfca\Message\Request1810;
use common\models\TradeLog;
use common\lib\cfca\Cfca;
use PayGate\Cfca\Response\Response1320;
use PayGate\Cfca\Response\Response1350;
use PayGate\Cfca\Response\Response1810;
use PayGate\Cfca\Response\Response1520;
use common\models\user\Batchpay;
use common\models\user\DrawRecord;
use common\models\sms\SmsMessage;

class CrontabController extends Controller
{
    /**
     * 定时 刷新满标 满标生成还款计划.
     */
    public function actionUpdatefull()
    {
        $data = OnlineProduct::find()->where(['finish_rate' => 1, 'status' => 2])->all();
        foreach ($data as $dat) {
            $pid = $dat['id'];
            OnlineProduct::updateAll(['status' => 3, 'sort' => OnlineProduct::SORT_FULL], ['id' => $pid]);
            //$orders = OnlineOrder::find()->where(['online_pid'=>$pid,'status'=>  OnlineOrder::STATUS_SUCCESS])->asArray()->select('id,order_money,refund_method,yield_rate,expires,uid,order_time')->all();
            //OnlineRepaymentPlan::createPlan($pid,$orders);//转移到开始计息部分
        }
    }
    /**
     * 定时 修改预告期为募集期
     */
    public function actionUpdatenow()
    {
        OnlineProduct::updateAll(['status' => 2, 'sort' => OnlineProduct::SORT_NOW], ' online_status=1 and status=1 and start_date<='.time());
    }

    /**
     * 定时 修改募集期状态为流标状态
     */
    public function actionUpdateliu()
    {
        $product = OnlineProduct::find()->where(['del_status' => OnlineProduct::STATUS_USE, 'online_status' => OnlineProduct::STATUS_ONLINE, 'status' => OnlineProduct::STATUS_NOW]);
        $product = $product->andFilterWhere(['<', 'end_date', time()])->all();
        //var_dump($product);exit;

        $bc = new BcRound();
        bcscale(14);
        $transaction = Yii::$app->db->beginTransaction();
        foreach ($product as $val) {
            $order = OnlineOrder::find()->where(['online_pid' => $val['id'], 'status' => OnlineOrder::STATUS_SUCCESS])->all();
            foreach ($order as $v) {
                $ua = UserAccount::findOne(['uid' => $v['uid']]);

                $ua->freeze_balance = $bc->bcround(bcsub($ua->freeze_balance, $v['order_money']), 2);
                $ua->available_balance = $bc->bcround(bcadd($ua->available_balance, $v['order_money']), 2);
                $ua->out_sum = $bc->bcround(bcsub($ua->out_sum, $v['order_money']), 2);

                if (!$ua->save()) {
                    $transaction->rollBack();

                    return false;
                }

                $v->status = OnlineOrder::STATUS_CANCEL;
                if (!$v->save()) {
                    $transaction->rollBack();

                    return false;
                }

                $money_record = new MoneyRecord();
                $money_record->sn = MoneyRecord::createSN();
                $money_record->type = MoneyRecord::TYPE_ORDER;
                $money_record->osn = $v->sn;
                $money_record->account_id = $ua->id;
                $money_record->uid = $v['uid'];
                $money_record->balance = $ua->available_balance;
                $money_record->in_money = $v['order_money'];

                if (!$money_record->save()) {
                    $transaction->rollBack();

                    return false;
                }
            }

            $val->scenario = 'status';
            $val->status = OnlineProduct::STATUS_LIU;
            if (!$val->save()) {
                $transaction->rollBack();

                return false;
            }
        }

        if ($product) {
            $transaction->commit();

            return true;
        }
        echo 1;

        return false;
    }

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

    /**
     * 获取中金对账单【中金每日凌晨5时生成上一日对账单】
     * 建议在凌晨5时之后执行上一日的对账单.
     */
    public function actionGetcfcacheckaccount()
    {
        $date = date('Y-m-d', strtotime('-1 day'));//获取前日
        $rq1810 = new Request1810(Yii::$app->params['cfca']['institutionId'], $date);
        $cfca = new Cfca();
        $resp = $cfca->request($rq1810);
        $rp1810 = new Response1810($resp->getText());
        //echo date('Y-m-d H:i:s',strtotime('20150118090808'));exit;
        $connection = \Yii::$app->db;
        $data = array();
        $time = time();
        $notes = $rp1810->getTxs();
        while (list(, $tx) = each($notes)) {
            $banknotificationtime = empty($tx['BankNotificationTime']) ? '0' : date('Y-m-d H:i:s', strtotime($tx['BankNotificationTime']));
            $data[] = [$date, $tx['TxType'], $tx['TxSn'], bcdiv($tx['TxAmount'], 100), $tx['PaymentAmount'], $tx['InstitutionAmount'], $banknotificationtime, $time, $time];
        }
        //var_dump($data);exit;
        if (!empty($data)) {
            $res = $connection->createCommand()->batchInsert(CheckaccountCfca::tableName(), ['tx_date', 'tx_type', 'tx_sn', 'tx_amount', 'payment_amount', 'institution_fee', 'bank_notification_time', 'created_at', 'updated_at'],
                    $data)->execute();
            if ($res) {
                echo 'success';
            } else {
                /////失败代码处理
            }
        }
        exit;
    }

    /**
     * 获取温都金服充值订单前一日【结算在结算定时任务中完成】
     * 建议在凌晨0点至5点之间运行。要保证5点之前执行完毕.
     */
    public function actionGetwdjfcheckaccount()
    {
        $date = date('Y-m-d', strtotime('-1 day'));//获取前日
        echo $date;
        $beginYesterday = mktime(0, 0, 0, date('m'), date('d') - 1, date('Y'));
        $endYesterday = mktime(0, 0, 0, date('m'), date('d'), date('Y')) - 1;
        $is_write = CheckaccountWdjf::find()->where(['tx_date' => $date])->count('id');
        if ($is_write) {
            return false;
        }

        $dataobj = RechargeRecord::find()->where(['status' => RechargeRecord::STATUS_YES])->andFilterWhere(['between', 'bankNotificationTime', date('Y-m-d H:i:s', $beginYesterday), date('Y-m-d H:i:s', $endYesterday)])->all();
        //var_dump($dataobj);exit;
        $insert_arr = array();
        $time = time();
        foreach ($dataobj as $dat) {
            $insert_arr[] = [$dat->sn, $date, 1341, $dat->sn, $dat->fund, 0, 0, $dat->bankNotificationTime,  $time, $time];
        }
        if (!empty($insert_arr)) {
            $connection = \Yii::$app->db;
            $res = $connection->createCommand()->batchInsert(CheckaccountWdjf::tableName(), ['order_no', 'tx_date', 'tx_type', 'tx_sn', 'tx_amount', 'payment_amount', 'institution_fee', 'bank_notification_time', 'created_at', 'updated_at'],
                    $insert_arr)->execute();
            if ($res) {
            } else {
                /////失败代码处理
                echo 'fail';
            }
        }

        return true;
    }

    /**
     * 温度金服的对账单与中金对账单做比对【建议在凌晨5点之后进行】.
     */
    public function actionComparebill()
    {
        $date = date('Y-m-d', strtotime('-1 day'));//获取前日
        //echo $date;
        $list = (new \yii\db\Query())
                ->select('w.*,c.tx_amount c_tx_amount')
                ->from([CheckaccountWdjf::tableName().' as w'])
                ->innerJoin(CheckaccountCfca::tableName().' as c', 'c.tx_sn=w.tx_sn')
                ->where(['w.tx_date' => $date, 'c.tx_date' => $date])->all();//只校正交易金额tx_amount
        //var_dump($list);exit;
        $false_ids = array();
        $success_ids = array();
        foreach ($list as $data) {
            if ($data['is_checked'] == 1) {
                //对于已经执行的。不能再执行了
                echo 'has been implemented';
                exit;
            }
            if (bccomp($data['tx_amount'], $data['c_tx_amount']) === 0) {
                $success_ids[] = $data['id'];
            } else {
                $false_ids[] = $data['id'];
            }
        }

        if (!empty($false_ids)) {
            CheckaccountWdjf::updateAll(['is_checked' => 1, 'is_auto_okay' => 2], ['id' => $false_ids]);
        }
        if (!empty($success_ids)) {
            CheckaccountWdjf::updateAll(['is_checked' => 1, 'is_auto_okay' => 1], ['id' => $success_ids]);
        }
        echo 'finished';
    }

    /**
     * 每日汇总对账单【建议在执行完对账之后执行Comparebill】.
     */
    public function actionCheckaccounthz()
    {
        bcscale(14);
        $date = date('Y-m-d', strtotime('-1 day'));//获取前日
        //先判断有没有录入过
        $beginYesterday = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - 1, date('Y')));
        $endYesterday = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d'), date('Y')) - 1);
//        $beginThisMonth=date('Y-m-d', mktime(0,0,0,date('m'),1,date('Y')));//本月的开始日期
//        $endThisMonth=date('Y-m-d', mktime(0,0,0,date('m'),date('t'),date('Y')));//本月的结束日期
        $count = CheckaccountHz::find()->filterWhere(['between', 'tx_date', $beginYesterday, $endYesterday])->count();
        if ($count) {
            echo 'has been implemented';
            exit;
        }

        $wdjfobj = CheckaccountWdjf::find()->where('is_checked=1 and (is_auto_okay=1 or (is_auto_okay=2 and is_okay=1))')->andFilterWhere(['between', 'tx_date', $beginYesterday, $endYesterday])->all();
        //var_dump($wdjfobj);exit;
        $recharge_count = $recharge_sum = $jiesuan_count = $jiesuan_sum = 0;
        foreach ($wdjfobj as $obj) {
            if ($obj->tx_type == 1311) {
                //充值
                ++$recharge_count;
                $recharge_sum = bcadd($recharge_sum, $obj->tx_amount);
            } elseif ($obj->tx_type == 1341) {
                //结算
                ++$jiesuan_count;
                $jiesuan_sum = bcadd($jiesuan_sum, $obj->tx_amount);
            }
        }
        $hzmodel = new CheckaccountHz();
        $bcround = new BcRound();
        $hzmodel->tx_date = $date;
        $hzmodel->recharge_count = $recharge_count;
        $hzmodel->recharge_sum = $bcround->bcround($recharge_sum, 2);
        $hzmodel->jiesuan_count = $jiesuan_count;
        $hzmodel->jiesuan_sum = $bcround->bcround($jiesuan_sum, 2);
        if ($hzmodel->validate()) {
            $hzmodel->save();
            echo 'finish!';
        } else {
            print_r($hzmodel->getErrors());
        }
    }

    /**
     * 发起批量代付请求
     */
    public function actionLaunchbatchpay()
    {
        $batchpays = Batchpay::find()->where(['is_launch' => Batchpay::IS_LAUNCH_NO])->all();
        $cfca = new Cfca();
        foreach ($batchpays as $batchpay) {
            $request1510 = new Request1510(Yii::$app->params['cfca']['institutionId'], $batchpay);
            $resp = $cfca->request($request1510);
            if ($resp->isSuccess()) {
                $batchpay->is_launch = Batchpay::IS_LAUNCH_YES;
                $batchpay->save(false);
            }
        }
    }

    /**
     * 次日查询前一日的结果.
     */
    public function actionBatchpayupdate()
    {
        $beginYesterday = mktime(0, 0, 0, date('m'), date('d') - 1, date('Y'));
        $endYesterday = mktime(0, 0, 0, date('m'), date('d'), date('Y')) - 1;
        $cfca = new Cfca();
        $bc = new BcRound();
        $yesbatchpay = Batchpay::find()->where(['is_launch' => Batchpay::IS_LAUNCH_YES])->andFilterWhere(['between', 'created_at', $beginYesterday, $endYesterday])->all();
        foreach ($yesbatchpay as $batchpay) {
            $request1520 = new Request1520(Yii::$app->params['cfca']['institutionId'], $batchpay->sn);
            $resp = $cfca->request($request1520);
            $rp1520 = new Response1520($resp->getText());
            $items = $rp1520->getItems();
            foreach ($items as $item) {
                if ($rp1520->isDone($item)) {
                    $batchpayItems = $batchpay->items;
                    $batchpayItem = $batchpayItems[0];
                    $batchpayItem->status = $item['Status'];
                    $batchpayItem->banktxtime = $item['BankTxTime'];
                    $batchpayItem->save(false);
                    $drawRord = DrawRecord::findOne(['sn' => $item['ItemNo']]);
                    $money = bcdiv($item['Amount'], 100, 2);//返回分制转为元制
                    $userAccount = UserAccount::find()->where('uid = '.$drawRord->uid)->one();
                    $userAccount->freeze_balance = $bc->bcround(bcsub($userAccount->freeze_balance, $money), 2);//冻结减少
                    $draw_status = 0;
                    $momeyRecord = new MoneyRecord();
                    //生成一个SN流水号
                    $sn = $momeyRecord::createSN();
                    $momeyRecord->uid = $drawRord->uid;
                    $momeyRecord->sn = $sn;
                    $momeyRecord->account_id = $userAccount->id;
                    if ($rp1520->isSuccess($item)) {
                        //成功的
                        $draw_status = DrawRecord::STATUS_SUCCESS;
                        $YuE = $userAccount->account_balance = $bc->bcround(bcsub($userAccount->account_balance, $money), 2);//账户总额减少
                        $userAccount->out_sum = $bc->bcround(bcadd($userAccount->available_balance, $money), 2);//更新出账
                        $momeyRecord->type = MoneyRecord::TYPE_DRAW;
                        $momeyRecord->balance = $YuE;
                        $momeyRecord->out_money = $money;
                    } else {
                        //失败
                        $draw_status = DrawRecord::STATUS_FAIL;//提现不成功
                        $YuE = $userAccount->account_balance = $bc->bcround(bcadd($userAccount->account_balance, $money), 2);//账户总额增加
                        $userAccount->available_balance = $bc->bcround(bcadd($userAccount->available_balance, $money), 2);//更新可用余额
                        $userAccount->in_sum = $bc->bcround(bcadd($userAccount->available_balance, $money), 2);//更新入账
                        $momeyRecord->type = MoneyRecord::TYPE_DRAW_RETURN;
                        $momeyRecord->balance = $YuE;
                        $momeyRecord->in_money = $money;
                    }
                    $momeyRecord->save();
                    $userAccount->save();
                    $drawRord->status = DrawRecord::STATUS_SUCCESS;
                    $drawRord->save();
                }
            }
        }
    }

    /**
     * 发起未充值成功的充值查询.
     */
    public function actionLaunchrecharge()
    {
        $recharges = RechargeRecord::find()->where(['status' => RechargeRecord::STATUS_NO, 'pay_type' => RechargeRecord::PAY_TYPE_NET])->orderBy('id desc')->limit(1)->all();
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
                }
            }
        }
    }
    
    /**
     * 短信发送任务[文件锁]
     */
    public function actionSms()
    {
        $messages = SmsMessage::find()->where(['status' => SmsMessage::STATUS_WAIT])->orderBy('id desc')->all();
        foreach ($messages as $msg) {
            $result = \Yii::$container->get('sms_lib')->send($msg);
            if ($result) {
                $msg->status = SmsMessage::STATUS_SENT;
            } else {
                $msg->status = SmsMessage::STATUS_FAIL;
            }
            $msg->save(false);
        }
    }
}
