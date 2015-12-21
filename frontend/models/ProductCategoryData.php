<?php

namespace frontend\models;

use Yii;
use common\models\product\ProductCategory;

/**
 * Description of ProductCategoryData
 *
 * @author zhy-pc
 */
class ProductCategoryData {

    public static $src = "";
   // static $basical = array(0=>"零","壹","贰","叁","肆","伍","陆","柒","捌","玖");
   // static $advanced=array(1=>"拾","佰","仟");
    static $basical = array(0=>"0","1","2","3","4","5","6","7","8","9");
    static $advanced=array(1=>"0","00","000");
  
    public function category($conditon) {
        $data = ProductCategory::getCategoryTree($conditon);
        return $data;
    }

    public function getSubCat($conditon) {
        return ProductCategory::findAll($conditon);
    }

    //中文字符串截取
    public function msubstr($str, $start = 0, $length, $charset = "utf-8", $suffix = true) {
        if (empty($str)) {
            return;
        }
        $sourcestr = $str;
        $cutlength = $length;
        $returnstr = '';
        $i = 0;
        $n = 0.0;
        $str_length = strlen($sourcestr); //字符串的字节数
        while (($n < $cutlength) and ($i < $str_length)) {
            $temp_str = substr($sourcestr, $i, 1);
            $ascnum = ord($temp_str);
            if ($ascnum >= 252) {
                $returnstr = $returnstr . substr($sourcestr, $i, 6);
                $i = $i + 6;
                $n++;
            } elseif ($ascnum >= 248) {
                $returnstr = $returnstr . substr($sourcestr, $i, 5);
                $i = $i + 5;
                $n++;
            } elseif ($ascnum >= 240) {
                $returnstr = $returnstr . substr($sourcestr, $i, 4);
                $i = $i + 4;
                $n++;
            } elseif ($ascnum >= 224) {
                $returnstr = $returnstr . substr($sourcestr, $i, 3);
                $i = $i + 3;
                $n++;
            } elseif ($ascnum >= 192) {
                $returnstr = $returnstr . substr($sourcestr, $i, 2);
                $i = $i + 2;
                $n++;
            } elseif ($ascnum >= 65 and $ascnum <= 90 and $ascnum != 73) {
                $returnstr = $returnstr . substr($sourcestr, $i, 1);
                $i = $i + 1;
                $n++;
            } elseif (!(array_search($ascnum, array(37, 38, 64, 109, 119)) === FALSE)) {
                $returnstr = $returnstr . substr($sourcestr, $i, 1);
                $i = $i + 1;
                $n++;
            } else {
                $returnstr = $returnstr . substr($sourcestr, $i, 1);
                $i = $i + 1;
                $n = $n + 0.5;
            }
        }
        if ($i < $str_length) {
            $returnstr = $returnstr . '...';
        }
        return $returnstr;
    }
    
    public function toFormatMoney($number){
        $numarr = explode('.', $number);
        if(intval($numarr[1])){
            return $number."元";
        }
        if(strlen(intval($number))>8){
            return ($number/100000000).'亿元';
        }
        if(strlen(intval($number))>4){
            return ($number/10000).'万元';
        }
        return $number."元";
    }

//    public function toFormatMoney($number) {
//        $number = intval($number);
//        //var_dump($number);exit;
//        $arr = array_reverse(str_split($number));
//        $data = '';
//        $zero = false;
//        $zero_num = 0;
//        foreach ($arr as $k => $v) {
//            $_chinese = '';
//            $zero = ($v == 0) ? true : false;
//            $x = $k % 4;
//            if ($x && $zero && $zero_num > 1)
//                continue;
//            switch ($x) {
//                case 0:
//                    if ($zero) {
//                        $zero_num = 0;
//                    } else {
//                        $_chinese = self::$basical[$v];
//                        $zero_num = 1;
//                    }
//                    if ($k == 8) {
//                        $_chinese.='亿';
//                    } elseif ($k == 4) {
//                        $_chinese.='万';
//                    }
//                    break;
//                default:
//                    if ($zero) {
//                        if ($zero_num == 1) {
//                            $_chinese = self::$basical[$v];
//                            $zero_num++;
//                        }
//                    } else {
//                        $_chinese = self::$basical[$v];
//                        $_chinese.=self::$advanced[$x];
//                    }
//            }
//            $data = $_chinese . $data;
//        }
//        return $data . '元';
//    }

}
