<?php

namespace common\lib\product;

use common\lib\bchelp\BcRound;
use Exception;

class ProductProcessor
{
    /**
     * 计算投标收益.
     *
     * @param array $order_info 订单信息：还款方式、年利率、投资金额 、起息日、项目期限、还款日期
     *
     * @return array $order_info 订单信息：还款方式、年利率、投资金额 、起息日、项目期限、还款日期、还款本息、还款利息、还款期数
     */
    public function getProductReturn($order_info)
    {
        $refund_method = $order_info['refund_method'];
        $yield_rate = $order_info['yield_rate'];
        $order_money = $order_info['order_money'];
        $expires = $order_info['expires'];

        if (empty($refund_method)) {
            throw new \Exception('Error: The $refund_method  isEmpty.');
        }
        if (empty($yield_rate)) {
            throw new \Exception('Error: The $yield_rate  isEmpty.');
        }
        if (empty($order_money)) {
            throw new \Exception('Error: The $order_money  isEmpty.');
        }

        if (empty($expires)) {
            throw new \Exception('Error: The $expires  isEmpty.');
        }

        bcscale(14);
        if ($refund_method == 2) {
            // 2.按月付息还本
            return;
        } elseif (($refund_method == 1)) {
            // 按天到期本息
            $order_info['order_return'] = $this->getProductDayReturn($yield_rate, $order_money, $expires);
        } else {
            return;
        }

        return $order_info;
    }

    /**
     * 计算投标收益.
     *
     * @param string $yield_rate  年利率
     * @param string $order_money 投资金额
     * @param string $expires     项目期限
     * @param string $isbc        是否保留2位
     *
     * @return string 产品到期日
     */
    public function getProductDayReturn($yield_rate, $order_money, $expires, $isbc = true)
    {
        if (empty($yield_rate)) {
            throw new \Exception('Error: The $yield_rate  isEmpty.');
        }
        if (empty($order_money)) {
            throw new \Exception('Error: The $order_money  isEmpty.');
        }

        if (empty($expires)) {
            throw new \Exception('Error: The $expires  isEmpty.');
        }
        bcscale(14);
        $day_lv = bcdiv($yield_rate, 360);
        $lixi = bcmul(bcmul($order_money, $day_lv), $expires);
        $bcround = new BcRound();

        return $isbc ? $bcround->bcround($lixi, 2) : $lixi;
    }

    /**
     * 按天到期本息
     * 按天到期本息，输入:项目还款方式 起息日、项目期限  返回:产品到期日
     * 按月付息还本，输入:项目还款方式 起息日、项目期限、返回：每一期的产品到期日.
     *
     * @param string $product_type 项目还款方式 d1:按天到期本息 m1:按月付息还本
     * @param string $order_date   起息日
     * @param string $period       项目期限
     *
     * @return string 产品到期日
     */
    public function LoanTerms($product_type = 'd1', $order_date = null, $period = null)
    {
        try {
            date_default_timezone_set('Asia/Shanghai');
            //验证
            if (!$product_type) {
                throw new \Exception('Error: The $product_type not defined.');
            }
            if (!$order_date) {
                throw new \Exception('Error: The $order_date type not defined.');
            }
            if (!$period) {
                throw new \Exception('Error: The period type not defined or is 0.');
            }

            if ($product_type != 'd1' && $product_type != 'm1' && $product_type != 'q1' && $product_type != 'y1') {
                throw new \Exception('Error: The $product_type type must be \'d1\' for day, or \'m1\' for month., or \'q1\' for quarter., or \'y1\' for year.');
            }
            //将起息日转换为北京时区
            if (!($this->checkDateIsValid($order_date))) {
                throw new \Exception('Error: The $order_date format is  not Y-m-d or Ymd.');
            }
            $order_datetime = null;
            if ($order_date) {
                $tz = new \DateTimeZone('Asia/Shanghai');
                $order_datetime = new \DateTime($order_date, $tz);
            }

            if ($product_type == 'd1') {
                $data_interval = new \DateInterval(sprintf('P%s%s', $period, 'D'));
                $order_datetime->add($data_interval);
            } elseif ($product_type == 'm1') {
                $data_interval = new \DateInterval(sprintf('P%s%s', $period, 'M'));
                $order_datetime->add($data_interval);
            } elseif ($product_type == 'q1') {
                $data_interval = new \DateInterval(sprintf('P%s%s', $period, 'M'));
                $order_datetime->add($data_interval);
            } elseif ($product_type == 'y1') {
                $data_interval = new \DateInterval(sprintf('P%s%s', $period, 'Y'));
                $order_datetime->add($data_interval);
            }

            if (!$order_datetime) {
                return;
            } else {
                return $order_datetime->format('Y-m-d');
            }
        } catch (\Exception $e) {
            exit($e->getMessage());
        }
    }

