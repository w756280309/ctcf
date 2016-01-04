<?php

namespace common\lib\cfca;

use PayGate\Cfca\Message\RequestInterface;
use PayGate\Cfca\Message\Response;
use SimpleXMLElement;
use Yii;

class Cfca
{
    private $apiUrl;
    private $clientKeyPath;
    private $clientKeyExportPass;
    private $cfcaCertPath;

    public function __construct()
    {
        $this->apiUrl = Yii::$app->params['cfca']['apiUrl'];
        $this->clientKeyPath = Yii::$app->params['cfca']['clientKeyPath'];
        $this->clientKeyExportPass = Yii::$app->params['cfca']['clientKeyExportPass'];
        $this->cfcaCertPath = Yii::$app->params['cfca']['cfcaCertPath'];
    }

    public function request(RequestInterface $message)
    {
        $xml = $message->getXml();

        $pkey = $this->getClientCerts()['pkey'];
        if (!openssl_sign($xml, $bSign, $pkey, OPENSSL_ALGO_SHA1)) {
            throw new \Exception('Unknown error.');
        }

        $resp = $this->post($this->apiUrl, [
            'message' => base64_encode($xml),
            'signature' => bin2hex($bSign),
        ]);

        if ($message->getTxCode() == "1311") {
            return $resp;
        }

        return $this->verifyResponse($resp);
    }

    /**
     * 读取文件的内容
     *
     * @param string $path 文件路径
     *
     * @return string 文件内容
     *
     * @throws \RuntimeException 路径不存在或无法访问
     */
    private function readFile($path)
    {
        if (!file_exists($path)) {
            throw new \RuntimeException(sprintf('File not found at "%s".', $path));
        }

        $content = file_get_contents($path);
        if (false === $content) {
            throw new \RuntimeException(sprintf('Can not open file found at %s.', $path));
        }

        return $content;
    }

    /**
     * 提取客户证书（p12）
     *
     * @return array pkey对应密钥，cert对应公钥
     *
     * @throws \RuntimeException 文件内容或密码无效导致提取失败
     */
    private function getClientCerts()
    {
        $p12 = $this->readFile($this->clientKeyPath);
        if (!openssl_pkcs12_read($p12, $certs, $this->clientKeyExportPass)) {
            throw new \RuntimeException('Invalid file content or wrong export password.');
        }

        return $certs;
    }

    private function post($url, array $data = [])
    {
        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERAGENT => 'institution',
            CURLOPT_CONNECTTIMEOUT => 120,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => false,
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, $options);

        $content = curl_exec($ch);

        curl_close($ch);

        return $content;
    }

    private function verifyResponse($content)
    {
        list($encoded, $sign) = explode(',', $content);

        $decoded = base64_decode($encoded, true);
        if (false === $decoded) {
            throw new \RuntimeException('Failed base64 decoding response message.');
        }

        $bSign = hex2bin($sign);

        $cert = $this->readFile($this->cfcaCertPath);
        if (!openssl_verify($decoded, $bSign, $cert)) {
            throw new \RuntimeException('Corrupted sign.');
        }

        return new Response($decoded);
    }
}
