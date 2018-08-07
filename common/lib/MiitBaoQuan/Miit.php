<?php

namespace common\lib\MiitBaoQuan;

use common\models\order\OnlineOrder;
use common\models\product\OnlineProduct;
use common\models\user\User;
use Yii;
use common\helpers\HttpHelper;
use common\lib\rsa\RSA;
use common\utils\SecurityUtils;
use common\models\order\EbaoQuan;
//use Wcg\Security\Aes;
use common\lib\aes\AES;
use yii\db\Exception;

class Miit
{
    private $idCode = '';
    private $ticket = '';
    private $MiitGetTicketUrl = ''; //获取票据的接口地址
    private $MiitContractUrl = '';  //合同实时上传的接口地址
    private $MiitGetHetongUrl = ''; //查看合同的接口地址
    private $WdjfPrivateKey = '';   //自己的私钥

    private $MiitPublicKey = '';    //工信部保全的公钥
    private $AesKey = '';           //
    private $AesKeyLength = MCRYPT_RIJNDAEL_128;

    private $config = [];           //关于soap的配置

    const BROWWER = 2;              //借款人
    const LENDER = 1;               //出借人

    public function __construct()
    {
        $this->idCode = Yii::$app->params['miit']['idcode'];

        $this->MiitPublicKey = Yii::$app->params['miit']['MiitPublicKey'];
        $this->MiitGetTicketUrl = Yii::$app->params['miit']['MiitGetTicketUrl'];
        $this->MiitContractUrl = Yii::$app->params['miit']['MiitContractUrl'];
        $this->MiitGetHetongUrl = Yii::$app->params['miit']['MiitGetHetongUrl'];
        //私钥
        $this->WdjfPrivateKey = Yii::$app->params['miit']['wdjf_private_key'];

        $this->AesKey = Yii::$app->params['miit']['aesKey'];
        $redis = Yii::$app->redis;
        $this->ticket = $redis->get('miit_baoquan_ticket') ?: $this->getTicket();

        $this->config = $this->getConfig();
    }

