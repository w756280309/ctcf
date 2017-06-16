<?php

namespace common\models\order;

use common\models\message\OrderMessage;
use common\models\product\OnlineProduct;
use common\models\promo\PromoService;
use common\models\user\UserInfo;
use common\models\product\OnlineProduct as Loan;
use common\lib\bchelp\BcRound;
use common\models\user\MoneyRecord;
use common\models\user\User;
use common\models\product\RateSteps;
use common\models\affiliation\AffiliationManager;
use common\models\coupon\UserCoupon;
use common\service\PayService;
use common\service\SmsService;
use common\utils\TxUtils;
use common\utils\SecurityUtils;
use Ding\DingNotify;
use Exception;
use Lhjx\Noty\Noty;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * 订单manager.
 *
 * @author zhanghongyu<zhanghongyu@wangcaigu.com>
 */
class OrderManager
{
    private $pz = 5;//每页尺寸
    /**
     * 用于标的满标超投部分撤销退款.
     *
     * @param OnlineOrder $order
     *
     * @return OnlineOrder
     *
     * @throws \Exception
     */
    public static function findInvalidOrders(Loan $loan)
    {
        bcscale(14);
        $bc = new BcRound();
        $orders = OnlineOrder::find()->where(['online_pid' => $loan->id, 'status' => OnlineOrder::STATUS_SUCCESS])->orderBy('id asc')->all();//升序排列
        $excessMoney = 0;//超额总计
        $current_sum = 0;
        foreach ($orders as $ord) {
            $current_sum = bcadd($ord->order_money, $current_sum);
            if (bccomp($current_sum, $loan->money) > 0) {
                $returnMoney = null;
                if (bccomp(bcsub($current_sum, $loan->money), $ord->order_money) < 0) { //计算超标的第一个订单超标金额
                    $returnMoney = bcsub($current_sum, $loan->money);
                } else {
                    $returnMoney = null;
                }
                $excessMoney = bcadd($excessMoney, (null === $returnMoney) ? $ord->order_money : $returnMoney);
                self::cancelOrder($ord, $returnMoney);
            }
        }
        Loan::updateAll(['funded_money' => $bc->bcround(bcsub($loan->funded_money, $excessMoney), 2)], 'id='.$loan->id);//更新标的实际募集金额
    }