    /**
     * 校验日期格式是否正确.
     *
     * @param string $date    日期
     * @param string $formats 需要检验的格式数组
     *
     * @return bool
     */
    public function checkDateIsValid($date, $formats = array('Y-m-d', 'Y-n-j', 'Y/m/d', 'Y/n/j', 'Ymd', 'Ynj'))
    {
        $unixTime = strtotime($date);
        if (!$unixTime) { //strtotime转换不对，日期格式显然不对。
            return false;
        }

        //校验日期的有效性，只要满足其中一个格式就OK
        foreach ($formats as $format) {
            if (date($format, $unixTime) == $date) {
                return true;
            }
        }

        return false;
    }

    /**
     * 计算时间月份  项目期限.
     *
     * @param string                $tenderCompletedDate The deal load full timestamp in string, sucn as 2014-08-08.
     * @param Char                  $periodType          Whether 'D' or 'M', Day or Month
     * @param int                   $period              Day number, or Month number
     * @param int                   $dueDate             The deal's due date, this is timestamp in integer
     * @param bool or Integer (0|1) $amortized           该借款是否属于分期偿付
     */
    public function LoanTimes($tenderCompletedDate = null, $period = null, $dueDate = null, $periodType = 'd', $amortized = true)
    {
        defined('DEAL_PERIOD_TYPE_DAY') || define('DEAL_PERIOD_TYPE_DAY', 'D');
        defined('DEAL_PERIOD_TYPE_MONTH') || define('DEAL_PERIOD_TYPE_MONTH', 'M');
        $ret = null;
        $tz = new \DateTimeZone('Asia/Shanghai');
        try {
            if (!$period && !$dueDate) {
                throw new Exception('Error: The deal\'s period and due date is null.');
            }
            $periodType = $periodType ? strtoupper($periodType) : null;
            if (!$periodType) {
                throw new Exception('Error: The period type not defined.');
            }
            if ($periodType != DEAL_PERIOD_TYPE_DAY && $periodType != DEAL_PERIOD_TYPE_MONTH) {
                throw new Exception('Error: The period type must be \'d\' for day, or \'m\' for month.');
            }
            if ($dueDate) {
                $dt = new \DateTime();
                $dt->setTimestamp($dueDate);
                $dt->setTimezone($tz);
                $dueDate = $dt;
            }
            if ($tenderCompletedDate) {
                $dt = new \DateTime($tenderCompletedDate, $tz);
                $tenderCompletedDate = $dt;
            }
            if ($tenderCompletedDate) {
                if (!$dueDate) {
                    $dueDate = new \DateTime();
                    $dueDate->setTimestamp($tenderCompletedDate->format('U'));
                    $dueDate->setTimezone($tz);
                    $dueDate->add(new \DateInterval(sprintf('P%s%s', $period, $periodType)));
                }
                $period = $tenderCompletedDate->diff($dueDate);
                if (!$period->invert) {
                    if ($amortized) {
                        $monthNumber = 0;
                        if ($period->y) {
                            $monthNumber += $period->y * 12;
                        }
                        $monthNumber += $period->m;
                        if ($monthNumber) {
                            $formatStr = $tenderCompletedDate->format('Ymd') == $tenderCompletedDate->format('Ymt') ? 'Y-m-t' : 'Y-m-d';
                            for ($i = 1; $i <= $monthNumber; ++$i) {
                                if ($i == 1) {
                                    $lastDT = $tenderCompletedDate;
                                }
                                $lastU = $lastDT->format('U');
                                $lastDT->add(new \DateInterval('P1M'));
                                $nextU = $lastDT->format('U');
                                $last = new \DateTime();
                                $last->setTimestamp($lastU);
                                $last->setTimezone($tz);
                                $next = new \DateTime();
                                $next->setTimestamp($nextU);
                                $next->setTimezone($tz);
                                $dt1 = new \DateTime($last->format($formatStr), $tz);
                                $dt2 = new \DateTime($next->format($formatStr), $tz);
                                if ($dt2->format('Ym') == $dueDate->format('Ym') && $tenderCompletedDate->format('Ymd') == $tenderCompletedDate->format('Ymt')) {
                                    $dt2 = $dueDate;
                                }
                                $ret['days'][$i] = array('date' => $dt2->format('U'), 'length' => $dt1->diff($dt2)->days, 'period' => array('y' => $period->y, 'm' => $period->m, 'd' => $period->d, 'days' => $period->days));
                            }
                        }
                        if ($period->d) {
                            $key = 1;
                            if (isset($ret['days']) && $ret['days']) {
                                $key = count($ret['days']) + 1;
                            }
                            $ret['days'][$key] = array('date' => $dueDate->format('U'), 'length' => $period->d, 'period' => array('y' => $period->y, 'm' => $period->m, 'd' => $period->d, 'days' => $period->days));
                        }
                    } else {
                        $ret['days'][1] = array('date' => $dueDate->format('U'), 'length' => $period->days, 'period' => array('y' => $period->y, 'm' => $period->m, 'd' => $period->d, 'days' => $period->days));
                    }
                }
            } else {
                if (!$dueDate) {
                    $dueDate = new \DateTime();
                    $dueDate->setTimezone($tz);
                    $dueDate->add(new \DateInterval(sprintf('P%s%s', $period, $periodType)));
                }
                $now = new \DateTime();
                $now->setTimezone($tz);
                $period = $now->diff($dueDate);
                $ret = array('period' => array('y' => $period->y, 'm' => $period->m, 'd' => $period->d, 'days' => $period->days));
            }
            if ($ret && isset($ret['days']) && $ret['days']) {
                $ret['count'] = count($ret['days']);
            }

            return $ret;
        } catch (Exception $e) {
            exit($e->getMessage());
        }
    }

