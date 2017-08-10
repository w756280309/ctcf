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
        if (empty($mobileNumber)) {  //验证手机号的有效性,包括值和格式
            return '';
        }

        if (11 !== strlen($mobileNumber)) {   //验证手机号长度
            return $mobileNumber;
        }

        return substr_replace($mobileNumber, '******', 3, 6);
    }

    /**
     * 混淆座机号码(带区号, 区号和号码之间用"-"隔开)
     * todo CRM处使用，混淆规则需要再确认
     * @param $number
     * @return string
     */
    public static function obfsLandlineNumber($number)
    {
        return substr_replace($number, '****', strpos($number, '-') + 3, 4);
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

    /**
     * 将数字转成大写人民币形式
     * 注：
     * 1. 数字是两位小数
     * 2. 最高支持到千万
     * @param $num
     * @return float|int|string
     */
    public static function numToRmb($num)
    {
        $c1 = "零壹贰叁肆伍陆柒捌玖";
        $c2 = "分角元拾佰仟万拾佰仟亿";
        //精确到分后面就不要了，所以只留两个小数位
        $num = round($num, 2);
        //将数字转化为整数
        $num = $num * 100;
        if (strlen($num) > 10) {
            return $num;
        }
        $i = 0;
        $c = "";
        while (1) {
            if ($i == 0) {
                //获取最后一位数字
                $n = substr($num, strlen($num) - 1, 1);
            } else {
                $n = $num % 10;
            }
            //每次将最后一位数字转化为中文
            $p1 = substr($c1, 3 * $n, 3);
            $p2 = substr($c2, 3 * $i, 3);
            if ($n != '0' || ($n == '0' && ($p2 == '亿' || $p2 == '万' || $p2 == '元'))) {
                $c = $p1 . $p2 . $c;
            } else {
                $c = $p1 . $c;
            }
            $i = $i + 1;
            //去掉数字最后一位了
            $num = $num / 10;
            $num = (int) $num;
            //结束循环
            if ($num == 0) {
                break;
            }
        }
        $j = 0;
        $slen = strlen($c);
        while ($j < $slen) {
            //utf8一个汉字相当3个字符
            $m = substr($c, $j, 6);
            //处理数字中很多0的情况,每次循环去掉一个汉字“零”
            if ($m == '零元' || $m == '零万' || $m == '零亿' || $m == '零零') {
                $left = substr($c, 0, $j);
                $right = substr($c, $j + 3);
                $c = $left . $right;
                $j = $j - 3;
                $slen = $slen - 3;
            }
            $j = $j + 3;
        }
        //这个是为了去掉类似23.0中最后一个“零”字
        if (substr($c, strlen($c) - 3, 3) == '零') {
            $c = substr($c, 0, strlen($c) - 3);
        }
        //将处理的汉字加上“整”
        if (empty($c)) {
            return "零元整";
        } else {
            if (mb_strpos($c, '角') || mb_strpos($c, '分')) {
                return $c;
            } else {
                return $c . "整";
            }
        }
    }
}