    /**
     * 创建取消订单执行方法.
     *
     * @param OnlineOrder $order
     * @param type        $ret_money
     *
     * @return OnlineOrder
     *
     * @throws \Exception
     */
    public static function cancelOrder(OnlineOrder $order, $ret_money = null)
    {
        if (null !== $ret_money && bccomp($order->order_money, $ret_money) < 0) {
            throw new \Exception('申请退款金额超出订单金额');
        }
        $bc = new BcRound();
        bcscale(14);
        $resp = Yii::$container->get('ump')->getOrderInfo($order);
        if (!$resp->isSuccessful() || '2' !== $resp->get('tran_state')) {
            throw new \Exception('交易状态异常或者查询失败');
        }
        $loan = Loan::findOne($order->online_pid);
        $transaction = Yii::$app->db->beginTransaction();
        $cancelOrder = CancelOrder::initForOrder($order, $ret_money);
        if (!$cancelOrder->save()) {
            $transaction->rollBack();
            throw new \Exception('交易创建失败');
        }

        //账户资金变动修改
        $ua = $order->user->lendAccount;

        //如果部分超标。不超标的部分可以投标
        if (null !== $ret_money) {
            $data = ArrayHelper::toArray($order);
            unset($data['id']);
            unset($data['sn']);
            $new_ord = new OnlineOrder($data);
            $new_ord->sn = TxUtils::generateSn();
            $new_ord->order_money = $bc->bcround(bcsub($order->order_money, $ret_money), 2);
            $new_ord->save();//创建一个订单

            $ua->available_balance = $bc->bcround(bcsub($ua->available_balance, $new_ord->order_money), 2);
            //$ua->freeze_balance = $bc->bcround(bcadd($ua->freeze_balance, $new_ord->order_money), 2);
            $ua->out_sum = $bc->bcround(bcadd($ua->out_sum, $new_ord->order_money), 2);//ua有修改在判断外层save

            $mrmodel = new MoneyRecord();
            $mrmodel->account_id = $ua->id;
            $mrmodel->sn = TxUtils::generateSn('MR');
            $mrmodel->type = MoneyRecord::TYPE_ORDER;
            $mrmodel->osn = $new_ord->sn;
            $mrmodel->uid = $new_ord->uid;
            $mrmodel->balance = $ua->available_balance;
            $mrmodel->out_money = $new_ord->order_money;
            $mrmodel->remark = '资金流水号:'.$mrmodel->sn.',订单流水号:'.($order->sn).',账户余额:'.($ua->account_balance).'元，可用余额:'.($ua->available_balance).'元，冻结金额:'.$ua->freeze_balance.'元。';
            $mrmodel->save();//创建一个资金记录
        }

        $changeMoney = null === $ret_money ? $order->order_money : $ret_money;//计算更改的金额
        //$ua->freeze_balance = $bc->bcround(bcsub($ua->freeze_balance, $changeMoney), 2);//由于满标时候冻结金额已经解冻。所以此处不应该再减了
        $ua->available_balance = $bc->bcround(bcadd($ua->available_balance, $changeMoney), 2);
        $ua->drawable_balance = $bc->bcround(bcadd($ua->drawable_balance, $changeMoney), 2);
        $ua->in_sum = $bc->bcround(bcadd($ua->in_sum, $changeMoney), 2);
        if (!$ua->save()) {
            $transaction->rollBack();
            throw new \Exception('账户变动失败');
        }
        $order->status = OnlineOrder::STATUS_CANCEL;
        if (!$order->save()) {
            $transaction->rollBack();
            throw new \Exception('订单状态修改失败');
        }

        $money_record = new MoneyRecord();
        $money_record->sn = TxUtils::generateSn('MR');
        $money_record->type = MoneyRecord::TYPE_CANCEL_ORDER;
        $money_record->osn = $order->sn;
        $money_record->account_id = $ua->id;
        $money_record->uid = $order->uid;
        $money_record->balance = $ua->available_balance;
        $money_record->in_money = $changeMoney;
        if (!$money_record->save()) {
            $transaction->rollBack();
            throw new \Exception('资金记录失败');
        }

        //如果是阶梯利率
        if ($loan->isFlexRate) {
            $rateStepsConfig = RateSteps::parse($loan->rateSteps);//获取阶梯利率配置
            $cur_usr_total_money = self::getTotalInvestment($loan, $order->user);//获取当前投标人对当前标的的总投资额
            $rate = RateSteps::getRateForAmount($rateStepsConfig, $cur_usr_total_money);
            if (!$rate) {
                $rate = $loan->yield_rate;
            } else {
                $rate = $rate/100;
            }
            OnlineOrder::updateAll(["yield_rate" => $rate], ["online_pid" => $loan->id, "uid" => $order->user->id, "status" => OnlineOrder::STATUS_SUCCESS]);
        }

        //联动标的转账
        $trans_resp = Yii::$container->get('ump')->loanTransferToLender($cancelOrder);
        if (!$trans_resp->isSuccessful()) {
            $transaction->rollBack();
            throw new \Exception('联动标的转账失败');
        }
        $transaction->commit();

        return $order;
    }

    /**
     * 判断当前订单是否超投。然后需要对这笔订单进行联动一侧的资金转账.
     *
     * @param $ordOrSn 可以为订单对象或者订单号
     *
     * @return bool 如果返回true代表当前是订单撤销成功。false则不是超标订单
     *
     * @throws \Exception
     */
    public static function cancelNoPayOrder($ordOrSn)
    {
        bcscale(14);
        $ord = OnlineOrder::ensureOrder($ordOrSn);
        if (OnlineOrder::STATUS_FALSE !== $ord->status) {
            throw new \Exception('状态异常');
        }
        //查找截止当前订单是否超投
        $loan = Loan::findOne($ord->online_pid);
        $orderbalance = $loan->getLoanBalance();//标的剩余可投金额
        $lastAmount = bcsub($orderbalance, $ord->order_money, 2);//此笔交易成功后的剩余资金
        $com = bccomp($lastAmount, 0, 2);
        if ($com === 0) {
            //刚好投完
            return false;//不是超投
        } elseif ($com > 0) {
            //有剩余资金
            if (bccomp($lastAmount, $loan->start_money, 2) >= 0) {
                //剩余资金超过或者等于起投金额
                return false;//不是超投
            }
        }

        $transaction = Yii::$app->db->beginTransaction();
        try {
            UserCoupon::unuseCoupon($ord);

            $cancelOrder = CancelOrder::initForOrder($ord, $ord->paymentAmount);
            $cancelOrder->txStatus = CancelOrder::ORDER_CANCEL_SUCCESS;
            if (!$cancelOrder->save()) {
                throw new \Exception('撤销交易创建失败');
            }
            $ord->status = OnlineOrder::STATUS_CANCEL;
            if (!$ord->save()) {
                throw new \Exception('状态修改失败');//
            }
            //联动标的转账
            $trans_resp = Yii::$container->get('ump')->loanTransferToLender($cancelOrder);
            if (!$trans_resp->isSuccessful()) {
                throw new \Exception('联动标的转账失败');
            }
            OrderQueue::updateAll(['status' => 1], 'orderSn='.$ord->sn);
            $transaction->commit();

            return true;
        } catch (\Exception $ex) {
            $transaction->rollBack();
            //记录异常日志
            $msg = "超投处理    订单ID:" . $ord->id . "   错误信息：" . $ex->getMessage() . PHP_EOL;
            Yii::trace($msg, 'loan_order');
            throw $ex;
        }
    }

