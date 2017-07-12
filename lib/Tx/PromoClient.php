<?php

namespace Tx;

use GuzzleHttp\Client as HttpClient;

class PromoClient
{
    private $httpClient;

    public function __construct($api_url)
    {
        $this->apiUrl = $api_url;
    }

    /**
     * 获得一个HTTP客户端实例.
     *
     * @return \GuzzleHttp\Client
     */
    protected function getHttpClient()
    {
        if (null === $this->httpClient) {
            $this->httpClient = new HttpClient([
                'allow_redirects' => false,
                'connect_timeout' => 5,
                'timeout' => 5,
            ]);
        }

        return $this->httpClient;
    }

    public function send($data)
    {
        $url = !empty($data) ? $this->apiUrl.'?'.http_build_query($data) : $this->apiUrl;
        return $this->getHttpClient()->request('GET', $url);
    }
}
