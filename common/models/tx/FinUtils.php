<?php

namespace common\models\tx;

class FinUtils
{
    //根据订单和标的计算应付利息,以分为单位
    public static function calculateCurrentProfit(Loan $loan, $amount, $apr)
    {
        $date = date('Y-m-d');
        $startDate = $loan->startDate;
        $plans = $loan->getRepaymentPlan($amount, $apr);//还款计划
        $count = count($plans);
        if ($count <= 0) {
            return 0;
        }
        if ($date < $startDate || $date > $plans[$count - 1]['date']) {
            return 0;
        }
        foreach ($plans as $key => $plan) {
            if ($key === 0) {
                if ($date >= $startDate && $date < $plan['date']) {
                    $newDate = (new \DateTime($date))->add(new \DateInterval('P1D'))->format('Y-m-d');
                    $planDuration = (new \DateTime($startDate))->diff(new \DateTime($plan['date']))->days;//第一期天数
                    $duration = (new \DateTime($startDate))->diff(new \DateTime($newDate))->days;//当天到第一个
                    $planInterest = $plan['interest'];
                }
            } else {
                if ($date >= $plans[$key - 1]['date'] && $date < $plan['date']) {
                    $newDate = (new \DateTime($date))->add(new \DateInterval('P1D'))->format('Y-m-d');
                    $planDuration = (new \DateTime($plans[$key - 1]['date']))->diff(new \DateTime($plan['date']))->days;//当期天数
                    $duration = (new \DateTime($plans[$key - 1]['date']))->diff(new \DateTime($newDate))->days;//当天到上一个还款日天数
                    $planInterest = $plan['interest'];
                }
            }
        }
        if (isset($planDuration) && isset($duration) && isset($planInterest)) {
            $profit = bcdiv(bcmul($duration, bcmul($planInterest, 100, 14), 14), $planDuration, 0);

            return $profit;
        } else {
            return '0';
        }
    }

    /**
     * 计算利息
     * 温都金服项目总利息计算公式
     * （1）按天计息  利息 = 本金 * 利率 *（时间 - 计息时间）/ 365
     * （2）按月计息  利息 = 本金 * 利率 * 期限（以月为单位） / 12 * (时间 - 计息时间) / 项目总天数.
     *
     * 参数传入情况
     * （1）按天计息  startDate\interestCalcType\apr\isAmortized\basisDays
     * （2）按月计息  startDate\duration\interestCalcType\apr\isAmortized
     *
     * 开始时间(string，Y-m-d，startDate)
     * 项目期限(int,duration)
     * 计息方式（int,interestCalcType）,1:按天;2:按月
     * 年化基数（int,basisDays）,365:一年365天;360:一年360天
     * 利率（真实利率（8.5%的利率，$apr = 0.085） float,apr）
     * 是否分期 (bool, isAmortized),false:不分期;true:分期
     *
     * @param array  $loan   标的信息
     * @param string $amount 资金
     * @param string $date   时间点
     *
     * @throws \Exception
     *
     * @return string
     */
    public static function calculateInterest($loan, $amount, $date)
    {
        if (!isset($date)
            || !in_array($loan['interestCalcType'], [1, 2])
            || !isset($loan['startDate'])
            || !isset($loan['apr'])
            || !isset($amount)
            || !isset($date)
        ) {
            throw new \Exception('参数错误 ');
        }
        if ($amount <= 0) {
            throw new \Exception('金额不能小于0');
        }
        if ($date < $loan['startDate']) {
            throw new \Exception('日期不能小于标的计息日期');
        }
        $interest = 0;
        $diff = (new \DateTime($loan['startDate']))->diff(new \DateTime($date));
        if ($loan['interestCalcType'] === 1) {
            //按天计息
            if (!isset($loan['basisDays']) || $loan['basisDays'] < 0) {
                throw new \Exception('参数错误 ');
            }
            if (!$loan['isAmortized']) {
                //不分期
                $duration = $diff->days;
                $interest = bcdiv(bcmul(bcmul($amount, $loan['apr'], 14), $duration, 14), $loan['basisDays'], 0);
            }
        } else {
            if (!isset($loan['duration'])) {
                throw new \Exception('参数错误');
            }
            //按月计息
            if ($loan['isAmortized']) {
                //分期
                $endTime = self::calcRetDate($loan['duration'], strtotime($loan['startDate']));
                $totalDays = (new \DateTime($loan['startDate']))->diff(new \DateTime(date('Y-m-d', $endTime)))->days;
                $days = $diff->days;
                $interest = bcdiv(bcmul(bcmul(bcmul($amount, $loan['apr'], 14), $loan['duration'], 14), $days, 14), bcmul(12, $totalDays, 14), 0);
            }
        }

        return $interest;
    }

    //给指定日期加指定月（天）
    private static function calcRetDate($add, $startTime, $unit = 'm')
    {
        if ($unit === 'm') {
            $time = strtotime("+$add month", $startTime);
            $tmp_time = strtotime("+$add month", strtotime(date('Y-m', $startTime)));
            if (date('m', $time) !== date('m', ($tmp_time))) {
                $time = strtotime('last day of '.date('Y-m', strtotime('-1 month', $time)));
            }

            return $time;
        } elseif ($unit === 'd') {
            $time = strtotime("+$add day", $startTime);

            return $time;
        } else {
            throw new \Exception('暂时只支持月、天');
        }
    }

