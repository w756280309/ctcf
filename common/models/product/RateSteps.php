<?php

namespace common\models\product;

/**
 * 阶梯利率.
 *
 * @author zhanghongyu<zhanghongyu@wangcaigu.com>
 */
class RateSteps
{
    /**
     * 根据浮动利率字符串返回浮动利率数组
     * @param string $str 浮动利率字符串
     * @return array 浮动利率数组
     */
    public static function parse($str)
    {
        $rateSteps = [];
        if (!self::checkRateSteps($str)) {
            return $rateSteps;
        }
        $rates = explode(PHP_EOL, $str);
        foreach ($rates as $rate) {
            if (0 !== strlen($rate)) {
                $data = explode(',', trim($rate));
                $rateSteps[] = ['min' => trim($data[0]), 'rate' => trim($data[1])];
            }
        }
        return $rateSteps;
    }

    /**
     * 检查浮动利率字符串是否合法
     * @param string $rateSteps
     * @param int|float $yield_rate
     * @return bool
     */
    public static function checkRateSteps($rateSteps, $yield_rate = 0)
    {
        if (!is_string($rateSteps) || empty($rateSteps)) {
            return false;
        }
        $rates = explode(PHP_EOL, $rateSteps);
        if (empty($rates)) {
            return false;
        }
        $last_rate = 0;
        $last_state = 0;
        foreach ($rates as $rate) {
            //空行忽略
            if (0 === strlen(trim($rate))) {
                continue;
            }
            if (!preg_match('/^(\s*\d+\.?\d*\s*),(\s*\d+\.?\d*\s*)$/', $rate)) {
                return false;
            } else {
                $data = explode(',', trim($rate));
                if (!isset($data[0]) || !isset($data[1])) {
                    return false;
                }
                //浮动利率和基础利率作比较
                if (floatval(trim($data[1])) <= $yield_rate) {
                    return false;
                }
                $s = floatval(trim($data[0]));
                $r = floatval(trim($data[1]));
                //下一个利率大于上一个利率
                $next_rate = $r;
                if ($next_rate <= $last_rate) {
                    return false;
                }
                $last_rate = $r;
                //下一个金额大于上一个金额
                $next_state = $s;
                if ($next_state <= $last_state) {
                    return false;
                }
                $last_state = $s;
            }
        }
        return true;
    }

    /**
     * 获取阶梯利率最大值
     * @param array $config 一个包含阶梯利率信息的数组
     * @return number|boolean 当输入数组为空时,返回false,否则,返回阶梯利率最大值
     */
    public static function getTopRate(array $config)
    {
        if (empty($config)) {
            return false;
        }

        $topRate = 0;
        foreach ($config as $val) {
            if (-1 === bccomp($topRate, $val['rate'], 1)) {
                $topRate = $val['rate'];
            }
        }

        return 0 === (int) $topRate ? false : $topRate;
    }

    /**
     * 根据投资额获取利率
     * @param array $config 阶梯利率配置
     * @param int|float $amount 金额
     * @return bool
     */
    public static function getRateForAmount($config, $amount)
    {
        $rate = false;
        foreach ($config as $val) {
            if (bccomp($val['min'], $amount, 2) <= 0) {
                $rate = $val['rate'];
                continue;
            }
        }
        return $rate;
    }
}
