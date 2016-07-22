<?php

namespace common\models\order;

use common\models\payment\Repayment;
use yii\behaviors\TimestampBehavior;
use common\models\product\OnlineProduct;
use common\lib\product\ProductProcessor;
use common\models\sms\SmsMessage;
use common\lib\bchelp\BcRound;
use Yii;

class OnlineRepaymentPlan extends \yii\db\ActiveRecord
{
    const STATUS_WEIHUAN = 0;//0、未还
    const STATUS_YIHUAN = 1;// 1、已还
    const STATUS_TIQIAM = 2;// 2、提前还款
    const STATUS_WUXIAO = 3;// 3，无效;

    public static function createSN($pre = 'hkjh')
    {
        $pre_val = 'HP';
        list($usec, $sec) = explode(' ', microtime());
        $v = ((float) $usec + (float) $sec);

        list($usec, $sec) = explode('.', $v);
        $date = date('ymdHisx'.rand(1000, 9999), $usec);

        return $pre_val.str_replace('x', $sec, $date);
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'online_repayment_plan';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['online_pid', 'sn', 'order_id', 'qishu', 'benxi', 'benjin', 'lixi', 'yuqi_day', 'benxi_yue'], 'required'],
            [['online_pid', 'order_id', 'qishu', 'uid', 'refund_time', 'status'], 'integer'],
            [['benxi', 'benjin', 'lixi', 'overdue', 'benxi_yue'], 'number'],
            [['sn'], 'string', 'max' => 30],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'online_pid' => 'Online Pid',
            'sn' => 'Sn',
            'order_id' => 'Order ID',
            'qishu' => 'Qishu',
            'uid' => 'Uid',
            'benxi' => 'Benxi',
            'benjin' => 'Benjin',
            'lixi' => 'Lixi',
            'overdue' => 'Overdue',
            'yuqi_day' => 'Yuqi Day',
            'benxi_yue' => 'Benxi Yue',
            'refund_time' => 'Refund Time',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    public static function initPlan($ord, $initplan)
    {
        $plan = new self($initplan);
        $plan->sn = self::createSN();
        $plan->online_pid = $ord['online_pid'];
        $plan->order_id = $ord['id'];
        $plan->uid = $ord['uid'];
        $plan->status = self::STATUS_WEIHUAN;
        $plan->yuqi_day = '0';
        $plan->overdue = 0;
        $plan->benxi_yue = 0;//付息还本时候用到的字段
        return $plan;
    }

    /**
     * 获取总的利息.
     */
    public static function getTotalLixi(OnlineProduct $loan, OnlineOrder $ord)
    {
        bcscale(14);
        $bc = new BcRound();
        if (OnlineProduct::REFUND_METHOD_DAOQIBENXI === (int) $loan->refund_method) {
            //到期本息
            return $bc->bcround(bcmul($ord->order_money, bcmul($loan->expires, bcdiv($ord->yield_rate, 365))), 2);  //以订单里面的利率为准
        } else {
            return $bc->bcround(bcdiv(bcmul(bcmul($ord->order_money, $ord->yield_rate), $loan->expires), 12), 2);
        }
    }

