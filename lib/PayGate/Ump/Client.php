<?php

namespace PayGate\Ump;

use Crypto\CryptoUtils;
use GuzzleHttp\Client as HttpClient;
use Psr\Http\Message\ResponseInterface as Psr7ResponseInterface;

/**
 * 联动优势API调用.
 */
class Client
{
    const ENCRYPT_ENCODING = 'GB18030';

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
     * 4.2.1 以平台的用户标识及身份三要素在联动开户.
     *
     * @param string $appUserId
     * @param string $idName
     * @param int    $idType
     * @param string $idNo
     * @param string $mobile
     *
     * @return Response
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

        return $this->doRequest($data);
    }

    /**
     * 4.5.2 查询用户的账号和协议签署情况
     *
     * @param string $epayUserId 在联动一侧的用户ID
     *
     * @return Response
     */
    public function getUserInfo($epayUserId)
    {
        $data = [
            'service' => 'user_search',
            'user_id' => $epayUserId,
            'is_find_account' => '01',
            'is_select_agreement' => '1',
        ];

        return $this->doRequest($data);
    }

    /**
     * 4.5.5 商户信息查询
     *
     * @param string $managedMerchantId 在联动一侧的商户号
     *
     * @return Response
     */
    public function getMerchantInfo($managedMerchantId)
    {
        $data = [
            'service' => 'ptp_mer_query',
            'query_mer_id' => $managedMerchantId,
            'account_type' => '01',
        ];

        return $this->doRequest($data);
    }

    /**
     * 4.3.1 标的类接口
     * 发标(商户->平台)
     * @param string $project_amount 单位分,最小1,最大9999999999999
     * @param string $loan_user_id 会去联动一侧判断用户是否存在[测试上投资用户可以用来融资]
     * @param string $project_expire_date 只做格式校验。没有对时间做其他限制
     * @return Response
     */
    public function registerLoan(
        $project_id, $project_name, $project_amount, $loan_user_id,
        $project_expire_date
    )
    {
        $data = [
            'service' => 'mer_bind_project',
            'project_id' => $project_id,
            'project_name' => $project_name,
            'project_amount' => $project_amount,
            'project_expire_date' => $project_expire_date,
            'loan_user_id' => $loan_user_id,
        ];

        return $this->doRequest($data);
    }

    /**
     * 4.5.3 标的查询接口 查询标的账户状态及余额
     * @param type $project_id 商户端标的号
     * @return type
     */
    public function getLoan($project_id) {
        $data = [
            'service' => 'project_account_search',
            'project_id' => $project_id,
        ];
        return $this->doRequest($data);
    }

    /**
     * 获得一个HTTP客户端实例
     *
     * @return \GuzzleHttp\Client
     */
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

    /**
     * 以POST方式提交请求数据
     *
     * @return Response
     */
    protected function doRequest(array $data)
    {
        // 添加协议参数
        $data = array_merge($data, [
            'charset' => $this->charset,
            'mer_id' => $this->merchantId,
            'version' => $this->version,
        ]);

        // 签名
        $data['sign'] = $this->sign($data);
        $data['sign_type'] = $this->signType;

        $httpResponse = $this->getHttpClient()->request('POST', null, [
            'form_params' => $data,
        ]);

        return $this->processHttpResponse($httpResponse);
    }

    /**
     * 处理联动接口的返回
     *
     * @param \Psr\Http\Message\ResponseInterface $response PSR-7 HTTP响应对象
     *
     * @return Response
     */
    protected function processHttpResponse(Psr7ResponseInterface $response)
    {
        $html = trim($response->getBody()->getContents());

        $doc = new \DOMDocument();
        $doc->validateOnParse = true;

        // 避免乱码
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', $this->charset);

        // 因联动构造HTML不符合规范，关闭错误提醒
        libxml_use_internal_errors(true);
        $doc->loadHTML($html);
        libxml_use_internal_errors(false);

        $xpath = new \DOMXpath($doc);
        $nodes = $xpath->query('//meta');
        if (0 === $nodes->length) {
            throw new \Exception('Meta element not found.');
        } elseif ($nodes->length > 1) {
            // 因行为未定义，遇到返回多个meta标签的情况直接报错
            throw new \Exception('Handling of multiple meta elements not implemented.');
        }

        $content = $nodes->item(0)->getAttribute('content');
        $segs = explode('&', $content);
        $pairs = [];
        foreach ($segs as $seg) {
            list($key, $val) = explode('=', $seg, 2);
            $pairs[$key] = $val;
        }

        if (!$this->verifySign($pairs)) {
            throw new \Exception('Sign invalid.');
        }

        return new Response($pairs);
    }

    /**
     * 为签名把数组连接为k1=v1&k2=v2...形式的字符串
     *
     * @param array $data
     *
     * @return string
     */
    protected function concatForSigning(array $data)
    {
        ksort($data);

        $concated = '';
        for ($i = 1, $l = count($data); $i <= $l; ++$i) {
            $concated .= sprintf('%s=%s', key($data), current($data));
            if ($i < $l) {
                $concated .= '&';
            }

            next($data);
        }

        return $concated;
    }

    /**
     * 校验包含签名的数组
     *
     * @param array $data 待校验的数组
     *
     * @return bool
     */
    protected function verifySign(array $data)
    {
        if (!isset($data['sign'])) {
            throw new \Exception('Sign missing.');
        }

        $sign = base64_decode($data['sign']);
        foreach (['sign', 'sign_type'] as $ignore) {
            unset($data[$ignore]);
        }

        $content = mb_convert_encoding(
            $this->concatForSigning($data),
            self::ENCRYPT_ENCODING,
            $this->charset
        );

        return CryptoUtils::verifySign($content, $sign, $this->umpCertPath);
    }

    /**
     * 签名
     *
     * @param array $data 待签名的数组
     *
     * @return string
     */
    protected function sign(array $data)
    {
        return base64_encode(CryptoUtils::sign($this->concatForSigning($data), $this->clientKeyPath));
    }

    /**
     * 加密
     *
     * @param string $data 待加密的字符串
     *
     * @return string
     */
    protected function encrypt($data)
    {
        $encoding = mb_detect_encoding($data);
        if (self::ENCRYPT_ENCODING !== $encoding) {
            $data = mb_convert_encoding($data, self::ENCRYPT_ENCODING, $encoding);
        }

        return base64_encode(CryptoUtils::encrypt($data, $this->umpCertPath));
    }
}