    /**
     * 判断一个债权是否可以转让（根据债权日期）.
     *
     * 计息日期(string, Y-m-d, startDate)
     * 宽限期(int, graceDays)无宽限期时可以不传
     * 还款日(array, repaymentDate),至少包含一个日期
     * 项目期限(int, expires),若为分期，单位为月,到期本息为日
     * 是否分期(int, isAmortized),true分期false未分期
     * 订单最低持有X天(int, holdDays)
     * 转让周期Y天(int, duration)
     * 债权转让日期(string, Y-m-d, date)
     * 分期项目资产发起转让限制条件(int, loan_fenqi_limit)
     * 不分期（到期本息）项目资产发起转让限制条件(int, loan_daoqi_limit)
     *
     * @param array  $loan   标的信息  [startDate,graceDays,repaymentDate,expires,isAmortized]
     * @param array  $config 债权配置信息[holdDays,duration,loan_fenqi_limit,loan_daoqi_limit]
     * @param string $date   债权日期
     *
     * @return bool
     *
     * @throws \Exception
     */
    public static function canBuildCreditByDate($loan, $config, $date)
    {
        $expires = $loan['expires'];
        $isAmortized = $loan['isAmortized'];
        if ($isAmortized) {
            $months_limit = $config['loan_fenqi_limit'];
            if ($expires <= $months_limit) {
                throw new \Exception('分期项目小于等于'.$months_limit.'个月不能转让');
            }
        } else {
            $days_limit = $config['loan_daoqi_limit'];  
            if ($expires <= $days_limit) {
                throw new \Exception('到期本息项目小于等于'.$days_limit.'天不能转让');
            }
        }
        $repaymentDate = $loan['repaymentDate'];
        $graceDays = isset($loan['graceDays']) ? $loan['graceDays'] : 0;
        //计算下一个还款日
        $count = count($repaymentDate);//还款日数组至少要包含一个日期
        if ($count === 0) {
            throw new \Exception('还款日不能为空');
        }
        if ($loan['startDate'] > $date) {
            throw new \Exception('开始时间异常');
        }
        if ($date > $repaymentDate[$count - 1]) {
            throw new \Exception('转让日期不能大于最后一个还款日');
        }
        if ($count > 1) {
            //判断债权日期在分期的哪一阶段
            foreach ($repaymentDate as $key => $value) {
                if ($key === 0) {
                    if ($date > $loan['startDate'] && $date <= $value) {
                        $nextDate = $value;
                    }
                } else {
                    if ($date > $repaymentDate[$key - 1] && $date <= $value) {
                        $nextDate = $value;
                    }
                }
            }
        }
        if (!isset($nextDate)) {
            $tmpDate = end($repaymentDate);
            if ($graceDays > 0) {
                $tmpDate = date('Y-m-d', strtotime('- '.$graceDays.' day', strtotime($tmpDate)));
            }
            $nextDate = $tmpDate;
        }
        if ($date >= $nextDate) {
            throw new \Exception('转让日期不能大于下一个还款日');
        }

        //判断是否满足条件 持有X天可转让
        $date = new \DateTime($date);
        $startDate = new \DateTime($loan['startDate']);
        $endDate = $startDate->add(new \DateInterval('P'.$config['holdDays'].'D'));
        if ($date < $endDate) {
            throw new \Exception('至少持有'.$config['holdDays'].'天可转让');
        }
        //判断是否满足条件  下一个还款日前Y天不可转让
        $newDate = $date->add(new \DateInterval('P'.$config['duration'].'D'));
        $expireDate = new \DateTime($nextDate);

        if ($newDate >= $expireDate) {
            throw new \Exception('下一个还款日前'.$config['duration'].'天不可转让');
        }

        return true;
    }

    /**
     * 判断一个债权是否可以转让（根据金额）.
     *
     * @param string $excessAmount    剩余可转让金额(float, excessAmount)
     * @param string $amount          待转让债权金额(float, amount)
     * @param string $minOrderAmount  债权起投金额(float, minOrderAmount)
     * @param string $incrOrderAmount 债权递增金额(float, incrOrderAmount)
     *
     * @throws \Exception
     *
     * @return bool
     */
    public static function canBuildCreditByAmount($excessAmount, $amount, $minOrderAmount, $incrOrderAmount)
    {
        if (bccomp($amount, 0, 0) <= 0) {
            throw new \Exception('转让金额必须是大于零');
        }
        if (bccomp($amount, $excessAmount, 0) > 0) {
            throw new \Exception('转让金额不能超过可转让金额');
        }
        //可转让金额大于起投金额
        if (bccomp($excessAmount, $minOrderAmount, 0) >= 0) {
            if (bccomp($amount, $minOrderAmount, 0) < 0) {
                throw new \Exception('转让金额必须大于起投金额');
            }
            if (bccomp(bcsub($excessAmount, $amount, 0), $minOrderAmount, 0) < 0) {
                if (bccomp($amount, $excessAmount, 0) !== 0) {
                    throw new \Exception('必须将剩余金额全部转让');
                }
            } else {
                if (bccomp(bcmod(bcsub($amount, $minOrderAmount, 0), $incrOrderAmount), 0) !== 0) {
                    throw new \Exception('转让金额必须以递增金额的整数倍递增');
                }
            }
        } else {
            if (bccomp($amount, $excessAmount, 0) !== 0) {
                throw new \Exception('必须将剩余金额全部转让');
            }
        }

        return true;
    }

    public static function generateSn($prefix = '')
    {
        list($sec, $usec) = explode('.', sprintf('%.6f', microtime(true)));

        return $prefix
        .date('ymdHis', $sec)
        .$usec
        .mt_rand(1000, 9999);
    }
}
