<?php
/**
 * Created by IntelliJ IDEA.
 * User: al
 * Date: 15-1-25
 * Time: 下午8:43
 * 使用方法：$tong_model = new \common\models\GuoZhengTong();
             $re = $tong_model->check("王海燕", '130982198206230960');
 */

namespace common\models;

use common\models\gzt\DES;

use SimpleXMLElement;
use SoapClient;


class GuoZhengTong
{
    private $Key = '12345678';
    private $iv = '12345678';
    private $partner = 'nanjingjinjiao02';
    private $partnerPW = 'nanjingjinjiao02_g6Y3rsf_';
    private $supportClass = array ("1A020201" => "Name,CardNum" );
    private $wsdlURL = 'http://gboss.id5.cn/services/QueryValidatorServices?wsdl';

    //name  GB18030 编码
    public static function check($name, $idNumber)
    {
        $model = new static;
        $xml = new SimpleXMLElement($model->getData('1A020201', sprintf("%s,%s", iconv("utf-8","GB18030//IGNORE",$name), $idNumber)));
        $xml = json_encode($xml);
        $xml = json_decode($xml, true);
        if ($xml['message']['status'] == 0 && $xml['policeCheckInfos']['policeCheckInfo']['message']['status'] == 0)
        {
            if ($xml['policeCheckInfos']['policeCheckInfo']['compStatus'] == 3)
            {
                return true;
            }
        }
        return false;
    }

    /**
     * 取得数据
     * @param string $type 查询类型
     * @param string $param 查询参数
     * @return string
     */
    function getData($type, $param) {
        $DES = new DES ( $this->Key, $this->iv );
        try {
            $soap = new SoapClient ( $this->wsdlURL );
        } catch ( Exception $e ) {
            return "Linkerror";
        }
        //var_dump ( $soap->__getTypes () );
        //@todo 加密数据
        $partner = $DES->encrypt ( $this->partner );
        $partnerPW = $DES->encrypt ( $this->partnerPW );
        $type = $DES->encrypt ( $type );
        //先将中文转码
//        $param = mb_convert_encoding ( $param, "GBK", "UTF-8" );
//        $param = mb_convert_encoding ( $param, "GBK" );
        $param = $DES->encrypt ( $param );
        $params=array("userName_"=>$partner,"password_"=>$partnerPW, "type_" => $type, "param_" => $param );
        //请求查询
        $data = $soap->querySingle ( $params );
        //@todo 解密数据
        $resultXML = $DES->decrypt ( $data->querySingleReturn );
        $resultXML = mb_convert_encoding ( $resultXML, "UTF-8", "GBK" );
        return $resultXML;
    }

    /**
     * 格式化参数
     * @param array $params 参数数组
     * @return string
     */
    function formatParam($queryType, $params) {
        if (empty ( $this->supportClass [$queryType] )) {
            return - 1;
        }
        $keys = array ();
        $values = array ();
        foreach ( $params as $key => $value ) {
            $keys [] = $key;
            $values [] = strtoupper ( $value );
        }
        $param = str_replace($keys,$values,$this->supportClass[$queryType] );
        return $param;
    }

    /**
     * 取得生日(由身份证号)
     * @param int $id 身份证号
     * @return string
     */
    function getBirthDay($id) {
        switch (strlen ( $id )) {
            case 15 :
                $year = "19" . substr ( $id, 6, 2 );
                $month = substr ( $id, 8, 2 );
                $day = substr ( $id, 10, 2 );
                break;
            case 18 :
                $year = substr ( $id, 6, 4 );
                $month = substr ( $id, 10, 2 );
                $day = substr ( $id, 12, 2 );
                break;
        }
        $birthday = array ('year' => $year, 'month' => $month, 'day' =>$day );
        return $birthday;
    }

    /**
     * 取得性别(由身份证号)--可能不准
     * @param int $id 身份证号
     * @return string
     */
    function getSex($id) {
        switch (strlen ( $id )) {
            case 15 :
                $sexCode = substr ( $id, 14, 1 );
                break;
            case 18 :
                $sexCode = substr ( $id, 16, 1 );
                break;
        }
        if ($sexCode % 2) {
            return "男";
        } else {
            return "女";
        }
    }

    /**
     * 格式化数据
     * @param string $type
     * @param srring $data
     * @return array
     */
    function formatData($type, $data) {
        switch ($type) {
            case "1A020201" :
                $detailInfo=$data['policeCheckInfos']['policeCheckInfo'];
                $birthDay=$this->getBirthDay($detailInfo['identitycard'] );
                $sex = $this->getSex ( $detailInfo ['identitycard'] );
                $info = array (
                    'name' => $detailInfo ['name'],
                    'identitycard' => $detailInfo ['identitycard'],
                    'sex' => $sex,
                    'compStatus' => $detailInfo ['compStatus'],
                    'compResult' => $detailInfo ['compResult'],
                    'policeadd' => $detailInfo ['policeadd'],
                    //'checkPhoto' => $detailInfo ['checkPhoto'],
                    'birthDay' => $birthDay,
                    'idcOriCt2' => $detailInfo ['idcOriCt2'],
                    'resultStatus' => $detailInfo ['compStatus'] );
                break;
            default :
                $info = array (false );
                break;
        }
        return $info;
    }
}