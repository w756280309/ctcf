<?php

namespace common\models\tx;

use common\models\product\RepaymentHelper;
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
        $startDate = $this->getStartDate();
        if ($startDate > $paymentDates[0]) {
            throw new \Exception('还款日不能小于计息日期');
        }
        $repaymentMethod = $this->getRefundMethod();
        $duration = $this->getDuration();


        return RepaymentHelper::calcRepayment($paymentDates, $repaymentMethod, $startDate, $duration, $amount, $apr);
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
