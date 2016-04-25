<?php

namespace common\lib\StringUtils;

class StringUtils
{
    /**
     * 保留身份证号里面的生日信息,其他信息一律隐藏
     * @param string|int $idCardNo 字符串格式或数字格式的身份证号
     * @return string 完成隐藏信息处理的身份证号字符串
     */
    public static function obfsIdCardNo($idCardNo)
    {
        if (empty($idCardNo) || (!is_integer($idCardNo) && !is_string($idCardNo))) {    //验证身份证号的有效性,包括值和格式
            return null;
        }

        $length = strlen($idCardNo);
        if (!in_array($length, [15, 18])) {    //验证身份证号长度有效性
            return null;
        }

        if (15 === $length) {    //15位身份证号替换规则
            return substr_replace(substr_replace($idCardNo, '******', 0, 6), '***', -3);
        }

        return substr_replace(substr_replace($idCardNo, '******', 0, 6), '****', -4);
    }

    /**
     * 隐藏手机号4-9位
     * @param string|int $mobileNumber 字符串格式或数字格式的手机号
     * @return string 隐藏后的手机号字符串
     */
    public static function obfsMobileNumber($mobileNumber)
    {
        if (empty($mobileNumber) || (!is_integer($mobileNumber) && !is_string($mobileNumber))) {   //验证手机号的有效性,包括值和格式
            return null;
        }

        if (11 !== strlen($mobileNumber)) {   //验证手机号长度
            return null;
        }

        return substr_replace($mobileNumber, '******', 3, 6);
    }
}

