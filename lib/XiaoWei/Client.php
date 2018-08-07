<?php

namespace XiaoWei;

use GuzzleHttp\Client as HttpClient;
use yii\base\InvalidParamException;

class Client
{
    private $apiUrl; //接口请求地址
    private $signKey; //签名秘钥
    private $signType = 'md5'; //签名方式

    public function __construct($apiUrl, $signKey)
    {
        $this->apiUrl = $apiUrl;
        $this->signKey = $signKey;
    }

    /**
     * post请求
     *
     * @param string $uri
     * @param array $data
     *
     * @return array
     */
    public function doRequest($uri, array $data)
    {
        $client = $this->getHttpClient();
        if (isset($data['sign'])) {
            unset($data['sign']);
        }
        $data['sign'] = $this->sign($data);
        $respData = $client->request('POST', $uri, [
            'form_params' => $data,
        ]);
        $content = $respData->getBody()->getContents();

        return json_decode($content, true);
    }

    protected function sign($params, $isReturnString = false)
    {
        $str = '';
        //添加上时间校验
        $params['signDate'] = date('Y-m-d');
        ksort($params, SORT_STRING);
        foreach ($params as $key => $one) {
            if (in_array($key, ['sign'], true)) {
                continue;
            } elseif (is_array($one)) {
                $str .= $this->sign($one, true);
            } else {
                $str .= urldecode($one);
            }
        }
        if ($isReturnString) {
            return $str;
        }
        $str .= $this->signKey;
        $functionName = strtolower($this->signType);
        $resultStr = $functionName($str);

        return $resultStr;
    }

    protected function verifySign(array $params)
    {
        if (!isset($params['sign'])) {
            throw new InvalidParamException();
        }
        $sign = $params['sign'];
        unset($params['sign']);

        return $sign === $this->sign($params);
    }

    private function getHttpClient()
    {
        return new HttpClient([
            'base_uri' => $this->apiUrl,
            'allow_redirects' => false,
            'connect_timeout' => 30,
            'timeout' => 30,
        ]);
    }
}
