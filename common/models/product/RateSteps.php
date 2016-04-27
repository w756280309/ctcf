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
        if (empty($str) || !is_string($str)) {
            return $rateSteps;
        }
        $rates = explode(PHP_EOL, $str);
        foreach ($rates as $rate) {
            if (preg_match('/^(\s*\d+\.?\d*\s*),(\s*\d+\.?\d*\s*)$/', $rate)) {
                $data = explode(',', trim($rate));
                if (isset($data[0]) && isset($data[1])) {
                    $rateSteps[] = ['min' => trim($data[0]), 'rate' => trim($data[1])];
                }
            }
        }
        return $rateSteps;
    }

    public static function getTopRate(array $config)
    {
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
