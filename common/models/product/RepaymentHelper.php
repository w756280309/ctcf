<?php

namespace common\models\product;

use common\lib\bchelp\BcRound;
use Wcg\DateTime\DT;
use Wcg\Interest\Builder;
use Wcg\Math\Bc;

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
     * @param bool $isDailyAccrual 是否是分期截止日（按天），默认不是
     *
     * @return array
     */
    public static function calcRepaymentDate($startDate, $endDate, $repaymentMethod, $duration, $paymentDay, $isCustomRepayment, $isDailyAccrual = false)
    {
        if ($isDailyAccrual && $repaymentMethod > 1) {
            $startDateTime = new \DateTime($startDate);
            $endDateTime = new \DateTime($endDate);
            $dateDiff = $endDateTime->diff($startDateTime);
            $duration = $dateDiff->m;
            $diffYear = $dateDiff->y;
            if ($diffYear > 0) {
                $duration = $duration + 12 * $diffYear;
            }
            if ($dateDiff->d > 0) {
                $duration += 1;
            }
        }

        //$term 表示期数 $totalMonthEachTerm 表示 每期多少月
        if ($repaymentMethod === 2 || $repaymentMethod === 6 || $repaymentMethod === 10) {
            $term = $duration;
            $totalMonthEachTerm = 1;
        } elseif ($repaymentMethod === 3 || $repaymentMethod === 7) {
            $term = ceil($duration / 3);
            $totalMonthEachTerm = 3;
        } elseif ($repaymentMethod === 4 || $repaymentMethod === 8) {
            $term = ceil($duration / 6);
            $totalMonthEachTerm = 6;
        } elseif ($repaymentMethod === 5 || $repaymentMethod === 9) {
            $term = ceil($duration / 12);
            $totalMonthEachTerm = 12;
        }
        //还款日期
        $paymentDays = [];
        if ($repaymentMethod === 1) {
            $paymentDays[] = $endDate;
        } elseif (in_array($repaymentMethod, [2, 3, 4, 5, 10])) {
            //最后一次还款日期为计算出的 项目截止日期
            for ($i = 1; $i < $term; $i++) {
                //获取当期还款日期
                $paymentDays[] = (new DT($startDate))->addMonth($i * $totalMonthEachTerm)->format('Y-m-d');
            }
            $paymentDays[] = $endDate;//最后一个还款日为截止日
        } elseif (in_array($repaymentMethod, [6, 7, 8, 9])) {
            for ($i = 1; $i <= $term; $i++) {
                //获取当期还款时间
                $time = (new DT($startDate))->addMonth(($i - 1) * $totalMonthEachTerm)->getTimestamp();
                $m = intval(date('m', $time));
                if ($repaymentMethod === 7) {
                    if ($m <= 3) {
                        $m = '03';
                    } elseif ($m <= 6) {
                        $m = '06';
                    } elseif ($m <= 9) {
                        $m = '09';
                    } else {
                        $m = '12';
                    }
                } elseif ($repaymentMethod === 8) {
                    if ($m <= 6) {
                        $m = '06';
                    } else {
                        $m = '12';
                    }
                } else if ($repaymentMethod === 9) {
                    $m = '12';
                } else {
                    $m = str_pad($m, 2, '0', STR_PAD_LEFT);
                }

                //获得实际还款月的最后一天，去取还款月的最后一天和已设还款日的最小值
                $curMonthFirstDay = date('Y', $time) . '-' . $m . '-01';
                $curMonthEndDay = date('t', strtotime($curMonthFirstDay));
                $paymentTmpDay = min(intval($paymentDay), intval($curMonthEndDay));
                $paymentPadDay = str_pad($paymentTmpDay, 2, '0', STR_PAD_LEFT);
                $paymentDate = date('Y', $time) . '-' . $m . '-' . $paymentPadDay;

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
        if ($repaymentMethod === 9
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

    /**
     * 计算还款本金和利息
     *
     * @param   array   $repaymentDates     还款日期数组
     * @param   int     $repaymentMethod    还款方式
     * @param   string  $startDate          起息日
     * @param   int     $duration           项目期限
     * @param   float   $amount             金额
     * @param   float   $apr                利率
     * @param   bool    $isDailyAccrual     是否是分期截止日（按天），默认不是
     *
     * @return array
     * @throws \Exception
     */
    public static function calcRepayment($repaymentDates, $repaymentMethod, $startDate, $duration, $amount, $apr, $isDailyAccrual = false)
    {
        $amount = Bc::round($amount, 2);
        $apr = Bc::round($apr, 4);
        $count = count($repaymentDates);
        if ($count < 0) {
            throw new \Exception('还款日期不能为空');
        }

        if ($startDate > $repaymentDates[0]) {
            throw new \Exception('还款日不能小于计息日期');
        }

        bcscale(14);
        if (10 === $repaymentMethod) {//等额本息
            $repayPlan = Builder::create(Builder::TYPE_DEBX)
                ->setStartDate(new DT($startDate))
                ->setMonth($duration)
                ->setRate($apr)
                ->build($amount);
            $res = [];
            foreach ($repayPlan as $index => $repayTerm) {
                $res[$index] = [
                    'date' => $repayTerm->getEndDate()->add(new \DateInterval('P1D'))->format('Y-m-d'),
                    'principal' => Bc::round($repayTerm->getPrincipal(), 2),
                    'interest' => Bc::round($repayTerm->getInterest(), 2),
                ];
            }
            return $res;
        }

        if (1 === $repaymentMethod) {   //到期本息计算利息
            $interest = Bc::round(bcdiv(bcmul($amount, bcmul($duration, $apr, 14), 14), 365, 14), 2);

            return [
                [
                    'date' => $repaymentDates[0],    //还款日期
                    'principal' => $amount,   //还款本金,以元为单位
                    'interest' => $interest,    //还款利息,以元为单位
                ],
            ];
        }

        $res = [];
        //判断是分期类型标的且设置了截止日
        if ($repaymentMethod >= 2 && $isDailyAccrual) {
            foreach ($repaymentDates as $k => $valueDate) {
                $principal = '0.00';
                if ($k === ($count - 1)) {
                    $principal = $amount;
                }
                $startDate = ($k === 0) ? $startDate : $repaymentDates[$k - 1];
                $refundDays = (new \DateTime($startDate))->diff(new \DateTime($valueDate))->days; //当期付息天数
                $interest = Bc::round(bcdiv(bcmul($amount, bcmul($refundDays, $apr, 14), 14), 365, 14), 2);
                $res[$k] = [
                    'date' => $valueDate,    //还款日期
                    'principal' => $principal,   //还款本金
                    'interest' => $interest,    //还款利息
                ];
            }

            return $res;
        }

        //分期类型 - 按月付息，不设到期截止日
        $totalInterest = Bc::round(bcdiv(bcmul(bcmul($amount, $apr, 14), $duration, 14), 12, 14), 2);    //计算总利息
        if (bccomp($totalInterest, '0.00', 2) <= 0) {
            $totalInterest = '0.01';
        }
        $totalDays = (new \DateTime($startDate))->diff(new \DateTime(end($repaymentDates)))->days;
        foreach ($repaymentDates as $key => $val) {
            $principal = '0.00';
            if ($key === ($count - 1)) {
                $principal = $amount;
                $interest = Bc::round(bcsub($totalInterest, array_sum(array_column($res, 'interest')), 2), 2);   //最后一期分期利息计算,用总的减去前面计算出来的,确保总额没有差错
            } else {
                if (in_array($repaymentMethod, [6, 7, 8, 9])) {
                    $startDate = ($key === 0) ? $startDate : $repaymentDates[$key - 1];
                    if ($val < $startDate) {
                        throw new \Exception('标的计息日不能小于还款日');
                    }

                    $refundDays = (new \DateTime($startDate))->diff(new \DateTime($val))->days;    //应还款天数
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

        return $res;
    }

    /**
     * 生成还款计划数据
     *
     * @param   string $startDate           起息日
     * @param   string $endDate             截止日
     * @param   int    $repaymentMethod     还款方式
     * @param   int    $duration            项目期限
     * @param   int    $paymentDay          固定还款日
     * @param   bool   $isCustomRepayment   是否是自定义还款
     * @param   float  $amount              投资金额
     * @param   float  $apr                 利率
     * @param   bool   $isDailyAccrual      是否分期设置了截止日
     *
     * @return array
     */
    public static function calcRepaymentPlan($startDate, $endDate, $repaymentMethod, $duration, $paymentDay, $isCustomRepayment, $amount, $apr, $isDailyAccrual = false)
    {
        $repaymentDates = self::calcRepaymentDate($startDate, $endDate, $repaymentMethod, $duration, $paymentDay, $isCustomRepayment, $isDailyAccrual);
        return self::calcRepayment($repaymentDates, $repaymentMethod, $startDate, $duration, $amount, $apr, $isDailyAccrual);
    }

    /**
     * 计算加息券产生的收益
     *
     * @param string $amount           金额（元）
     * @param string $apr              利率 例：0.05（即5%）
     * @param string $duration         期限（默认：天）
     * @param string $baseDurationUnit 期限单位/按年（默认：365天）
     *
     * @return string 收益（元）
     */
    public static function calcBonusProfit($amount, $apr, $duration, $baseDurationUnit = '365')
    {
        //标的投资本金 x 加息券加息利率 x 加息天数 / 365
        return bcdiv(bcmul(bcmul($amount, $apr, 14), $duration, 14), $baseDurationUnit, 2) ;
    }
    public static function calcRepayment2($repaymentDates, $repaymentMethod, $startDate, $duration, $amount, $apr)
    {
        $amount = Bc::round($amount, 2);
        $apr = Bc::round($apr, 4);
        $count = count($repaymentDates);
        if ($count < 0) {
            throw new \Exception('还款日期不能为空');
        }
        if ($startDate > $repaymentDates[0]) {
            throw new \Exception('还款日不能小于计息日期');
        }
        bcscale(14);
        if (10 === $repaymentMethod) {//等额本息
            $repayPlan = Builder::create(Builder::TYPE_DEBX)
                ->setStartDate(new DT($startDate))
                ->setMonth($duration)
                ->setRate($apr)
                ->build($amount);
            $res = [];
            foreach ($repayPlan as $index => $repayTerm) {
                $res[$index] = [
                    'date' => $repayTerm->getEndDate()->add(new \DateInterval('P1D'))->format('Y-m-d'),
                    'principal' => Bc::round($repayTerm->getPrincipal(), 2),
                    'interest' => Bc::round($repayTerm->getInterest(), 2),
                ];
            }
            return $res;
        }

        if ($repaymentMethod === 1) {   //到期本息计算利息
            $interest = Bc::round(bcdiv(bcmul($amount, bcmul($duration, $apr, 14), 14), 365, 14), 2);

            return [
                [
                    'date' => $repaymentDates[0],    //还款日期
                    'principal' => $amount,   //还款本金,以元为单位
                    'interest' => $interest,    //还款利息,以元为单位
                ],
            ];
        }

        $res = [];
        $totalInterest = Bc::round(bcdiv(bcmul(bcmul($amount, $apr, 14), $duration, 14), 12, 14), 2);    //计算总利息
        if (bccomp($totalInterest, '0.00', 2) <= 0) {
            $totalInterest = '0.01';
        }
        $totalDays = (new \DateTime($startDate))->diff(new \DateTime(end($repaymentDates)))->days;
        foreach ($repaymentDates as $key => $val) {
            $principal = '0.00';
            if ($count == $key + 1) {
                $principal = $amount;
                //$interest = Bc::round(bcsub($totalInterest, array_sum(array_column($res, 'interest')), 2), 2);   //最后一期分期利息计算,用总的减去前面计算出来的,确保总额没有差错
            }
            if (in_array($repaymentMethod, [6, 7, 8, 9])) {
                $startDate = ($key === 0) ? $startDate : $repaymentDates[$key - 1];
                if ($val < $startDate) {
                    throw new \Exception('标的计息日不能小于还款日');
                }

                $refundDays = (new \DateTime($startDate))->diff(new \DateTime($val))->days;    //应还款天数
                $interest = Bc::round(bcmul(bcdiv(bcmul($amount, $apr, 14), 365, 14), $refundDays, 14), 2);
            } else {
                $interest = Bc::round(bcdiv($totalInterest, $count, 14), 2);    //普通计息和自然计息都按照14位精度严格计算,即从小数位后第三位舍去
            }

            $res[$key] = [
                'date' => $val,    //还款日期
                'principal' => $principal,   //还款本金
                'interest' => $interest,    //还款利息
            ];
        }

        return $res;
    }

    /**
     * 计算等额本息年化投资金额,计算公式为（1期本金*1 + 2期本金*2 + n期本金 * n）/12  1,2....n为期数
     * @param string $startDate
     * @param integer $duration
     * @param float $apr
     * @param integer $amount
     * @return int|float
     */
    public static function calcDebxAnnualInvest($startDate, $duration, $apr, $amount)
    {
        $annualInvestAmount = 0;
        $repayPlan = Builder::create(Builder::TYPE_DEBX)
            ->setStartDate(new DT($startDate))
            ->setMonth($duration)
            ->setRate($apr)
            ->build($amount);
        foreach ($repayPlan as $index => $repayTerm) {
            $key = $index + 1;
            $annualInvestAmount += Bc::round($repayTerm->getPrincipal(), 2) * $key;
        }
        $annualInvestAmount /= 12;

        return $annualInvestAmount;
    }
}
