<?php

namespace common\models\product;

use Wcg\DateTime\DT;

class RepaymentHelper
{
    /**
     * 计算标的还款日
     *
     * @param string $startDate 计息日期
     * @param string $endDate 截止日期
     * @param int $refundMethod 还款方式
     * @param int $duration 项目期限
     * @param int $paymentDay 固定还款日
     * @param bool $isCustomRepayment 是否是自定义还款
     *
     * @return array
     */
    public static function calcRepaymentDate($startDate, $endDate, $refundMethod, $duration, $paymentDay, $isCustomRepayment)
    {
        //$term 表示期数 $totalMonthEachTerm 表示 每期多少月
        if ($refundMethod === 2 || $refundMethod === 6 || $refundMethod === 10) {
            $term = $duration;
            $totalMonthEachTerm = 1;
        } elseif ($refundMethod === 3 || $refundMethod === 7) {
            $term = ceil($duration / 3);
            $totalMonthEachTerm = 3;
        } elseif ($refundMethod === 4 || $refundMethod === 8) {
            $term = ceil($duration / 6);
            $totalMonthEachTerm = 6;
        } elseif ($refundMethod === 5 || $refundMethod === 9) {
            $term = ceil($duration / 12);
            $totalMonthEachTerm = 12;
        }
        //还款日期
        $paymentDays = [];
        if ($refundMethod === 1) {
            $paymentDays[] = $endDate;
        } elseif (in_array($refundMethod, [2, 3, 4, 5, 10])) {
            //最后一次还款日期为计算出的 项目截止日期
            for ($i = 1; $i < $term; $i++) {
                //获取当期还款日期
                $paymentDays[] = (new DT($startDate))->addMonth($i * $totalMonthEachTerm)->format('Y-m-d');
            }
            $paymentDays[] = $endDate;//最后一个还款日为截止日
        } elseif (in_array($refundMethod, [6, 7, 8, 9])) {
            for ($i = 1; $i <= $term; $i++) {
                //获取当期还款时间
                $time = (new DT($startDate))->addMonth(($i - 1) * $totalMonthEachTerm)->getTimestamp();
                $paymentDay = min(intval($paymentDay), intval(date('t', $time)));//取还款日和当月最后一天的最小值
                $paymentDay = str_pad($paymentDay, 2, '0', STR_PAD_LEFT);
                $m = intval(date('m', $time));
                if ($refundMethod === 7) {
                    if ($m <= 3) {
                        $m = '03';
                    } elseif ($m <= 6) {
                        $m = '06';
                    } elseif ($m <= 9) {
                        $m = '09';
                    } else {
                        $m = '12';
                    }
                } elseif ($refundMethod === 8) {
                    if ($m <= 6) {
                        $m = '06';
                    } else {
                        $m = '12';
                    }
                } else if ($refundMethod === 9) {
                    $m = '12';
                } else {
                    $m = str_pad($m, 2, '0', STR_PAD_LEFT);
                }
                $paymentDate = date('Y', $time) . '-' . $m . '-' . $paymentDay;
                //如果还款时间大于最后一个还款日退出
                if ($paymentDate > $endDate) {
                    break;
                }

                if ($paymentDate > $startDate) {
                    $paymentDays[] = $paymentDate;
                } else {
                    //如果还款时间小于起息日期，期数+1
                    $term++;
                }
            }
            if (!in_array($endDate, $paymentDays)) {
                $paymentDays[] = $endDate;//最后一个还款日为截止日
            }
        }

        //按自然年计息、自定义还款标的,当标的的实际还款日 大于 项目期限/12 时候合并最后两期
        $term = count($paymentDays);
        if ($refundMethod === 9
            && $isCustomRepayment
            && $term >= 2
            && $term > ceil($duration / 12)
        ) {
            array_pop($paymentDays);
            array_pop($paymentDays);
            $paymentDays[] = $endDate;
        }
        return $paymentDays;
    }
}