    /**
     * 定时确认撤销订单的状态
     *
     * @param CancelOrder $ord
     */
    public static function ackCancelOrder(CancelOrder $ord)
    {
        if (CancelOrder::ORDER_CANCEL_ACK !== $ord->txStatus) {
            throw new \Exception('状态异常');
        }
        $trans_resp = Yii::$container->get('ump')->getOrderInfo($ord);
        if ($trans_resp->isSuccessful()) {
            if ('2' === $trans_resp->get('tran_state')) {
                $ord->txStatus = CancelOrder::ORDER_CANCEL_SUCCESS;
            } elseif ('0' !== $trans_resp->get('tran_state')) {
                $ord->txStatus = CancelOrder::ORDER_CANCEL_FAIL;
            }
            $ord->save(false);

            return $ord;
        } else {
            throw new \Exception('联动一侧异常');
        }
    }

    public static function confirmOrder($ordOrSn)
    {
        $loanFullAndNotify = false;//是否需要满标钉钉提醒

        bcscale(14);
        $order = OnlineOrder::ensureOrder($ordOrSn);
        if (OnlineOrder::STATUS_SUCCESS === $order->status) {
            return true;
        }
        $bcrond = new BcRound();
        $loan = Loan::findOne($order->online_pid);

        $coupon = UserCoupon::findOne(['order_id' => $order->id]);
        if ($coupon && $coupon->id !== intval($order->userCoupon_id)) {
            throw new Exception("代金券使用异常");
        }
        $user = $order->user;
        $ua = $user->type === User::USER_TYPE_PERSONAL ? $user->lendAccount : false;//当前限制投资人进行投资

        if ($ua === false) {
            throw new Exception(PayService::getErrorByCode(PayService::ERROR_UA));
        }
        //用户资金表
        $ua->available_balance = bcsub($ua->available_balance, $order->paymentAmount, 2);    //调整计算精度,防止小数位丢失
        if ($ua->available_balance * 1 < 0) {
            throw new Exception(PayService::getErrorByCode(PayService::ERROR_MONEY_LESS));
        }
        $transaction = Yii::$app->db->beginTransaction();
        $order->status = OnlineOrder::STATUS_SUCCESS;
        $order->save();

        $ua->drawable_balance = $bcrond->bcround(bcsub($ua->drawable_balance, $order->paymentAmount), 2);
        $ua->freeze_balance = $bcrond->bcround(bcadd($ua->freeze_balance, $order->paymentAmount), 2);
        $ua->out_sum = $bcrond->bcround(bcadd($ua->out_sum, $order->paymentAmount), 2);
        $uare = $ua->save();
        if (!$uare) {
            $transaction->rollBack();
            throw new Exception(PayService::getErrorByCode(PayService::ERROR_UA_CAL));
        }

        //资金记录表
        $mrmodel = new MoneyRecord();
        $mrmodel->account_id = $ua->id;
        $mrmodel->sn = MoneyRecord::createSN();
        $mrmodel->type = MoneyRecord::TYPE_ORDER;
        $mrmodel->osn = $order->sn;
        $mrmodel->uid = $order->uid;
        $mrmodel->balance = $ua->available_balance;
        $mrmodel->out_money = $order->paymentAmount;
        $mrmodel->remark = '资金流水号:'.$mrmodel->sn.',订单流水号:'.($order->sn).',账户余额:'.($ua->account_balance).'元，可用余额:'.($ua->available_balance).'元，冻结金额:'.$ua->freeze_balance.'元。';
        $mrres = $mrmodel->save();
        if (!$mrres) {
            $transaction->rollBack();
            throw new Exception(PayService::getErrorByCode(PayService::ERROR_MR));
        }

        /*修改标的完成比例  后期是否需要定时更新*/
        $summoney = OnlineOrder::find()->where(['status' => 1, 'online_pid' => $loan->id])->sum('order_money');
        $insert_sum = $summoney; //包含此笔募集的总金额
        $update = array();
        if (0 <= bccomp($insert_sum, $loan->money)) {
            //投资总和与融资总额比较。如果投资总和大于等于融资总额。要完成满标状态值的修改
            $update['finish_rate'] = 1;
            $update['full_time'] = time();//由于定时任务去修改满标状态以及生成还款计划。所以此处不设置修改满标状态
            if (!$loan->finish_date) {
                $diff = \Yii::$app->functions->timediff(strtotime(date('Y-m-d', $loan->start_date)), strtotime(date('Y-m-d', $loan->finish_date)));
                Loan::updateAll(['expires' => $diff['day'] - 1], 'id='.$loan->id.' and finish_date>0'); //对于此时设置有结束日期的要校准项目天数
            }
        } else {
            $finish_rate = $bcrond->bcround(bcdiv($insert_sum, $loan->money), 2);
            if (0 === bccomp($finish_rate, 1) && 0 !== bccomp($insert_sum, $loan->money)) {
                //主要处理由于四舍五入造成的不应该募集完成的募集完成了：完成比例等于1了，并且包含此次交易成功所有金额不等于募集金额
                $finish_rate = 0.99;
            } elseif (0 === bccomp($finish_rate, 0)) {
                $finish_rate = 0.01;
            }
            $update['finish_rate'] = $finish_rate;
        }
        if ($loan->finish_rate < 0.9 && $update['finish_rate'] >= 0.9) {
            $loanFullAndNotify = true ;
        }
        $res = Loan::updateAll($update, ['id' => $loan->id]);
        if (false === $res) {
            $transaction->rollBack();
            throw new Exception(PayService::getErrorByCode(PayService::ERROR_SYSTEM));
        }
        $command = Yii::$app->db->createCommand('UPDATE '.Loan::tableName().' SET funded_money=funded_money+'.$order->order_money.' WHERE id='.$loan->id);
        $command->execute();//更新实际募集金额
        OrderQueue::updateAll(['status' => 1], 'orderSn='.$order->sn);

        //如果是阶梯利率
        if ($loan->isFlexRate) {
            $rateStepsConfig = RateSteps::parse($loan->rateSteps);//获取阶梯利率配置
            $cur_usr_total_money = self::getTotalInvestment($loan, $user);//获取当前投标人对当前标的的总投资额
            $rate = RateSteps::getRateForAmount($rateStepsConfig, $cur_usr_total_money);
            if (!$rate) {
                $rate = $loan->yield_rate;
            } else {
                $rate = $rate/100;
            }
            OnlineOrder::updateAll(["yield_rate" => $rate], ["online_pid" => $loan->id, "uid" => $user->id, "status" => OnlineOrder::STATUS_SUCCESS]);
        }

        //投标之后添加保全
        $job = new BaoQuanQueue(['itemId' => $order->id, 'status' => BaoQuanQueue::STATUS_SUSPEND, 'itemType' => BaoQuanQueue::TYPE_LOAN_ORDER]);
        $job->save();


        //投标成功，向用户发送短信
        $message = [
            $user->real_name,
            $loan->title,
            $order->paymentAmount,
            Yii::$app->params['contact_tel'],
        ];

        $templateId = Yii::$app->params['sms']['toubiao'];
        SmsService::send(SecurityUtils::decrypt($user->safeMobile), $templateId, $message, $user);

        $transaction->commit();

        //投资成功之后更新用户信息
        UserInfo::dealWidthOrder($order);

        //投资完成之后活动统一处理逻辑
        try {
            PromoService::doAfterSuccessLoanOrder($order);
        } catch (\Exception $ex) {
        }

        //即将满标时候钉钉提醒
        try {
            if ($loanFullAndNotify) {
                $notify = new DingNotify('wdjf');
                $notify->charSentText('标的 [' . $loan->title . '] 募集进度为 ' . $update['finish_rate'] . ', 请及时处理');
            }
        } catch (\Exception $ex) {
            $msg = '标的订单处理-满标钉钉提醒：订单号-'.$order->id.';异常信息-'.$ex->getMessage();
            \Yii::trace($msg, 'loan_order');
        }

        //发送微信推送消息,写入queue_task
        if ($loan->isFlexRate) {
            $order->yield_rate = $rate;
        }
        Noty::send(new OrderMessage($order));

        return true;
    }

