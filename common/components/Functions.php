<?php

namespace common\components;

use Yii;
use yii\base\Component;

class Functions extends Component {

    public function getIp() {
        $ip = false;

        $seq = array('HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR'
            , 'HTTP_X_FORWARDED'
            , 'HTTP_X_CLUSTER_CLIENT_IP'
            , 'HTTP_FORWARDED_FOR'
            , 'HTTP_FORWARDED'
            , 'REMOTE_ADDR');

        foreach ($seq as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
                        return $ip;
                    }
                }
            }
        }
    }

    /**
     * 使用实例：\Yii::$app->functions->passport_encrypt("abcsssssssss",'njfae.cn');
     * @param type $txt 需要加密的文本
     * @param type $key 索引，解密时候需要同样索引
     * @return type
     */
    public function passport_encrypt($txt, $key) {
        srand((double) microtime() * 1000000);
        $encrypt_key = md5(rand(0, 32000));
        $ctr = 0;
        $tmp = '';
        for ($i = 0; $i < strlen($txt); $i++) {
            $ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
            $tmp .= $encrypt_key[$ctr] . ($txt[$i] ^ $encrypt_key[$ctr++]);
        }
        return base64_encode($this->passport_key($tmp, $key));
    }

    /**
     * 
     * @param type $txt 需要解密的文本
     * @param type $key 索引，加密时候的索引
     * @return type
     */
    public function passport_decrypt($txt, $key) {
        $txt = $this->passport_key(base64_decode($txt), $key);
        $tmp = '';
        for ($i = 0; $i < strlen($txt); $i++) {
            $md5 = $txt[$i];
            $tmp .= $txt[++$i] ^ $md5;
        }
        return $tmp;
    }

    public function passport_key($txt, $encrypt_key) {
        $encrypt_key = md5($encrypt_key);
        $ctr = 0;
        $tmp = '';
        for ($i = 0; $i < strlen($txt); $i++) {
            $ctr = $ctr == strlen($encrypt_key) ? 0 : $ctr;
            $tmp .= $txt[$i] ^ $encrypt_key[$ctr++];
        }
        return $tmp;
    }

    /**
     * 截取中文字符串
     * Utf-8、gb2312都支持的汉字截取函数
     * cut_str(字符串, 截取长度, 开始长度, 编码);
     * 编码默认为 utf-8
     * 开始长度默认为 0
     */
    public function cut_str($string, $sublen, $start = 0, $houzhui = "...", $code = 'UTF-8') {
        if ($code == 'UTF-8') {
            $pa = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/";
            preg_match_all($pa, $string, $t_string);

            if (count($t_string[0]) - $start > $sublen)
                return join('', array_slice($t_string[0], $start, $sublen)) . $houzhui;
            return join('', array_slice($t_string[0], $start, $sublen));
        }
        else {
            $start = $start * 2;
            $sublen = $sublen * 2;
            $strlen = strlen($string);
            $tmpstr = '';

            for ($i = 0; $i < $strlen; $i++) {
                if ($i >= $start && $i < ($start + $sublen)) {
                    if (ord(substr($string, $i, 1)) > 129) {
                        $tmpstr.= substr($string, $i, 2);
                    } else {
                        $tmpstr.= substr($string, $i, 1);
                    }
                }
                if (ord(substr($string, $i, 1)) > 129)
                    $i++;
            }
            if (strlen($tmpstr) < $strlen)
                $tmpstr.= $houzhui;
            return $tmpstr;
        }
    }

