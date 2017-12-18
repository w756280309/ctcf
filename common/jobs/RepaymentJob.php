<?php
/**
 * Created by PhpStorm.
 * User: ZouJianShuang
 * Date: 17-12-15
 * Time: 上午10:14
 */
namespace common\jobs;

use common\models\offline\OfflineLoan;
use common\models\offline\OfflineRepayment;
use common\models\offline\OfflineRepaymentPlan;
use common\models\order\OnlineRepaymentPlan;
use common\service\SmsService;
use yii\base\Object;
use yii\queue\Job;
use Yii;

/**
 * Class RepaymentJob
 * @package common\jobs
 * 用于标的确认计息生成还款计划
 * 注：暂时用于线下标的
 */
class RepaymentJob extends Object implements Job  //需要继承Object类和Job接口
{
    public $id;     //标的id
    public $operator;   //操作人
    public $action;

    public function execute($queue)
    {
        $loan = OfflineLoan::findOne($this->id);
        if (!is_null($loan)) {
            $transaction = Yii::$app->db->beginTransaction();
            if ($this->action == 'add') {   //生成还款计划
                try {
                    self::saveRepayment($loan);    //还款计划
                    //self::sendSms($loan);   //发短信
                    $transaction->commit();
                } catch (\Exception $e) {
                    $transaction->rollBack();
                }
            } else if ($this->action == 'del') {    //删除还款计划
                try {
                    self::delRepayment($loan);
                    $transaction->commit();
                } catch (\Exception $e) {
                    $transaction->rollBack();
                }
            }
        }

        //发送短信
        if ($this->action == 'sendsms') {
            $plans = OfflineRepaymentPlan::find()->where(['status' => 1])->andWhere(['in', 'id', $this->id])->all();
            //var_dump($plans);die;
            self::sendSms($plans);
        }
    }

    //生成还款计划
    public function saveRepayment(OfflineLoan $loan)
    {
        $orders = $loan->getSuccessOrder(); //交易成功的订单
        if (count($orders) > 0) {
            $repaymentData = [];
            foreach ($orders as $order) {
                $amountData = OfflineRepaymentPlan::calcBenxi($order);
                if (empty($amountData)) {
                    throw new \Exception('还款数据不能为空');
                }
                foreach ($amountData as $key => $value) {
                    $term = $key + 1;
                    //判断还款计划是否存在
                    $plan = OfflineRepaymentPlan::find()
                        ->where(['order_id' => $order->id, 'uid' => $order->user_id, 'qishu' => $term])
                        ->one();
                    if (!is_null($plan)) {
                        continue;
                    }
                    //还款计划 offline_repayment_plan
                    $amount = bcadd($value['principal'], $value['interest'], 2);
                    $plan = new OfflineRepaymentPlan([
                        'loan_id' => $order->loan_id,
                        'sn' => OnlineRepaymentPlan::createSN(),
                        'order_id' => $order->id,
                        'qishu' => $term,
                        'uid' => $order->user_id,
                        'benxi' => $amount,
                        'benjin' => $value['principal'],
                        'lixi' => $value['interest'],
                        'refund_time' => $value['date'],
                        'operator' => $this->operator,
                    ]);
                    //最后一期计算贴息
                    if ($term == count($amountData) && strtotime($loan->jixi_time) > strtotime($order->valueDate)) {
                        $plan->yuqi_day = bcdiv(bcsub(strtotime($loan->jixi_time), strtotime($order->valueDate)), bcmul(24, 3600));
                        //todo
                        $plan->tiexi = bcdiv(bcmul($plan->yuqi_day, bcmul($order->money * 10000, $order->apr)), 365 , 2);
                        $amount = bcadd($amount, $plan->tiexi, 2);
                        $value['principal'] = bcadd($value['interest'], $plan->tiexi);
                    }
                    if (!$plan->save()) {
                        throw new \Exception('还款计划（repayment_plan）保存失败');
                    }

                    $repaymentData[$term] = [
                        'amount' => isset($repaymentData[$term]['amount']) ? bcadd($repaymentData[$term]['amount'], $amount, 2) : $amount,
                        'principal' => isset($repaymentData[$term]['principal']) ? bcadd($repaymentData[$term]['principal'], $value['principal'], 2) : $value['principal'],
                        'interest' => isset($repaymentData[$term]['interest']) ? bcadd($repaymentData[$term]['interest'], $value['interest'], 2) : $value['interest'],
                        'dueDate' => $value['date'],
                    ];
                }
            }
            if (empty($repaymentData)) {
                throw new \Exception('标的还款数据不能为空');
            }
            foreach ($repaymentData as $term => $data) {
                $rep = new OfflineRepayment([
                    'loan_id' => $loan->id,
                    'term' => $term,
                    'dueDate' => $data['dueDate'],
                    'amount' => $data['amount'],
                    'principal' => $data['principal'],
                    'interest' => $data['interest'],
                ]);
                if (!$rep->save()) {
                    throw new \Exception('线下标的还款数据保存失败');
                }
            }
        } else {
            throw new \Exception('标的['.$loan->title.']没有任何成功的订单');
        }
    }
    //给投标用户发短信
    public function sendSms($plans)
    {
        if (!empty($plans)) {
            foreach ($plans as $plan) {
                try {
                    $user = $plan->user;
                    $loan = $plan->loan;
                    $order = $plan->order;
                    if ($order->lastTerm == $plan->qishu) {
                        $message = [
                            $user->realName,
                            $loan->title,
                            bcadd($plan->benxi, $plan->tiexi, 2),
                            substr($order->bankCardNo, -4),
                            $order->accBankName,
                        ];
                        //最后一期
                        $templateId = Yii::$app->params['offline_repayment_sms']['fuxi_last'];
                        SmsService::send($user->mobile, $templateId, $message);
                    } else {
                        //分期
                        $message = [
                            $user->realName,    //用户名
                            $loan->title,       //产品名
                            '第' . $plan->qishu . '期',   //第多少期
                            bcadd($plan->benxi, $plan->tiexi, 2),   //金额
                            substr($order->bankCardNo, -4),       //银行卡尾号
                            $order->accBankName,     //银行
                        ];
                        $templateId = Yii::$app->params['offline_repayment_sms']['fuxi_ordinary'];
                        SmsService::send($user->mobile, $templateId, $message);
                    }
                } catch (\Exception $e) {
                    var_dump($e->getMessage());
                    throw new \Exception($e->getMessage());
                }
            }
        }
    }

    //删除标的的还款计划（offline_repayment & offline_rapayment_plan）
    public function delRepayment(OfflineLoan $loan)
    {
        if ($loan->is_jixi == false && $loan->finish_date == null) {
            $repayments = $loan->getRepayments();
            $plans = OfflineRepaymentPlan::find()->where(['loan_id' => $loan->id])->all();
            if (count($repayments) > 0 && count($plans) > 0) {
                //删除repayment
                $res = OfflineRepayment::deleteAll(['loan_id' => $loan->id]);
                if (!$res) {
                    throw new \Exception('删除线下标的还款计划失败，['.$loan->title.']');
                }
                //删除repayment_plan
                $res = OfflineRepaymentPlan::deleteAll(['loan_id' => $loan->id]);
                if (!$res) {
                    throw new \Exception('删除线下标的还款计划失败，['.$loan->title.']');
                }
            }
        } else {
            throw new \Exception('删除线下标的还款计划失败，['.$loan->title.']');
        }
    }
}