    /**
     * 创建用户标的订单.
     */
    public function createOrder($sn = null, $price = null, $uid = null, UserCoupon $coupon = null, $investFrom = 0)
    {
        if (empty($sn)) {
            return ['code' => PayService::ERROR_LAW, 'message' => '缺少参数'];   //参数为空,抛出错误信息
        }

        $model = Loan::findOne(['sn' => $sn]);
        if (null === $model) {
            return ['code' => PayService::ERROR_SYSTEM, 'message' => '找不到标的信息'];   //对象为空,抛出错误信息
        }

        $user = User::findOne($uid);
        $order = new OnlineOrder();
        $order->investFrom = $investFrom;
        $order->order_money = $price;
        $order->uid = $uid;
        $time = time();
        bcscale(14);

        $order->sn = OnlineOrder::createSN();
        $order->online_pid = $model->id;
        $order->order_time = $time;
        $order->refund_method = $model->refund_method;
        $order->yield_rate = $model->yield_rate;
        $order->expires = $model->expires;
        $order->username = $user->real_name;

        if ($coupon) {
            $order->userCoupon_id = $coupon->id;
            $order->couponAmount = $coupon->couponType->amount;
            $order->paymentAmount = bcsub($price, $order->couponAmount, 2);
        } else {
            $order->userCoupon_id = 0;
            $order->couponAmount = 0;
            $order->paymentAmount = $price;
        }

        if (Yii::$app->request->cookies->getValue('campaign_source')) {
            $order->campaign_source = Yii::$app->request->cookies->getValue('campaign_source');
        }
        if (!$order->validate()) {
            return ['code' => PayService::ERROR_MONEY_FORMAT,  'message' => current($order->firstErrors)];
        }
        $ore = $order->save(false);
        if (!$ore) {
            return [
                'code' => PayService::ERROR_ORDER_CREATE,
                'message' => PayService::getErrorByCode(PayService::ERROR_ORDER_CREATE),
            ];
        }
        $transaction = Yii::$app->db->beginTransaction();
        if ($coupon) {
            $coupon->order_id = $order->id;
            $coupon->isUsed = 1;
            if (!$coupon->save(false)) {
                $transaction->rollBack();
                return ['code' => PayService::ERROR_SYSTEM, 'message' => '代金券使用异常'];
            }
        }

        //免密逻辑处理
        $res = Yii::$container->get('ump')->orderNopass($order);
        $errmsg = '';

        if ($res->isSuccessful()) {
            if (Yii::$app->request->cookies->getValue('campaign_source')) {
                (new AffiliationManager())->log(Yii::$app->request->cookies->getValue('campaign_source'), $order);
            }
            try {
                if (null === OrderQueue::findOne(['orderSn' => $order->sn])) {
                    OrderQueue::initForQueue($order)->save();
                }
                $transaction->commit();

                return [
                    'code' => PayService::ERROR_SUCCESS,
                    'message' => '',
                    'tourl' => '/order/order/wait?osn='.$order->sn
                ];
            } catch (\Exception $ex) {
                $errmsg = $ex->getMessage();
            }
        } else {
            $errmsg = $res->get('ret_msg');
        }

        if ($coupon) {
            $coupon->order_id = null;
            $coupon->isUsed = 0;
            if (!$coupon->save(false)) {
                $transaction->rollBack();
                $errmsg = "代金券退回异常";
            } else {
                $transaction->commit();
            }
        }

        return [
            'code' => PayService::ERROR_MONEY_FORMAT,
            'message' => $errmsg,
            'tourl' => '/order/order/result?status=fail&osn='.$order->sn,
        ];
    }

