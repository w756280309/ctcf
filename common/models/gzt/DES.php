<?php
/**
 * Created by IntelliJ IDEA.
 * User: al
 * Date: 15-1-25
 * Time: 下午8:40
 */

namespace common\models\gzt;


class DES {
    var $key;
    var $iv; //偏移量
    private $cipher; //added

    function DES($key, $iv = 0) {
        $this->key = $key;
        if ($iv == 0) {
            $this->iv = $key;
        } else {
            $this->iv = $iv;
        }
    }

    //added
    function __construct($key, $iv = 0)
    {
        $this->DES($key, $iv);
        $this->cipher = mcrypt_module_open(MCRYPT_DES, '', 'cbc', ''); //added
    }

    //加密
    function encrypt($str) {
        //added
        mcrypt_generic_init($this->cipher, $this->key, $this->iv);
        $size = mcrypt_get_block_size ( MCRYPT_DES, MCRYPT_MODE_CBC );
        $str = $this->pkcs5Pad ( $str, $size );
        $result = mcrypt_generic($this->cipher, $str);
        mcrypt_generic_deinit($this->cipher);
        return base64_encode($result);
        //added end!
    }

    //解密
    function decrypt($str) {
        $str = base64_decode ( $str );
        //$strBin = $this->hex2bin( strtolower($str));
        $str = @mcrypt_cbc ( MCRYPT_DES, $this->key, $str, MCRYPT_DECRYPT,$this->iv );
        $str = $this->pkcs5Unpad ( $str );
        return $str;
    }

    function hex2bin($hexData) {
        $binData = "";
        for($i = 0; $i < strlen ( $hexData ); $i += 2) {
            $binData .= chr ( hexdec ( substr ( $hexData, $i, 2 ) ) );
        }
        return $binData;
    }

    function pkcs5Pad($text, $blocksize) {
        $pad = $blocksize - (strlen ( $text ) % $blocksize);
        return $text . str_repeat ( chr ( $pad ), $pad );
    }
    function pkcs5Unpad($text) {
        $pad = ord ( $text {strlen ( $text ) - 1} );
        if ($pad > strlen ( $text ))
            return false;
        if (strspn ( $text, chr ( $pad ), strlen ( $text ) - $pad ) != $pad)
            return false;
        return substr ( $text, 0, - 1 * $pad );
    }
}