    /**
     * @ TODO 获取票据信息
     * 获取成功后保存到session,周期四个小时
     */
    public function getTicket()
    {
        $postData['idCode'] = $this->idCode;
        $postData['clientIP'] = HttpHelper::getClientIP();
        $postData['random'] = mt_rand(1000000, 9999999);
        $postData['summary'] = md5($postData['idCode'] . $postData['clientIP'] . $postData['random']);
        $postData['signValue'] = RSA::sign($postData['summary'], $this->WdjfPrivateKey, OPENSSL_ALGO_MD5);
        $jsonData = json_encode($postData);
        $encrypt = RSA::rsaEncrypt($jsonData, $this->MiitPublicKey);
        $result = self::doRequest($this->MiitGetTicketUrl, ['message' => $encrypt]);
        //处理结果
        $result = json_decode($result, true);
        if ($result && $result['resultCode'] == 1) {
            $redis = Yii::$app->redis;
            $redis->setex('miit_baoquan_ticket', 4 * 3600, $result['ticket']);
            return $result['ticket'];
        }
        return '';
    }
    /**
     * @TODO 合同实时上传接口
     * @param $filePath  合同模板路径
     * @param $order_id  订单id
     * @param $type 订单类型:0,订单;
     */
    public function hetongUpload($filePath, User $user, $ecode, $ename, $signTime, $type, $itemType, $order_id)
    {
        try {
            if (!file_exists($filePath)) {
                throw new \Exception('合同文件不存在');
            }
            //获取合同的数据
            $contractData = self::readFile($filePath);
            $userinfo = [
                'idcode' => SecurityUtils::decrypt($user->safeIdCard),
                'name' => $user->real_name,
                'phone' => SecurityUtils::decrypt($user->safeMobile),
                'userType' => self::LENDER,
            ];
            //拼接参数信息
            $info['users'] = $userinfo;
            $info['ecode'] = $ecode;
            $info['ename'] = $ename;
            $info['effectTime'] = str_pad($signTime, 13, '0');
            $info['signTime'] = str_pad($signTime, 13, '0');
            $info['dueTime'] = str_pad($signTime + 5 * 365 * 24 * 3600, 13, '0');  //合同到期时间还未定(5年)
            $info['contentMd5'] = md5($contractData);
            $info['ticket'] = $this->ticket;

            $soapClient = new \SoapClient($this->MiitContractUrl, $this->config);
            $soapNameSpace = self::getWdslNameSpace();

            //设置soapHeader信息
            $tmpObj = new \StdClass;
            $tmpObj->ticket = $this->ticket;
            $tmpObj->aeskey = RSA::rsaEncrypt($this->AesKey, $this->MiitPublicKey, OPENSSL_ALGO_MD5);

            $soapHeader = new \SoapHeader($soapNameSpace, 'authhead', $tmpObj, false);
            $soapClient->__setSoapHeaders($soapHeader);
            $postData = Aes::encrypt(json_encode($info, JSON_UNESCAPED_UNICODE), $this->AesKey, null, $this->AesKeyLength, MCRYPT_MODE_CBC, true);
            $contractData = Aes::encrypt($contractData, $this->AesKey, null, $this->AesKeyLength, MCRYPT_MODE_CBC, true);

            $res = $soapClient->uploadContract(['contractDesc' => $postData, 'contractData' => $contractData]);
            $result = self::returnData($res->return);
            if ($result['data']['status'] != 1) {
                throw new Exception('合同上传失败，原因：' . json_encode($result));
            }
            //实例化 EbaoQuan(调试通过)
            $model = new EbaoQuan();
            $model->type = $type;
            $model->title = $ename;
            $model->itemId = $order_id;
            $model->itemType = $itemType;
            $model->uid = $user->id;
            $model->success = $result['data']['status'] == 1 ? 1 : 0;
            $model->errMessage = $result['data']['status'] == 1 ? '成功' : json_encode($result);//失败的话 就返回的信息保存
            $model->baoId = $ecode;
            $model->preservationTime = str_pad($signTime, 13, '0');
            return $model->save();
        } catch (\Exception $e) {
            \Yii::trace('保权失败,订单ID:'.$order_id.';失败信息'.$e->getMessage());
            throw $e;
        }
    }
    /**
     * @TODO 查看合同的接口
     */
    public function viewHetong($itemId, $type = EbaoQuan::TYPE_M_LOAN, $itemType = EbaoQuan::ITEM_TYPE_LOAN_ORDER)
    {
        $EbaoQuan = EbaoQuan::findOne(['itemId' => $itemId, 'success' => 1, 'type' => $type, 'itemType' => $itemType]);
        if (is_null($EbaoQuan)) {
            return false;
            throw new \Exception('合同未上传');
        }
        $user = User::findOne(['id' => $EbaoQuan->uid]);
        $data = [
            'idcode' => SecurityUtils::decrypt($user->safeIdCard),
            'ecode' => $EbaoQuan->baoId,
            'phone' => SecurityUtils::decrypt($user->safeMobile),
            'token' => $this->ticket,
        ];
        $res = self::doRequest($this->MiitGetHetongUrl, $data);
        $res = json_decode($res);
        if ($res->success == 1) {
            return $res->info->short_url;
        } else {
            return null;
        }
    }

    /**
     * @TODO 读取文本数据
     * @param $filePath 合同模板路径
     */
    private function readFile($filePath)
    {
        if (!is_file($filePath)) {
            return null;
        }
        $fp = fopen($filePath, 'rb');
        $content = fread($fp, filesize($filePath));
        fclose($fp);
        return $content;
    }
    /**
     * 生成soap的命名空间
     */
    private function getWdslNameSpace()
    {
        $wdslContent = file_get_contents($this->MiitContractUrl);
        $res = preg_match('/targetNamespace="(.*)"/', $wdslContent, $data);
        if (!$res) {
            throw new \Exception('未匹配获取到wdsl的命名空间');
        }
        return $data[1];
    }

    /**
     * @TODO 获取一个配置文件信息
     */
    private function getConfig()
    {
        return [
            'soap_version' => '1.1',
            'encoding' => 'UTF-8',
            'cache_wsdl' => 0,
            'compression' => true
        ];
    }

    /**
     * @TODO 返回参数处理
     */
    private function returnData($json = null)
    {
        if (is_null($json)) return null;
        $data = json_decode($json, true);
        if (empty($data)) return null;
        $aesKey = RSA::rsaDecrypt($data['key'], $this->WdjfPrivateKey);
        if (empty($aesKey)) {
            $aesKey = $data['key'];
        }
        $result = Aes::decrypt($data['data'], $aesKey, null, $this->AesKeyLength);
        $result = substr($result, 0, (strrpos($result, '}') + 1));
        return [
            'data' => json_decode($result, true),
            'key' => $aesKey
        ];
    }
    //用于发送POST请求
    private static function doRequest($requestUrl, $postData = array()) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $requestUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //禁止直接显示获取的内容 重要
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //不验证证书下同
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); //
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}