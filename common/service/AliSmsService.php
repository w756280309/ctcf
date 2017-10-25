<?php

namespace common\service;

use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest;
use Aliyun\Core\Config;
use Aliyun\Core\DefaultAcsClient;
use Aliyun\Core\Profile\DefaultProfile;
use Yii;

class AliSmsService
{
    protected $accessKeyId;
    protected $accessKeySecret;

    protected $acsClient;
    protected $response;


    public function __construct($accessKeyId, $accessKeySecret)
    {
        Config::load();
        $this->accessKeyId = $accessKeyId;
        $this->accessKeySecret = $accessKeySecret;
        // 短信API产品名
        $product = "Dysmsapi";
        // 短信API产品域名
        $domain = "dysmsapi.aliyuncs.com";
        // 暂时不支持多Region
        $region = "cn-hangzhou";
        // 服务结点
        $endPointName = "cn-hangzhou";

        //初始化访问的acsClient
        $profile = DefaultProfile::getProfile($region, $this->accessKeyId, $this->accessKeySecret);
        DefaultProfile::addEndpoint($endPointName, $region, $product, $domain);
        $this->acsClient = new DefaultAcsClient($profile);
    }

    /**
     * 发送短信模板短信
     *
     * @return bool
     */
    public function send($smsOrderId, $mobile, $signName, $templateId, $templateParams)
    {
        $black_mobile = explode(',', Yii::$app->params['NoSendSms']);
        if (in_array($mobile, $black_mobile)) {
            Yii::info('黑名单用户：' . $mobile);
            return true;
        }
        $request = new SendSmsRequest();
        $request->setOutId($smsOrderId);
        $request->setSignName($signName);
        $request->setTemplateCode($templateId);
        $request->setTemplateParam(json_encode($templateParams));
        $request->setPhoneNumbers($mobile);
        $request->setAcceptFormat('JSON');
        $acsResponse = $this->acsClient->getAcsResponse($request);
        $this->response = $acsResponse;
        \Yii::info("[sms_send][ali_sms] sn:{$smsOrderId};response:" . json_encode($acsResponse));
        return $this->isSuccess();
    }

    public function isSuccess()
    {
        return !is_null($this->response) && isset($this->response->Code) && $this->response->Code === 'OK';
    }
}
