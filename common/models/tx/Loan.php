<?php

namespace common\models\tx;

use common\models\product\RepaymentHelper;
use Wcg\DateTime\DT;
use Wcg\Interest\Builder;
use Wcg\Math\Bc;
use Yii;
use Zii\Model\ActiveRecord;

/**
 * Class Loan.
 *
 * @property string     $startDate      转过格式的计息时间
 * @property string     $endDate        转过格式的标的截止日期
 * @property int        $graceDays      标的宽限期
 * @property array      $repaymentDates 还款日数组
 * @property int        $refundMethod   还款方式
 * @property int        $duration       项目期限
 */
class Loan extends ActiveRecord
{
    public static function getDb()
    {
        return Yii::$app->db;
    }

    public static function tableName()
    {
        return 'online_product';
    }

    public function attributes()
    {
        return [
            'id',
            'status',
            'money',
            'start_money',
            'dizeng_money',
            'is_jixi',
            'jixi_time',
            'finish_date',
            'kuanxianqi',
            'refund_method',
            'expires',
            'paymentDay',
            'order_limit',
            'isTest',
            'sn',
            'issuerSn',
            'title',
            'funded_money',
            'yield_rate',
            'start_date',
            'end_date',
            'allowTransfer',
            'isCustomRepayment',
        ];
    }

    //获取格式化的计息日期
    public function getStartDate()
    {
        return date('Y-m-d', $this->jixi_time);
    }

    //获取格式化的标的截止日期
    public function getEndDate()
    {
        return date('Y-m-d', $this->finish_date);
    }

    //宽限期
    public function getGraceDays()
    {
        return intval($this->kuanxianqi);
    }

    //判断是否是按自然月（季、半年、年）计息
    public function isNatureRefundMethod()
    {
        return in_array($this->getRefundMethod(), [6, 7, 8, 9]);
    }

    /**
     * 获取指定标的的所有还款日.
     * 注意点： 默认标的的起息日期和截止日期是正确的；如果还款日超过当月最后一天，实际还款日取最后一天.
     *
     * @return array 返回所有还款日自然排序后组成的数组,返回 date('Y-m-d',$time) 组成的数组
     *
     * @throws Exception
     */
    public function getRepaymentDates()
    {
        return RepaymentHelper::calcRepaymentDate($this->getStartDate(),
            $this->getEndDate(),
            $this->getRefundMethod(),
            $this->getDuration(),
            $this->paymentDay,
            $this->isCustomRepayment
        );
    }

    /**
     * 计算还款计划
     * 返回金额以元为单位计算
     *
     * @param int   $amount 订单金额,以分为单位传输
     * @param float $apr    订单利率
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getRepaymentPlan($amount, $apr)
    {
        $amount = bcdiv($amount, 100, 2);//以元为单位的订单金额
        $paymentDates = $this->getRepaymentDates();
        $count = count($paymentDates);
        if ($count < 0) {
            throw new \Exception('还款日期不能为空');
        }
        $startDay = $this->startDate;
        if ($startDay > $paymentDates[0]) {
            throw new \Exception('还款日不能小于计息日期');
        }
        bcscale(14);
        if (10 === $this->getRefundMethod()) {//等额本息
            $repayPlan = Builder::create(Builder::TYPE_DEBX)
                ->setStartDate(new DT($this->getStartDate()))
                ->setMonth($this->getDuration())
                ->setRate($apr)
                ->build($amount);
            $res = [];
            foreach ($repayPlan as $index => $repayTerm) {
                $res[$index] = [
                    'date' => $repayTerm->getEndDate()->add(new \DateInterval('P1D'))->format('Y-m-d'),
                    'principal' => Bc::round($repayTerm->getPrincipal(), 2),
                    'interest' =>Bc::round($repayTerm->getInterest(), 2),
                ];
            }
            return $res;
        }

        if (!$this->isAmortized()) {   //到期本息计算利息
            $interest = bcdiv(bcmul($amount, bcmul($this->duration, $apr, 14), 14), 365, 2);

            return [
                [
                    'date' => $paymentDates[0],    //还款日期
                    'principal' => $amount,   //还款本金,以元为单位
                    'interest' => $interest,    //还款利息,以元为单位
                ],
            ];
        }

        $res = [];
        $totalInterest = bcdiv(bcmul(bcmul($amount, $apr, 14), $this->duration, 14), 12, 2);    //计算总利息
        if (bccomp($totalInterest, '0.00', 2) <= 0) {
            $totalInterest = '0.01';
        }
        $totalDays = (new \DateTime($startDay))->diff(new \DateTime(end($paymentDates)))->days;
        foreach ($paymentDates as $key => $val) {
            $principal = 0;
            if ($key === ($count - 1)) {
                $principal = $amount;
                $interest = bcsub($totalInterest, array_sum(array_column($res, 'interest')), 2);   //最后一期分期利息计算,用总的减去前面计算出来的,确保总额没有差错
            } else {
                if ($this->isNatureRefundMethod()) {
                    $startDay = ($key === 0) ? $startDay : $paymentDates[$key - 1];
                    if ($val < $startDay) {
                        throw new \Exception('标的计息日不能小于还款日');
                    }

                    $refundDays = (new \DateTime($startDay))->diff(new \DateTime($val))->days;    //应还款天数
                    $interest = bcdiv(bcmul($totalInterest, $refundDays, 14), $totalDays, 2);
                } else {
                    $interest = bcdiv($totalInterest, $count, 2);    //普通计息和自然计息都按照14位精度严格计算,即从小数位后第三位舍去
                }
            }

            $res[$key] = [
                'date' => $val,    //还款日期
                'principal' => $principal,   //还款本金
                'interest' => $interest,    //还款利息
            ];
        }
        //按自然年计息、自定义还款标的,当标的的实际还款日 大于 项目期限/12 时候合并最后两期
        $term = count($res);
        if ($this->refund_method === 9
            && $this->isCustomRepayment
            && $term >= 2
            && $term > ceil($this->expires/12)
        ) {
            $lastTermData = $res[$term-1];//最后一期数据
            $exceptMergeData = $res[$term-2];//倒数第二期，需要合并到最后一期
            array_pop($res);
            $res[$term - 2] = [
                'date' => $lastTermData[0],
                'principal' => bcadd($lastTermData[1], $exceptMergeData[1], 2),
                'interest' => bcadd($lastTermData[2], $exceptMergeData[2], 2)
            ];
        }

        return $res;
    }

    //获取还款方式
    public function getRefundMethod()
    {
        return intval($this->refund_method);
    }

    //获取期限
    public function getDuration()
    {
        return intval($this->expires);
    }

    //判断是否分期
    public function isAmortized()
    {
        return 1 !== $this->refundMEthod;
    }

    //获取该标的对应的发起债权时的起投金额,单位分
    public function getMinOrderAmount()
    {
        return bcmul(max($this->start_money, bcdiv($this->money, $this->order_limit, 2)), 100, 0);
    }

    //获取标的对应的递增金额,单位分
    public function getIncrOrderAmount()
    {
        return bcmul($this->dizeng_money, 100, 0);
    }
}
