<?php

namespace Lhjx\Identity\DataCenter;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Cookie\CookieJar;

class Client
{
    private $httpClient;
    protected $baseUrl;
    protected $domain;
    protected $email;
    protected $password;

    public function __construct($baseUrl, $domain, $email, $password)
    {
        $this->baseUrl = $baseUrl;
        $this->domain = $domain;
        $this->email = $email;
        $this->password = $password;
    }

    //获取csrf令牌
    private function getCsrf()
    {
        $this->httpClient = new HttpClient(['cookies' => true, 'timeout' => 10]);
        $service = Credit::CREDIT_CSRFAUTH;
        $requestUrl = $this->getRequestUrl($service);
        $response = $this->httpClient->get($requestUrl);
        $str = $response->getHeader('Set-Cookie')[0];
        $str = explode(';', $str)[0];
        $session_id = explode('=', $str)[1];

        $resContent = $response
            ->getBody()
            ->getContents();
        return ['response' => $resContent, 'session_id' => $session_id];
    }

    //登录接口
    private function loginApi()
    {
        $csrf = $this->getCsrf();
        $response = $csrf['response'];
        $session_id = $csrf['session_id'];
        $csrfRes = json_decode($response, true);
        $headerName = $csrfRes['data']['headerName'];
        $csrfToken = $csrfRes['data']['token'];
        $cookies = [
            'SESSION' => $session_id,
        ];
        $service = Credit::CREDI_LOGINAUTH;
        $requestUrl = $this->getRequestUrl($service);
        $domain = $this->domain;
        $cookieJar = CookieJar::fromArray($cookies, $domain);

        $requestData = [
            'body' => json_encode([
                'email' => $this->email,
                'password' => $this->password,
            ]),
            'headers' => [
                $headerName => $csrfToken,
                'content-type' => 'application/json;charset=UTF-8',
            ],
            'cookies' => $cookieJar,
        ];

        $this->httpClient->request('post', $requestUrl, $requestData)
            ->getBody()
            ->getContents();

        return ['headerName' => $headerName, 'token' => $csrfToken];
    }

    /**
     * 查询征信的接口
     * @param $data $data['name'] 用户名  $data['idCardNum']: 用户身份证号 $data[tel]:用户手机号 $data['bankCardNum']: 用户银行卡号
     * @return mixed 异步码
     */
    public function getApiService($data)
    {
        $name = isset($data['name']) ? $data['name'] : '';
        $idCardNum = isset($data['idCardNum']) ? $data['idCardNum'] : '';
        $tel = isset($data['tel']) ? $data['tel'] : '';
        $bankCardNum = isset($data['bankCardNum']) ? $data['bankCardNum'] : '';
        $tokenInfo = $this->loginApi();
        $csrfToken = $tokenInfo['token'];
        $headerName = $tokenInfo['headerName'];
        $service = Credit::CREDIT_APISERVICEAUTH;
        $requestUrl = $this->getRequestUrl($service);
        $requestData = [
            'body' => json_encode([
                'dataItems' => $data['apiName'],
                'name' => $name,
                'idCardNum' => $idCardNum,
                'tel' => $tel,
                'bankCardNum' => $bankCardNum,
            ]),
            'headers' => [
                $headerName => $csrfToken,
                'Content-Type' => 'application/json;charset=UTF-8',
            ]
        ];
        $response = $this->httpClient
            ->request('post', $requestUrl, $requestData)
            ->getBody()
            ->getContents();
        $result = $this->getResponseResult($response, $tokenInfo);

        return $result;
    }

    /**
     * 根据异步码获取查询结果
     * @param $response  异步码信息
     * @param $tokenInfo 用户token和headerName
     * @return mixed 查询结果
     */
    private function getResponseResult($response, $tokenInfo)
    {
        $response = json_decode($response, true);
        if (isset($response)) {
            $body = $response['data']['resultArray'][0];
            if (isset($body['asyncResponse'])) {
                $ids = [$body['asyncResponse']];
                $csrfToken = $tokenInfo['token'];
                $headerName = $tokenInfo['headerName'];
                $service = Credit::CREDIT_APIRESPONSERESULT;
                $requestUrl = $this->getRequestUrl($service);
                $requestData = [
                    'body' => json_encode([
                        'ids' =>$ids
                    ]),
                    'headers' => [
                        $headerName => $csrfToken,
                        'Content-Type' => 'application/json;charset=UTF-8',
                    ]
                ];
                for ($i=0; $i < 5; $i++) {
                    $responseResult = $this->httpClient
                        ->request('post', $requestUrl, $requestData)
                        ->getBody()
                        ->getContents();
                    $responseResult = json_decode($responseResult, true);
                    if (isset($responseResult)) {
                        $bodyResult = $responseResult['data'][0];
                        if (isset($bodyResult['response'])) {
                            return $bodyResult['response'];
                        }
                    }
                    usleep(1500000);
                }
                $timeout['success'] = 'false';
                $timeout['认证情况'] = '超时';
                return $timeout;
            } elseif (isset($body['response'])) {
                return $body['response'];
            }
        }
    }

    /**
     * 根据获取的关键词获取接口地址
     * @param $service  关键词名称
     * @return string   接口地址
     */
    private function getRequestUrl($service)
    {
        $baseUrl = $this->baseUrl;
        switch ($service) {
            case Credit::CREDIT_CSRFAUTH:
                $result = $baseUrl . 'auth/csrf';
                break;
            case Credit::CREDI_LOGINAUTH:
                $result = $baseUrl . 'auth/ajaxLogin';
                break;
            case Credit::CREDIT_USERINFOAUTH:
                $result = $baseUrl . 'auth/getUserInfo';
                break;
            case Credit::CREDIT_LISTAPISAUTH:
                $result = $baseUrl . 'thirdPart/listAPIs';
                break;
            case Credit::CREDIT_APISERVICEAUTH:
                $result = $baseUrl . 'thirdPart2/asyncSubmit';//新版接口
                break;
            case Credit::CREDIT_APIRESPONSERESULT:
                $result = $baseUrl . 'thirdPart2/getResponseArrByIds';
                break;
            default:
                $result = '';
                break;
        }

        return $result;
    }
}
