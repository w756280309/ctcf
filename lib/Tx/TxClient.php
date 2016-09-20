<?php

namespace Tx;

use GuzzleHttp\Client;

class TxClient
{
    private $baseUrl;//Tx系统域名
    private $client;

    public function __construct()
    {
        $this->baseUrl = rtrim(\Yii::$app->params['clientOption']['host']['tx'], '/') . '/';
        $this->client = new Client(['base_uri' => $this->baseUrl]);
    }

    /**
     * @param string $path 请求地址如"Assets/detail"
     * @param string $params 请求参数 如 ['id' => 1042]
     * @param string|array|mixed $exceptionHandler 异常处理的回调函数
     * @param array $headerOptions 请求头
     *
     * @return mixed|null|string
     * 调用实例:
     * $txClient = Yii::$container->get('txClient');
     * $res = $txClient->get('assets/detail', ['id' => 10420], function(\Exception $ex){
     *      //自定义异常处理机制
     *      echo $ex->getMessage();
     * });
     */
    public function get($path = '', $params = '', $exceptionHandler = '', $headerOptions = [])
    {
        $client = $this->client;
        $path = ltrim($path, '/');
        $path = ltrim($path, 'Api/');
        $path = ltrim($path, '/');
        $path = 'Api/' . $path;
        if (!is_string($params)) {
            $params = http_build_query($params);
        }
        try {
            $res = $client->request('GET', $path . '?' . $params, [
                'headers' => empty($headerOptions) ? $headerOptions = [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ] : $headerOptions,
            ]);

            return json_decode($res->getBody(), true);
        } catch (\Exception $ex) {
            if (!empty($exceptionHandler)) {
                return call_user_func($exceptionHandler, $ex);
            } else {
                if (404 === $ex->getCode()) {
                    return null;
                } else {
                    throw $ex;
                }
            }
        }
    }

    public function post($path, $params = [], $exceptionHandler = '', $headerOptions = [])
    {
        $client = $this->client;
        $path = ltrim($path, '/');
        $path = ltrim($path, 'Api/');
        $path = ltrim($path, '/');
        $path = 'Api/' . $path;
        try {
            $res = $client->request('POST', $path, [
                'headers' => empty($headerOptions) ? [
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                ] : $headerOptions,
                'json' => $params,
            ]);

            return json_decode($res->getBody(), true);
        } catch (\Exception $ex) {
            if (!empty($exceptionHandler)) {
                return call_user_func($exceptionHandler, $ex);
            } else {
                throw $ex;
            }
        }
    }
}