//GB转UTF-8编码
    public function gb2utf8($gbstr) {
        if (function_exists('iconv')) {
            return iconv('gbk', 'utf-8//ignore', $gbstr);
        }
        global $CODETABLE;
        if (trim($gbstr) == "") {
            return $gbstr;
        }
        if (empty($CODETABLE)) {
            $filename = DEDEINC . "/data/gb2312-utf8.dat";
            $fp = fopen($filename, "r");
            while ($l = fgets($fp, 15)) {
                $CODETABLE[hexdec(substr($l, 0, 6))] = substr($l, 7, 6);
            }
            fclose($fp);
        }
        $ret = "";
        $utf8 = "";
        while ($gbstr != '') {
            if (ord(substr($gbstr, 0, 1)) > 0x80) {
                $thisW = substr($gbstr, 0, 2);
                $gbstr = substr($gbstr, 2, strlen($gbstr));
                $utf8 = "";
                @$utf8 = u2utf8(hexdec($CODETABLE[hexdec(bin2hex($thisW)) - 0x8080]));
                if ($utf8 != "") {
                    for ($i = 0; $i < strlen($utf8); $i += 3)
                        $ret .= chr(substr($utf8, $i, 3));
                }
            } else {
                $ret .= substr($gbstr, 0, 1);
                $gbstr = substr($gbstr, 1, strlen($gbstr));
            }
        }
        return $ret;
    }

    /**
     * rsa 生成签名串
     * @param type $pri_key_path
     * @param type $data
     * @return string 64位加密串
     */
    public function rsaCreateSign($pri_key_path = "", $data = "") {
        $private = file_get_contents($pri_key_path);
        $res = openssl_pkey_get_private($private);
        $sign = "";
        if (openssl_sign($data, $out, $res)) {
            $sign = (base64_encode($out));
        }
        return $sign;
    }

    /**
     * rsa 验证签名
     * @param type $sign 经过64位加密的签名
     * @param type $data 需要验证的数据
     * @param type $pub_key_path 公钥路径 
     * @return boolean
     */
    public function rsaVerifySign($pub_key_path = "", $data = "", $sign = "") {
        $sig = base64_decode($sign);
        $public = file_get_contents($pub_key_path);
        $pkeyid = openssl_pkey_get_public($public);
        if (openssl_verify($data, $sig, $pkeyid) === 1) {
            return true;
        } else {
            return FALSE;
        }
    }

    /**
     * 私钥加密数据
     * @param type $pri_key_path 私钥路径
     * @param type $data 待加密数据
     * @return string
     */
    public function rsaPriEncrypt($pri_key_path = "", $data = "") {
        $private = file_get_contents($pri_key_path);
        $pi_key = openssl_pkey_get_private($private);
        openssl_private_encrypt($data, $encrypted, $pi_key); //私钥加密  
        $encrypted = base64_encode($encrypted); //加密后的内容通常含有特殊字符，需要编码转换下，在网络间通过url传输时要注意base64编码是否是url安全的  
        return $encrypted;
    }

    /**
     * 与加密私钥配对的公钥来解密
     * @param type $pub_key_path
     * @param type $encrypt
     * @return string
     */
    public function rsaPubDecrypt($pub_key_path = "", $encrypt = "") {
        $public = file_get_contents($pub_key_path);
        $pu_key = openssl_pkey_get_public($public); //这个函数可用来判断公钥是否是可用的  
        openssl_public_decrypt(base64_decode($encrypt), $decrypted, $pu_key); //私钥加密的内容通过公钥可用解密出来 
        return $decrypted;
    }

    /**
     * rsa 公钥加密
     * @param type $data
     * @return type
     */
    public function rsaPubEncrypt($pub_key_path = "", $data = "") {
        $public_key = file_get_contents($pub_key_path);
        $pu_key = openssl_pkey_get_public($public_key); //这个函数可用来判断公钥是否是可用的  
        openssl_public_encrypt($data, $encrypted, $pu_key); //公钥加密  
        $encrypted = base64_encode($encrypted);
        return $encrypted;
    }

    /**
     * rsa 私钥解密
     * @param type $encrypt
     * @return type
     */
    public function rsaPriDecrypt($pri_key_path = "", $encrypt = "") {
        $private = file_get_contents($pri_key_path);
        $pi_key = openssl_pkey_get_private($private);
        openssl_private_decrypt(base64_decode($encrypt), $decrypted, $pi_key); //私钥解密
        return $decrypted;
    }

    /**
     * 
     * @param type $rev 收件人
     * @param type $subject 主题
     * @param type $temp 模板
     * @param type $param  参数
     * @param type $from 发件人
     * @return boolean
     */
    public function sendEmail($rev = "", $subject = "", $temp = "", $param = array(), $from = 'service@njfae.cn') {
        $mail = Yii::$app->mailer->compose($temp, $param);
        $mail->setFrom($from);
        $mail->setTo($rev);
        $mail->setSubject($subject);
        $mail->send();
        return true;
    }

