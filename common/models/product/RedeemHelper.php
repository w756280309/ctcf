<?php

namespace common\models\product;

class RedeemHelper
{
    public static function checkRedemptionPeriods($redemptionPeriods)
    {
        if (!is_string($redemptionPeriods) || empty($redemptionPeriods)) {
            return false;
        }
        $redemptionPeriods = explode(PHP_EOL, $redemptionPeriods);
        if (empty($redemptionPeriods)) {
            return false;
        }

        foreach ($redemptionPeriods as $redemptionPeriod) {
            if (!preg_match('/^(\s*\d+),(\s*\d+\s*)$/', $redemptionPeriod)) {
                return false;
            }
            $redemptionPeriodArr = explode(',', $redemptionPeriod);
            if (empty($redemptionPeriodArr)) {
                return false;
            }
            foreach ($redemptionPeriodArr as $redemptionDate) {
                if (false === strtotime($redemptionDate)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * 格式化赎回开放申请时段用于前台显示
     *
     * origin:
     * 20171010,20171020
     * 20171110,20171120
     *
     * format:
     * 2017年10月10日-2017年10月20日；2017年11月10日-2017年11月20日
     *
     * @param string $redemptionPeriods 赎回开放申请时段
     * @param string $tail              行末字符
     *
     * @return string
     */
    public static function formatRedemptionPeriods($redemptionPeriods, $tail = '、')
    {
        if (!self::checkRedemptionPeriods($redemptionPeriods)) {
            return '';
        }

        $formatRedemptionPeriods = '';
        $redemptionPeriods = explode(PHP_EOL, $redemptionPeriods);
        foreach ($redemptionPeriods as $redemptionPeriod) {
            $redemptionPeriodArr = explode(',', $redemptionPeriod);
            $formatLine = '';
            foreach ($redemptionPeriodArr as $redemptionDate) {
                $formatLine .= date('Y年m月d日', strtotime($redemptionDate)) . '-';
            }
            $formatRedemptionPeriods .= rtrim($formatLine, '-') . $tail;
        }

        return rtrim($formatRedemptionPeriods, $tail);
    }

    /**
     * 格式化赎回付款日用于前台显示
     *
     * origin:
     * 20171010,20171020
     *
     * format:
     * 2017年10月10日、2017年10月20日
     *
     * @param string $redemptionPaymentDates 赎回付款日
     * @param string $tail                   行末字符
     *
     * @return string
     */
    public static function formatRedemptionPaymentDates($redemptionPaymentDates, $tail = '、')
    {
        $formatRedemptionPaymentDates = '';
        $redemptionPaymentDates = explode(',', $redemptionPaymentDates);
        foreach ($redemptionPaymentDates as $redemptionPaymentDate) {
            $formatRedemptionPaymentDates .= (date('Y年m月d日', strtotime($redemptionPaymentDate)) . $tail);
        }

        return rtrim($formatRedemptionPaymentDates, $tail);
    }
}