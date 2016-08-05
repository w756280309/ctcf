<?php

namespace common\utils;

class StringUtils
{
    /**
     * 保留身份证号里面的生日信息,其他信息一律隐藏
     * @param string $idCardNo 字符串格式的身份证号
     * @return string 完成隐藏信息处理的身份证号字符串
     */
    public static function obfsIdCardNo($idCardNo)
    {
        if (empty($idCardNo) || !is_string($idCardNo)) {    //验证身份证号的有效性,包括值和格式
            return '';
        }

        $length = strlen($idCardNo);
        if (!in_array($length, [15, 18])) {    //验证身份证号长度有效性
            return $idCardNo;
        }

        if (15 === $length) {    //15位身份证号替换规则
            return substr_replace(substr_replace($idCardNo, '******', 0, 6), '***', -3);
        }

        return substr_replace(substr_replace($idCardNo, '******', 0, 6), '****', -4);
    }

    /**
     * 隐藏手机号4-9位
     * @param string $mobileNumber 字符串格式的手机号
     * @return string 隐藏后的手机号字符串
     */
    public static function obfsMobileNumber($mobileNumber)
    {
        if (empty($mobileNumber) || !is_string($mobileNumber)) {   //验证手机号的有效性,包括值和格式
            return '';
        }

        if (11 !== strlen($mobileNumber)) {   //验证手机号长度
            return $mobileNumber;
        }

        return substr_replace($mobileNumber, '******', 3, 6);
    }

    /**
     * 隐藏姓名部分信息,四个字的隐藏前两个字,其他的隐藏前一个字
     * @param string $name 字符串格式的姓名
     * @return string 隐藏后的姓名字符串
     */
    public static function obfsName($name)
    {
        if (empty($name) || !is_string($name)) {   //验证手机号的有效性,包括值和格式
            return '';
        }

        $num = mb_strlen($name, 'utf-8');

        if (1 === $num) {
            return $name;
        }

        if ($num >= 4) {
            return '**'.mb_substr($name, 2, $num - 2, 'utf-8');
        }

        return '*'.mb_substr($name, 1, $num - 1, 'utf-8');
    }

    /**
     * 金额显示样式一:
     * 1. 添加千分位显示
     * 2. 以万、亿为单位
     * 3. 小数点后保留到金额的分,不是两位，例如以万为单位的应为4+2=6位
     *
     * @param string $html html模板字符串,样式如下:
     * <span>{amount}</span><span>{unit}</span>
     */
    public static function amountFormat1($html, $val)
    {
        $len = strlen(intval($val));
        $wei = 0;
        $dw = '元';
        if ($len > 8) {
            $wei = 8;
            $dw = '亿元';
        } else if ($len > 4) {
            $wei = 4;
            $dw = '万元';
        }

        return str_replace(['{amount}', '{unit}'], [rtrim(rtrim(number_format($val / pow(10, $wei), $wei + 2), '0'), '.'), $dw], $html);
    }

    /**
     * 金额显示样式二:
     * 1. 添加千分位显示
     * 2. 去除小数点最右端多余的0,不带计数单位
     */
    public static function amountFormat2($val)
    {
        return rtrim(rtrim(self::amountFormat3($val), '0'), '.');
    }

    /**
     * 金额显示样式三:
     * 1. 添加千分位显示
     * 2. 小数位保留两位,不带计数单位
     */
    public static function amountFormat3($val)
    {
        return number_format($val, 2);
    }
}
