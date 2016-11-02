<?php

namespace Wcg\DateTime;

class DT extends \DateTime
{
    /**
     * 为指定日期添加指定月数
     * @param $month
     * @return $this
     */
    public function addMonth($month)
    {
        $month = intval($month);
        if ($month <= 0) {
            return $this;
        }
        $m = intval($this->format('m'));
        $y = intval($this->format('Y'));
        $this->add(new \DateInterval('P' . $month . 'M'));
        if ($m + $month > 12) {
            //增加后的月份超过一年，取实际月份
            $_m = ($m + $month) % 12;// $_m 增加后的月份
        } else {
            $_m = $m + $month;
        }
        if ($_m !== intval($this->format('m'))) {
            $y = $y + intval(($m + $month) / 12);
            $m = $_m;
            $d = date('t', strtotime($y . '-' . $m . '-01'));//取该月最后一天
            $this->setDate($y, $m, $d);
        }
        return $this;
    }

    /**
     * 判断一个日期是否时当月最后一天
     * @return bool
     */
    public function isLastDayOfMonth()
    {
        return intval($this->format('d')) === intval($this->format('t'));
    }

    /**
     * 比较两个日期的时间差
     * 用法：(new Tx('2016-10-31'))->diff(new DT('2018-03-28'))
     * @param DT $dt
     * @return array
     */
    public function humanDiff(DT $dt)
    {
        if (!$dt instanceof DT) {
            return [];
        }
        $date1 = $this;
        $date2 = $dt;
        if ($date1 > $date2) {
            $date = $date1;
            $date1 = $date2;
            $date2 = $date;
        }
        $m2 = intval($date2->format('m'));//结束日期所在的月份
        $m1 = intval($date1->format('m'));//开始日期所在的月份
        $d1 = intval($date1->format('d'));//开始日期在当月的天数
        $d2 = intval($date2->format('d'));//结束日期在当月的天数

        $y = intval($date2->format('Y')) - intval($date1->format('Y'));//用结束日期的年减去开始时间的年，$y>=0
        //获取两个日期相差的月份，暂时不考考天的影响
        $m = $m2 - $m1;
        if ($m2 < $m1) {
            $y = $y - 1;
            $m = $m + 12;
        }

        if (
            $d1 === $d2//相同天，两个日期一定相隔整月。如 02-12 04-12 ;02-28 03-28;
            || (//开始时间和结束时间都是当月最后一天，且开始时间大于结束时间，两个日期相隔一定是整数 如 2016-01-31 和 2016-02-29
                $d1 > $d2
                && $date1->isLastDayOfMonth()
                && $date2->isLastDayOfMonth()
            )
        ) {
            $d = 0;
        } else {
            if ($d1 > $d2) {
                //考虑日对相隔月份影响，如果开始日 大于 结束日，那么上文中计算的月份会多算一个月
                $m = $m - 1;
            }
            $date = (new self($date1->format('Y-m-d')))->addMonth($y * 12 + $m);//开始日期加整月,$date <= $date2, 且相隔时间少于一个月
            if (intval($date->format('m')) !== $m2) {
                //计算结果和结束日期不在一个月，那么计算结果一定是结束日期的上一个月
                $d = intval($date->format('t')) - intval($date->format('d')) + $d2;
            } else {
                //计算结果和结束日期在一个月
                $d = $d2 - $d1;
            }
        }

        return ['y' => $y, 'm' => $m, 'd' => $d];
    }
}