<?php

namespace WeiXin;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\RequestException;

class Client
{
    protected $httpClient;
    protected $clientId;
    protected $clientSecret;

    public function __construct($clientId, $clientSecret)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    // 生成申请身份认证的链接
    public function getAuthorizationUrl($callbackUrl, $scope = 'snsapi_base', $state = null)
    {
        $query = http_build_query([
            'appid' => $this->clientId,
            'redirect_uri' => $callbackUrl,
            'response_type' => 'code',
            'scope' => $scope,
            'state' => $state,
        ]);

        return 'https://open.weixin.qq.com/connect/oauth2/authorize?'.$query.'#wechat_redirect';
    }

    // 获取授权
    public function getGrant($code)
    {
        try {
            $resp = $this->getHttpClient()->post('sns/oauth2/access_token', [
                'form_params' => [
                    'appid' => $this->clientId,
                    'secret' => $this->clientSecret,
                    'code' => $code,
                    'grant_type' => 'authorization_code',
                ],
            ]);
        } catch (RequestException $ex) {
            throw new \Exception('oauth_get_grant_failed', 0, $ex);
        }

        $data = json_decode($resp->getBody()->getContents(), true);
        // 可以检查一下data是不是数组，是不是包含该有的键
        if (isset($data['errcode'])) {
            throw new \Exception($data['errmsg']);
        }

        return [
            'granted_time' => time(),
            'access_token' => $data['access_token'],
            'refresh_token' => $data['refresh_token'],
            'expires_in_seconds' => $data['expires_in'],
            'resource_owner_id' => $data['openid'],
            'scope' => $data['scope'],
        ];
    }

    // 获取微信帐号信息（应不需要）
    public function getResourceOwnerInfo($grant)
    {
        $httpClient = $this->getHttpClient();

        try {
            $resp = $httpClient->get('sns/userinfo', [
                'query' => [
                    'access_token' => $grant['access_token'],
                    'openid' => $grant['resource_owner_id'],
                    'lang' => 'zh_CN',
                ],
            ]);
        } catch (RequestException $ex) {
            throw new \Exception('oauth_api_error', 0, $ex);
        }

        $data = json_decode($resp->getBody()->getContents(), true);
        // 可以检查一下data是不是数组，是不是包含该有的键

        if ($data['ret'] < 0) {
            throw new \Exception($data['msg']);
        }

        return [
            'nickName' => $data['nickname'],
            'avatarUrl' => $data['headimgurl'],
        ];
    }

    private function getHttpClient()
    {
        if (null === $this->httpClient) {
            $this->httpClient = new HttpClient([
                'base_uri' => 'https://api.weixin.qq.com',
                'allow_redirects' => false,
                'connect_timeout' => 30,
                'timeout' => 30,
                //'http_errors' => false, //如果取消注释，则HTTP协议错误不抛异常
            ]);
        }

        return $this->httpClient;
    }
}
