<?php

namespace common\helpers;

/**
 * Description of HttpHelper
 *
 * @author Lee
 */
class HttpHelper {

    /**
     * @desc 发送http请求
     * @param $requestUrl
     * @param $postData
     * @return mixed
     */
    public static function doRequest($requestUrl, $postData = array()) {
        $post_string = self::postArrayToString($postData);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $requestUrl);
        curl_setopt($ch, CURLOPT_POST, strlen($post_string));
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //禁止直接显示获取的内容 重要
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //不验证证书下同
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); //
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    /**
     * @desc urlencode request datas
     * @param string $req
     * @return string
     */
    private static function postArrayToString($req = array()) {
        $tmp = array();
        foreach ($req as $key => $value) {
            array_push($tmp, "$key=" . urlencode($value));
        }
        return implode("&", $tmp);
    }

    public static function doGet($requestUrl){
        $ch = curl_init($requestUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true) ; // 获取数据返回
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true) ; // 在启用 CURLOPT_RETURNTRANSFER 时候将获取数据返回
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //禁止直接显示获取的内容 重要
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //不验证证书下同
        $output = curl_exec($ch) ;
        curl_close($ch);
        return $output;
    }

    //获取客户端ip
    public static function getClientIP(){
        if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown")){
            $ip = getenv("HTTP_CLIENT_IP");
        }else if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown")){
            $ip = getenv("HTTP_X_FORWARDED_FOR");
        }else if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown")){
            $ip = getenv("REMOTE_ADDR");
        }else if (isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown")){
            $ip = $_SERVER['REMOTE_ADDR'];
        }else{
            $ip = "unknown";
        }

        if (false !== strpos($ip, ','))
            $ip = reset(explode(',', $ip));

        if (preg_match('#^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$#', $ip)) {
            $ip_array = explode('.', $ip);
            if($ip_array[0]<=255 && $ip_array[1]<=255 && $ip_array[2]<=255 && $ip_array[3]<=255){
                return $ip;
            }
        }
        return "unknown";
    }

}