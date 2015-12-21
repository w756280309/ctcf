<?php
/**
 * Created by PhpStorm.
 * User: xmac
 * Date: 15-7-8
 * Time: 下午4:02
 */

namespace common\lib\bchelp;


class BcRound {
    function bcceil($number)
    {
        if (strpos($number, '.') !== false) {
            if (preg_match("~\.[0]+$~", $number)) return $this->bcround($number, 0);
            if ($number[0] != '-') return bcadd($number, 1, 0);
            return bcsub($number, 0, 0);
        }
        return $number;
    }

    function bcfloor($number)
    {
        if (strpos($number, '.') !== false) {
            if (preg_match("~\.[0]+$~", $number)) return $this->bcround($number, 0);
            if ($number[0] != '-') return bcadd($number, 0, 0);
            return bcsub($number, 1, 0);
        }
        return $number;
    }

    function bcround($number, $precision = 0)
    {
        if (strpos($number, '.') !== false) {
            if ($number[0] != '-') return bcadd($number, '0.' . str_repeat('0', $precision) . '5', $precision);
            return bcsub($number, '0.' . str_repeat('0', $precision) . '5', $precision);
        }
        return $number;
    }

}