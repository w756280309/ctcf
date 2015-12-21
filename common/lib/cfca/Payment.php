<?php

namespace common\lib\cfca;

use Yii;
/**
  本函数库需要openssl和curl支持。
 */
define("PAYURL", '');
define("TXURL", \Yii::$app->params['cfca']['apiUrl']);//
define("PAYURL2", '');
define("TXURL2", '');

//define("PATH", Yii::getAlias('@common').'/lib/cfca/');

class Payment {

// 签名函数
    function cfcasign_pkcs12($plainText) {
        $p12cert = array();
        $file = \Yii::$app->params['cfca']['clientKeyPath'];
        $fd = fopen($file, 'r');
        $p12buf = fread($fd, filesize($file));
        fclose($fd);
        openssl_pkcs12_read($p12buf, $p12cert, 'fake');

        $pkeyid = $p12cert["pkey"];
        $binary_signature = "";
        openssl_sign($plainText, $binary_signature, $pkeyid, OPENSSL_ALGO_SHA1);
        return bin2hex($binary_signature);
    }

// 验签函数
    function cfcaverify($plainText, $signature) {
//        $fcert = fopen(PATH."config/cfca.crt", "r");
//        $cert = fread($fcert, 8192);
//        fclose($fcert);
        $fcert = file_get_contents(\Yii::$app->params['cfca']['cfcaCertPath']);
        //var_dump($fcert);exit;
        $binary_signature = hex2bin($signature);
        $ok = openssl_verify($plainText, $binary_signature, $fcert);
        return $ok;
    }

//访问网页数据
    function get_web_content($curl_data) {
        $options = array(
            CURLOPT_RETURNTRANSFER => true, // return web page
            CURLOPT_HEADER => false, // don't return headers
            CURLOPT_FOLLOWLOCATION => true, // follow redirects
            CURLOPT_ENCODING => "", // handle all encodings
            CURLOPT_USERAGENT => "institution", // who am i
            CURLOPT_AUTOREFERER => true, // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120, // timeout on connect
            CURLOPT_TIMEOUT => 120, // timeout on response
            CURLOPT_MAXREDIRS => 10, // stop after 10 redirects
            CURLOPT_POST => 1, // i am sending post data
            CURLOPT_POSTFIELDS => $curl_data, // this are my post vars
            CURLOPT_SSL_VERIFYHOST => 0, // don't verify ssl
            CURLOPT_SSL_VERIFYPEER => false, //
            CURLOPT_VERBOSE => 1                //web输出
            //CURLOPT_VERBOSE => 0     //控制台输出     
        );

        $ch = curl_init(TXURL);
        curl_setopt_array($ch, $options);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Expect:"));
        $content = curl_exec($ch);
        curl_close($ch);
        return $content;
    }

    function get_web_content2($curl_data) {
        $options = array(
            CURLOPT_RETURNTRANSFER => true, // return web page
            CURLOPT_HEADER => false, // don't return headers
            CURLOPT_FOLLOWLOCATION => true, // follow redirects
            CURLOPT_ENCODING => "", // handle all encodings
            CURLOPT_USERAGENT => "institution", // who am i
            CURLOPT_AUTOREFERER => true, // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 120, // timeout on connect
            CURLOPT_TIMEOUT => 120, // timeout on response
            CURLOPT_MAXREDIRS => 10, // stop after 10 redirects
            CURLOPT_POST => 1, // i am sending post data
            CURLOPT_POSTFIELDS => $curl_data, // this are my post vars
            CURLOPT_SSL_VERIFYHOST => 0, // don't verify ssl
            CURLOPT_SSL_VERIFYPEER => false, //
            CURLOPT_VERBOSE => 1                //
        );

        $ch = curl_init(TXURL2);
        curl_setopt_array($ch, $options);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Expect:"));
        $content = curl_exec($ch);
        curl_close($ch);
        return $content;
    }

//同步交易方式向支付平台发送请求，支付平台返回一个数组，其中第一个元素为message，第二个为signature。注意这两个参数为支付平台返回。
    function cfcatx_transfer($message, $signature) {
        $post_data = array();
        $post_data['message'] = $message;
        $post_data['signature'] = $signature;

        $response = $this->get_web_content($this->data_encode($post_data));
        $response = trim($response);

        return explode(",", $response);
    }

    function cfcatx_transfer2($message, $signature) {
        $post_data = array();
        $post_data['message'] = $message;
        $post_data['signature'] = $signature;

        $response = $this->get_web_content2($this->data_encode($post_data));
        $response = trim($response);

        return explode(",", $response);
    }

//提交数据前要进行一下urlencode转换
    function data_encode($data, $keyprefix = "", $keypostfix = "") {
        assert(is_array($data));
        $vars = null;
        foreach ($data as $key => $value) {
            if (is_array($value))
                $vars .= data_encode($value, $keyprefix . $key . $keypostfix . urlencode("["), urlencode("]"));
            else
                $vars .= $keyprefix . $key . $keypostfix . "=" . urlencode($value) . "&";
        }
        return $vars;
    }

}

?>