    /**
     * $periodType d:day m:month q:quarter  hy:half a year y:year.
     */
    public function getDays($periodType)
    {
        if (!$days && !$periodType) {
            throw new Exception('Error: day/periodType不能为空');
        }
        $periodType = $periodType ? strtoupper($periodType) : null;
        if (!$periodType) {
            throw new Exception('Error: The period type not defined.');
        }
        if ($periodType != 'D' && $periodType != 'M'  && $periodType != 'Q'  && $periodType != 'HY'  && $periodType != 'Y') {
            throw new Exception('Error: The period type must be \'d\' for day, or \'m\' for month., or \'q\' for quarter., or \'hy\' for half a year., or \'y\' for year.');
        }
        if ('D' === $periodType) {
            return 1;
        } elseif ('M' === $periodType) {
            return 30;
        } elseif ('Q' === $periodType) {
            return 90;
        } elseif ('HY' === $periodType) {
            return 180;
        } elseif ('Y' === $periodType) {
            return 360;
        } else {
            throw new Exception('异常期数');
        }
    }

    /**
     * @param $addm 增加月数  int
     * @param $date1 基准日期 Y-m-d
     *
     * @return 时间戳
     */
    public function calcRetDate($addm, $date1)
    {
        $date = strtotime("+$addm month", $date1);
        $date_tmp = strtotime("+$addm month", strtotime(date('Y-m', $date1)));

        if (date('m', $date) !== date('m', ($date_tmp))) {
            $lastday = new \DateTime('last day of '.date('Y-m', strtotime('-1 month', $date)));

            return $lastday->getTimestamp();
        } else {
            return $date;
        }
    }
}