    public static function getTotalInvestment(Loan $loan, User $user)
    {
        $total = OnlineOrder::find()
            ->where([
                'status' => OnlineOrder::STATUS_SUCCESS,
                'uid' => $user->id,
                'online_pid' => $loan->id,
            ])
            ->sum('order_money');
        return null === $total ? 0 : $total;
    }

    /**
     * 撤销标的订单(暂时只支持募集中标的的撤单)
     * 撤标联动转账传递的order_id 为撤标流水sn
     * @param \common\models\order\OnlineOrder $order
     */
    public static function cancelLoanOrder(OnlineOrder $order)
    {
        Yii::trace('正在进行标的订单撤销，订单ID：' . $order->id, 'loan_order');
        $loan = $order->loan;
        $user = $order->user;
        if ($order->status !== OnlineOrder::STATUS_SUCCESS) {
            throw new \Exception('只支持成功订单的撤销');
        }
        if (OnlineProduct::STATUS_NOW !== $loan->status) {
            throw new \Exception('暂只支持募集中的标的的撤销');
        }
        $cancelOrderNewSn = MoneyRecord::createSN();
        //联动转账
        $umpRes = Yii::$container->get('ump')->loanCancelOrder($order, $cancelOrderNewSn);
        if (!$umpRes->isSuccessful()) {
            Yii::trace('撤销订单联动转账失败，失败信息:' . $umpRes->get('ret_msg'), 'loan_order');
            throw new \Exception('联动标的转账失败');
        }
        Yii::trace('标的撤标，已经成功转账', 'loan_order');
        //todo 之后需要补充撤销记录及状态
        $transaction = Yii::$app->db->beginTransaction();
        try {
            //撤销订单的代金券
            $userCoupon = UserCoupon::findOne(['order_id' => $order->id, 'user_id' => $user->id, 'isUsed' => 1]);
            if (!empty($userCoupon)) {
                $res = UserCoupon::updateAll(['order_id' => null, 'isUsed' => 0], ['id' => $userCoupon->id]);
                if ($res === 0) {
                    throw new \Exception('更新用户代金券失败');
                }
            }

            //更新账户信息
            $account = $user->lendAccount;
            //调整计算精度,防止小数位丢失
            $account->available_balance = bcadd($account->available_balance, $order->paymentAmount, 2);
            $account->drawable_balance = bcadd($account->drawable_balance, $order->paymentAmount, 2);
            $account->freeze_balance =bcsub($account->freeze_balance, $order->paymentAmount, 2);
            $account->out_sum =bcsub($account->out_sum, $order->paymentAmount, 2);
            $res = $account->save(false);
            if (!$res) {
                throw new \Exception('更新用户账户信息失败,失败信息');
            }
            //更新资金流水
            $moneyRecord = new MoneyRecord();
            $moneyRecord->account_id = $account->id;
            $moneyRecord->sn = $cancelOrderNewSn;
            $moneyRecord->type = MoneyRecord::TYPE_LOAN_CANCEL;
            $moneyRecord->osn = $order->sn;
            $moneyRecord->uid = $order->uid;
            $moneyRecord->balance = $account->available_balance;
            $moneyRecord->in_money = $order->paymentAmount;
            $moneyRecord->remark = '撤标操作';
            $res = $moneyRecord->save();
            if (!$res) {
                throw new \Exception('增加撤单流水失败');
            }
            $count = OnlineOrder::find()->where(['online_pid' => $loan->id, 'status' => 1])->count();
            //更改标的募集比例和实际募集金额
            $query = Yii::$app->db->createCommand("UPDATE online_product SET funded_money = funded_money - :orderMoney,finish_rate = IF(funded_money / money > 0.01, funded_money / money, :minRate) WHERE id = :loanId")->bindValues(['orderMoney' => $order->order_money, 'loanId' => $loan->id, 'minRate' => $count > 1 ? '0.01' : '0']);
            $res = $query->execute();
            if ($res === 0) {
                throw new \Exception('更新标的募集进度及实际募集金额失败');
            }

            $order->status = OnlineOrder::STATUS_CANCEL;
            $res = $order->save(false);
            if (!$res) {
                throw new \Exception('更新订单状态失败');
            }

            //更新用户信息
            UserInfo::updateUserInfoOfUser($user);

            //todo 撤标之后短信通知

            $transaction->commit();
        } catch (\Exception $ex) {
            $transaction->rollBack();
            throw $ex;
        }
    }
}
