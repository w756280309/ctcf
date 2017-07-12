<?php

namespace Tx;

use GuzzleHttp\Client as HttpClient;
use Psr\Http\Message\ResponseInterface as Psr7ResponseInterface;

class UmpClient
{
    const ENCRYPT_ENCODING = 'GB18030';

    private $apiUrl;

    /**
     * @var string 商户->联动请求签名私钥文件路径
     */
    private $merchantKeyFile;

    /**
     * @var string 商户ID
     */
    private $merchantId;

    /**
     * @var string 数据加密公钥文件路径
     */
    private $umpCertFile;

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

    public function __construct(array $options)
    {
        $this->apiUrl = $options['apiUrl'];
        $this->umpCertFile = $options['ump_cert'];
        $this->merchantId = $options['merchant_id'];
        $this->merchantKeyFile = $options['wdjf_key'];
    }

    /**
     * 获取对账单（暂限定为标的交易）.
     *
     * @param string $date YYYYMMDD
     *
     * @return string
     */
    public function getSettlement($date, $type = '03')
    {
        $data = [
            'service' => 'download_settle_file_p',
            'settle_date_p2p' => $date,
            'settle_type_p2p' => $type,
        ];

        return $this->doRequest($data);
    }

    /**
     * 4.3.5 标的转账[无密]
     *
     * 购买债权.
     *
     * @param array $param = [
     *                     'sn',       //交易流水号
     *                     'date',       //交易日期
     *                     'loanId',     //订单对应的标的ID
     *                     'fcUserId', //转出方在联动开立的账号
     *                     'amount',   //交易金额,以分为单位
     *                     ]
     */
    public function buyNote(array $param)
    {
        $data = [
            'service' => 'project_transfer_nopwd',
            'order_id' => $param['sn'],
            'mer_date' => date('Ymd', $param['date']),
            'project_id' => $param['loanId'],
            'serv_type' => '02',
            'trans_action' => '01',
            'partic_type' => '01',
            'partic_acc_type' => '01',
            'partic_user_id' => $param['fcUserId'],
            'amount' => $param['amount'],
        ];

        return $this->doRequest($data);
    }

    /**
     * 4.3.3 标的转账(商户->平台)
     *
     * 债权转让返款转让人.
     *
     * @param array $param = [
     *                     'sn',      //交易流水号
     *                     'date',      //交易日期
     *                     'loanId',    //订单对应的标的ID
     *                     'fcUserId',//转出方在联动开立的账号
     *                     'amount',  //交易金额
     *                     ]
     */
    public function noteFangkuan(array $param)
    {
        $data = [
            'service' => 'project_transfer',
            'order_id' => $param['sn'],
            'mer_date' => date('Ymd', $param['date']),
            'project_id' => $param['loanId'],
            'serv_type' => '56',
            'trans_action' => '02',
            'partic_type' => '01',
            'partic_acc_type' => '01',
            'partic_user_id' => $param['fcUserId'],
            'amount' => $param['amount'],
        ];

        return $this->doRequest($data);
    }

    /**
     * 4.3.5 标的转账[无密]
     *
     * 债券转让(收取手续费).
     *
     * @param array $param = [
     *                     'sn',      //交易流水号
     *                     'date',      //交易日期
     *                     'loanId',    //订单对应的标的ID
     *                     'amount',  //交易金额
     *                     ]
     */
    public function noteFee(array $param)
    {
        $data = [
            'service' => 'project_transfer',
            'order_id' => $param['sn'],
            'mer_date' => date('Ymd', $param['date']),
            'project_id' => $param['loanId'],
            'serv_type' => '52',
            'trans_action' => '02',
            'partic_type' => '03',
            'partic_acc_type' => '02',
            'partic_user_id' => $this->merchantId,
            'amount' => $param['amount'],
        ];

        return $this->doRequest($data);
    }