    public static function generatePlan(OnlineProduct $loan)
    {
        $pp = new ProductProcessor();
        bcscale(14);
        $bc = new BcRound();
        //获取所有订单
        $orders = OnlineOrder::find()->where(['online_pid' => $loan->id, 'status' => OnlineOrder::STATUS_SUCCESS])->all();

        $transaction = Yii::$app->db->beginTransaction();
        $up['is_jixi'] = 1;
        if (0 === $loan->finish_date) {
            $finish_date = null;
            if (OnlineProduct::REFUND_METHOD_DAOQIBENXI === (int) $loan->refund_method) {
                 $finish_date = $pp->LoanTerms('d1', date('Y-m-d', $loan->jixi_time), $loan->expires);
            } else {
                 $finish_date = date("Y-m-d", $pp->calcRetDate($loan->expires, $loan->jixi_time));//如果由于29,30,31造成的跨月的要回归到上一个月最后一天
            }
            if (null !== $finish_date) {
                $up['finish_date'] = strtotime($finish_date);
                $loan->finish_date = $up['finish_date'];
            }
        } else {
            //有截止日期时候，项目期限=截止日期 - 起息日期 + 1
            if (OnlineProduct::REFUND_METHOD_DAOQIBENXI === (int) $loan->refund_method) {
                $expires = (new \DateTime(date('Y-m-d', $loan->finish_date)))->diff((new \DateTime(date('Y-m-d',$loan->jixi_time))))->days;
                $loan->expires = $expires;
                $up['expires'] = $expires;
            }
        }

        OnlineProduct::updateAll($up, ['id' => $loan->id]);//修改已经计息
        $username = '';
        $sms = new SmsMessage([
            'template_id' => Yii::$app->params['sms']['manbiao'],
            'level' => SmsMessage::LEVEL_LOW,
        ]);
        $repayment = [];
        foreach ($orders as $ord) {
            //获取每个订单的还款金额详情
            $res_money = self::calcBenxi($ord);
            if ($res_money) {
                foreach ($res_money as $k => $v) {
                    $term = $k + 1;
                    $amount = $bc->bcround(bcadd($v[1], $v[2]), 2);
                    $principal = $bc->bcround($v[1], 2);
                    $interest = $bc->bcround($v[2], 2);
                    //生成还款计划
                    $initplan = [
                        'qishu' => $term,
                        'benxi' => $amount,
                        'benjin' => $principal,
                        'lixi' => $interest,
                        'refund_time' => strtotime($v[0]),
                    ];
                    $plan = self::initPlan($ord, $initplan);
                    if (!$plan->save()) {
                        $transaction->rollBack();
                        return false;
                    }
                    //统计还款数据
                    $totalAmount = isset($repayment[$term]['amount']) ? bcadd($repayment[$term]['amount'], $amount) : $amount;
                    $totalPrincipal = isset($repayment[$term]['principal']) ? bcadd($repayment[$term]['principal'], $principal) : $principal;
                    $totalInterest = isset($repayment[$term]['interest']) ? bcadd($repayment[$term]['interest'], $interest) : $interest;
                    $repayment[$term] = ['amount' => $totalAmount, 'principal' => $totalPrincipal, 'interest' => $totalInterest, 'dueDate' => $v[0]];
                }
            }

            if ($username != $ord->username) {
                $message = [
                    $ord->username,
                    $loan->title,
                    date('Y-m-d', $loan->jixi_time),
                    Yii::$app->params['contact_tel'],
                ];
                $_sms = clone $sms;
                $_sms->uid = $ord->uid;
                $_sms->mobile = $ord->mobile;
                $_sms->message = json_encode($message);
                $_sms->save();
            }
            $username = $ord->username;
        }
        foreach ($repayment as $key => $val) {
            $rep = new Repayment([
                'loan_id' => $loan->id,
                'term' => $key,
                'dueDate' => $val['dueDate'],
                'amount' => $val['amount'],
                'principal' => $val['principal'],
                'interest' => $val['interest'],
            ]);
            $rep->save();
        }
        $transaction->commit();
        return true;
    }

    /**
     * 计算每期应还本息.
     * @param \common\models\order\OnlineOrder $ord 订单对象
     *
     * 要点:
     * 1. 到期本息和自然日期计息的方式,都是按照天数计算的;
     * 2. 其他计息方式,是按照月份来计算的;
     */
    public static function calcBenxi(OnlineOrder $ord)
    {
        if (!$ord || !$ord->loan) {
            throw new \Exception();
        }

        $paymentDates = $ord->loan->paymentDates;
        if (empty($paymentDates)) {
            throw new \Exception();
        }

        $qishu = count($paymentDates);
        $bc = new BcRound();
        bcscale(14);

        if (OnlineProduct::REFUND_METHOD_DAOQIBENXI === (int) $ord->loan->refund_method) {   //到期本息计算利息
            if (1 !== $qishu) {
                throw new \Exception();
            }

            $lixi = $bc->bcround(bcmul($ord->order_money, bcmul($ord->loan->expires, bcdiv($ord->yield_rate, 365))), 2);

            return [
                [
                    $paymentDates[0],    //还款日期
                    $ord->order_money,   //还款本金
                    $lixi,    //还款利息
                ]
            ];
        }

        $res = [];
        $totalLixi = $bc->bcround(bcdiv(bcmul(bcmul($ord->order_money, $ord->yield_rate), $ord->loan->expires), 12), 2);    //计算总利息
        $isNature = $ord->loan->isNatureRefundMethod();

        if (!bccomp($totalLixi, '0', 2)) {
            $totalLixi = '0.01';
        }

        if ($isNature) {
            $startDay = date('Y-m-d', $ord->loan->jixi_time);
            $totalDays = (new \DateTime($startDay))->diff(new \DateTime(end($paymentDates)))->days;
        }

        foreach ($paymentDates as $key => $val) {
            $benjin = 0;
            if ($key === ($qishu - 1)) {
                $benjin = $ord->order_money;
                $lixi = $bc->bcround(bcsub($totalLixi, array_sum(array_column($res, 2))), 2);   //最后一期分期利息计算,用总的减去前面计算出来的,确保总额没有差错
            } else {
                if ($isNature) {
                    $startDay = !$key ? $startDay : $paymentDates[$key - 1];
                    if ($val <= $startDay) {
                        throw new \Exception();
                    }

                    $refundDays = (new \DateTime($startDay))->diff(new \DateTime($val))->days;    //应还款天数
                    $lixi = bcmul($totalLixi, bcdiv($refundDays, $totalDays), 2);
                } else {
                    $lixi = bcdiv($totalLixi, $qishu, 2);    //普通计息和自然计息都按照14位精度严格计算,即从小数位后第三位舍去
                }
            }

            $res[$key] = [
                $val,    //还款日期
                $benjin,   //还款本金
                $lixi,    //还款利息
            ];
        }

        return $res;
    }
}