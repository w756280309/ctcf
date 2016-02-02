<?php

namespace PayGate\Ump;

use Crypto\CryptoUtils;
use GuzzleHttp\Client as HttpClient;

/**
 * 联动优势API调用
 */
class Client
{
    /**
     * @var string 商户ID
     */
    private $merchantId;

    /**
     * @var string 商户->联动请求签名私钥文件路径
     */
    private $clientKeyPath;

    /**
     * @var string 数据加密公钥文件路径
     */
    private $umpCertPath;

    /**
     * @var \GuzzleHttp\Client
     */
    private $httpClient;

    /**
     * @var string 签名算法
     */
    private $signType = 'RSA';

    /**
     * @var string 语言编码
     */
    private $charset = 'UTF-8';

    /**
     * @var string 联动API版本号
     */
    private $version = '1.0';

    public function __construct($merchantId, $clientKeyPath, $umpCertPath)
    {
        $this->merchantId = $merchantId;
        $this->clientKeyPath = $clientKeyPath;
        $this->umpCertPath = $umpCertPath;
    }

    /**
     * 4.2.1
     *
     * 根据在平台的用户标识及身份三要素在联动开户
     *
     * @param string $appUserId
     * @param string $idName
     * @param int $idType
     * @param string $idNo
     * @param string $mobile
     */
    public function register($appUserId, $idName, $idType, $idNo, $mobile)
    {
        $orderId = time();

        $data = [
            'service' => 'mer_register_person',
            'order_id' => $orderId,
            'mer_cust_id' => $appUserId,
            'mer_cust_name' => $this->encrypt($idName),
            'identity_type' => $idType,
            'identity_code' => $this->encrypt($idNo),
            'mobile_id' => $mobile,
        ];

        $this->doRequest($data);

        return $orderId;
    }

    public function getUserInfo($epayUserId)
    {
        $data = [
            'service' => 'user_search',
            'user_id' => $epayUserId,
            'is_find_account' => '01',
            'is_select_agreement' => '1',
        ];

        $this->doRequest($data);
    }

    protected function getHttpClient()
    {
        if (null === $this->httpClient) {
            $this->httpClient = new HttpClient([
                'base_uri' => 'http://114.113.159.203:9200/spay/pay/payservice.do',
                'allow_redirects' => false,
                'connect_timeout' => 30,
                'timeout' => 30,
            ]);
        }

        return $this->httpClient;
    }

    protected function doRequest(array $data)
    {
        // unset sign_type & sign?
        $data = array_merge($data, [
            'charset' => $this->charset,
            'mer_id' => $this->merchantId,
            'version' => $this->version,
        ]);

        ksort($data);

        $concated = '';
        for ($i = 0, $l = count($data); $i < $l; $i++) {
            $concated .= sprintf('%s=%s', key($data), current($data));
            if ($i < ($l - 1)) {
                $concated .= '&';
            }

            next($data);
        }

        $data['sign_type'] = $this->signType;
        $data['sign'] = base64_encode(CryptoUtils::sign($concated, $this->clientKeyPath));

        $response = $this->getHttpClient()->request('POST', null, [
            'form_params' => $data,
        ]);

        $respHtml = trim($response->getBody()->getContents());
        echo $respHtml;

        $charset = 'UTF-8';
        $doc = new \DOMDocument('1.0', $charset);
        $doc->validateOnParse = true;

        $respHtml = mb_convert_encoding($respHtml, 'HTML-ENTITIES', $charset);
        libxml_use_internal_errors(true);
        $doc->loadHTML($respHtml);
        libxml_use_internal_errors(false);

        $xpath = new \DOMXpath($doc);
        $nodes = $xpath->query('//meta');
        if (1 !== $nodes->length) {
            throw new \Exception();
        }
        echo $nodes->item(0)->getAttribute('content');

        return $response;
    }

    protected function encrypt($content)
    {
        $encoding = mb_detect_encoding($content);
        if ('GB18030' !== $encoding) {
            $content = mb_convert_encoding($content, 'GB18030', $encoding);
        }

        return base64_encode(CryptoUtils::encrypt($content, $this->umpCertPath));
    }
}