    /**
     * 4.5.1 订单交易查询接口.
     *
     * @param array $param = [
     *                     'sn',  //订单流水号
     *                     'date',  //订单日期
     *                     ]
     *
     * @return ret_code===0000 查询成功 tran_state:0初始,2成功,3失败,4不明,5交易关闭[超过七个自然日的初始状态会关闭],6其他
     */
    public function getOrderInfo(array $param)
    {
        $data = [
            'service' => 'transfer_search',
            'order_id' => $param['sn'],
            'mer_date' => date('Ymd', $param['date']),
            'busi_type' => '03',
        ];

        return $this->doRequest($data);
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
                'base_uri' => $this->apiUrl,
                'allow_redirects' => false,
                'connect_timeout' => 30,
                'timeout' => 30,
            ]);
        }

        return $this->httpClient;
    }

    /**
     * 以POST方式提交请求数据.
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

        $response = $this->getHttpClient()->request('POST', null, [
            'form_params' => $data,
        ]);

        return $this->processResponse($response);
    }

    /**
     * 处理联动接口的返回.
     *
     * @param \Psr\Http\Message\ResponseInterface $response PSR-7 HTTP响应对象
     *
     * @return Response
     */
    protected function processResponse(Psr7ResponseInterface $response)
    {
        $result = [
            'isRedirection' => false,
        ];

        if (302 === $response->getStatusCode()) {
            $result['isRedirection'] = true;
            $result['redirectUrl'] = $response->getHeader('Location')[0];

            return $result;
        }

        if ($response->hasHeader('Content-Type')) {
            $contentType = $response->getHeader('Content-Type')[0];
            list($mimeType, $charsetString) = explode(';', $contentType);
            $mimeType = trim($mimeType);

            $content = trim($response->getBody()->getContents());

            if ('text/html' === $mimeType) {
                $doc = new \DOMDocument();
                $doc->validateOnParse = true;

                // 避免乱码
                $content = mb_convert_encoding($content, 'HTML-ENTITIES', $this->charset);

                // 因联动构造HTML不符合规范，关闭错误提醒
                libxml_use_internal_errors(true);
                $doc->loadHTML($content);
                libxml_use_internal_errors(false);

                $xpath = new \DOMXpath($doc);
                $nodes = $xpath->query('//meta[@name="MobilePayPlatform"]');

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

                return array_merge($result, $pairs);
            } elseif ('text/text' === $mimeType) {
                $charsetString = trim($charsetString);
                list(, $charset) = explode('=', $charsetString);

                return mb_convert_encoding($content, 'UTF-8', $charset);
            } else {
                throw new \Exception('Unsupported MIME type!');
            }
        } else {
            throw new \Exception();
        }
    }

    /**
     * 为签名把数组连接为k1=v1&k2=v2...形式的字符串.
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
     * 校验包含签名的数组.
     *
     * @param array $data 待校验的数组
     *
     * @return bool
     */
    public function verifySign(array $data, $algo = OPENSSL_ALGO_SHA1)
    {
        if (!isset($data['sign'])) {
            throw new \Exception('Sign missing.');
        }

        $sign = base64_decode($data['sign']);
        foreach (['sign', 'sign_type'] as $ignore) {
            unset($data[$ignore]);
        }

        $content = mb_convert_encoding(
            $this->concatForSigning($data), self::ENCRYPT_ENCODING, $this->charset
        );

        if (!file_exists($this->umpCertFile)) {
            throw new \Exception('PEM cert file not found.');
        }

        $certContent = file_get_contents($this->umpCertFile);

        return 1 === openssl_verify($content, $sign, $certContent);
    }

    /**
     * 签名.
     *
     * @param array $data 待签名的数组
     *
     * @return string
     */
    protected function sign($data, $algo = OPENSSL_ALGO_SHA1)
    {
        if (!file_exists($this->merchantKeyFile)) {
            throw new \Exception('Merchant key file not found.');
        }

        $keyContent = file_get_contents($this->merchantKeyFile);

        $sign = null;
        if (false === openssl_sign($this->concatForSigning($data), $sign, $keyContent, $algo)) {
            throw new \Exception('Error signing.');
        }

        return base64_encode($sign);
    }

    /**
     * 加密.
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

        if (!file_exists($this->umpCertFile)) {
            throw new \Exception('UMP cert file not found.');
        }

        $keyContent = file_get_contents($this->umpCertFile);

        $crypted = null;
        if (false === openssl_public_encrypt($data, $crypted, $keyContent)) {
            throw new \Exception('Error encrypting using UMP cert.');
        }

        return base64_encode($crypted);
    }
}