// $parent is the parent of the children we want to see 
// $level is increased when we go deeper into the tree, 
// used to display a nice indented tree
    /*
      通过数据库获取所有元素，通过下面函数构造树形结构
     */
    public function getTree($menus) {
        $id = $level = 0;
        $menuobjs = array();
        $tree = array();
        $notrootmenu = array();
        foreach ($menus as $menu) {
            $menuobj = new \stdClass();
            $menuobj->menu = $menu;
            $id = $menu['sn'];
            $level = $menu['psn'];
            $menuobj->nodes = array();
            $menuobjs[$id] = $menuobj;
            if ($level) {
                $notrootmenu[] = $menuobj;
            } else {
                $tree[] = $menuobj;
            }
        }

        foreach ($notrootmenu as $menuobj) {
            $menu = $menuobj->menu;
            $id = $menu['sn'];
            $level = $menu['psn'];
            $menuobjs[$level]->nodes[] = $menuobj;
        }
        return $tree;
    }

    public function bcceil($number) {
        if (strpos($number, '.') !== false) {
            if (preg_match("~\.[0]+$~", $number))
                return bcround($number, 0);
            if ($number[0] != '-')
                return bcadd($number, 1, 0);
            return bcsub($number, 0, 0);
        }
        return $number;
    }

    public function bcfloor($number) {
        if (strpos($number, '.') !== false) {
            if (preg_match("~\.[0]+$~", $number))
                return bcround($number, 0);
            if ($number[0] != '-')
                return bcadd($number, 0, 0);
            return bcsub($number, 1, 0);
        }
        return $number;
    }

    public function bcround($number, $precision = 0) {
        if (strpos($number, '.') !== false) {
            if ($number[0] != '-')
                return bcadd($number, '0.' . str_repeat('0', $precision) . '5', $precision);
            return bcsub($number, '0.' . str_repeat('0', $precision) . '5', $precision);
        }
        return $number;
    }

    public function toFormatMoney($number) {
        if (strlen(intval($number)) > 8) {
            return ($number / 100000000) . '亿元';
        }
        if (strlen(intval($number)) > 4) {
            return ($number / 10000) . '万元';
        }
        return $number . "元";
    }

    /**
     * 创建合同pdf
     */
    public function createHetong($header = "", $content = "", $file = "", $op = "I") {
        set_time_limit(0);
        require_once "tcpdf/tcpdf.php";
        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetHeaderData('', '', $header, '');

        // set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        // add a page
        $pdf->AddPage();
        // set font
        //$pdf->SetFont('stsongstdlight', '', 5);
        $txt = $content;

        $pdf->MultiCell(0, '', $txt, 0, 'L', $fill = 0, $ln = 1, '', '', 0, true, true, 0);
        //MultiCell(宽, 高, 内容, 边框,文字对齐, 文字底色, 是否换行, x坐标, y坐标, 变高, 变宽, 是否支持html, 自动填充, 最大高度)

        $file_name = $file; //不知中文怎么支持
        $pdf->Output($file_name . ".pdf", $op); /* 默认是I：在浏览器中打开，D：下载，F：在服务器生成pdf */
    }

    /**
     * 生成xml的响应
     * code 开头为E的为金交自定义错误
     * @param type $code
     * @param type $message
     * @return type
     */
    public function createXmlResponse($code = '0000', $message = '') {
        $simpleXML = new \SimpleXMLElement(\Yii::$app->params['cfca']['ownerror']);
        $simpleXML->Code = $code;
        $simpleXML->Message = $message;
        $xmlStr = $simpleXML->asXML();
        return $xmlStr;
    }

    /**
     * 根据时间戳获取时间描述
     * @param type $time
     */
    public function getDateDesc($time = "") {
        if (date("d", $time) == date("d") + 1) {
            return ['today' => 0, 'desc' => '明日', 'time' => $time];
        } else if (date("d", $time) == date("d")) {
            return ['today' => 1, 'desc' => '今日', 'time' => $time];
        } else {
            return ['today' => 0, 'desc' => date('m月d日', $time), 'time' => $time];
        }
    }

    /**
     * 
     * @param type $len 长度
     * @param type $simple 1 简单 2 复杂
     * @return type
     */
    public function createRandomStr($len = 6, $simple = 1) {
        if (!in_array($simple, array(1, 2))) {
            return FALSE;
        }
        $str = '';
        $chars = "";
        if ($simple == 1) {
            $chars = '0123456789';
        } else {
            $chars = 'abcdefghijkmnpqrstuvwxyz23456789ABCDEFGHIJKMNPQRSTUVWXYZ'; //去掉1跟字母l防混淆      
        }
        if ($len > strlen($chars)) {//位数过长重复字符串一定次数
            $chars = str_repeat($chars, ceil($len / strlen($chars)));
        }
        $chars = str_shuffle($chars); //打乱字符串
        $str = substr($chars, 0, $len);
        return $str;
    }

    public function dealurl($path = null) {
        $arr = parse_url($path);
        $ret = $arr['path'];
        if (isset($arr['query'])) {
            $ret .= "?" . $arr['query'];
        }
        return $ret;
    }

    /**
     * 获取时间差值
     * @param type $begin_time
     * @param type $end_time
     * @return type
     */
    public function timediff($begin_time, $end_time) {
        if ($begin_time < $end_time) {
            $starttime = $begin_time;
            $endtime = $end_time;
        } else {
            $starttime = $end_time;
            $endtime = $begin_time;
        }
        $timediff = $endtime - $starttime;
        $days = intval($timediff / 86400);
        $remain = $timediff % 86400;
        $hours = intval($remain / 3600);
        $remain = $remain % 3600;
        $mins = intval($remain / 60);
        $secs = $remain % 60;
        $res = array("day" => $days, "hour" => $hours, "min" => $mins, "sec" => $secs);
        return $res;
    }

    /**
     * 实现01，001，0001……递增功能
     * @param type $num
     * @param type $step
     * @return type
     */
    public function autoInc($num,$step=1){
        $arr=str_split($num);
        $count=count($arr);
        for($i=0,$zero_count=0,$num_new='',$flag=0;$i<$count;$i++){
            if($arr[$i]=='0' and $flag==0){
                $zero_count++;
            }
            elseif(is_numeric($arr[$i])){
                $flag=1;
                $num_new.=$arr[$i];
            }
            else{
                exit('错误:含有非数字字符');
            }
        }
        $num_new=intval($num_new)+$step;
        if($num_new>pow(10,$count-1)){
            return $num_new;
        }
        else{
            return str_pad('',$count-count(str_split($num_new)),'0').($num_new);
        }
    }
    
}
