<?php

namespace PayGate\Ump;

use Crypto\CryptoUtils;
use GuzzleHttp\Client as HttpClient;
use P2pl\BorrowerInterface;
use P2pl\LoanInterface;
use Psr\Http\Message\ResponseInterface as Psr7ResponseInterface;
use P2pl\QpayTxInterface;
use P2pl\OrderTxInterface;
use P2pl\QpayBindInterface;
use P2pl\UserInterface;
use P2pl\LoanFkInterface;
use P2pl\WithdrawalInterface;

/**
 * 联动优势API调用.
 */
class Client
{
    const ENCRYPT_ENCODING = 'GB18030';

    private $apiUrl;

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
     *client配置.
     *
     * @var type
     */
    private $clientOption;

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

    private $logAdapter;

    public function __construct($apiUrl, $merchantId, $clientKeyPath, $umpCertPath, $options, LoggerInterface $logAdapter)
    {
        $this->apiUrl = $apiUrl;
        $this->merchantId = $merchantId;
        $this->clientKeyPath = $clientKeyPath;
        $this->umpCertPath = $umpCertPath;
        $this->clientOption = $options;
        $this->logAdapter = $logAdapter;
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
    public function register(UserInterface $user)
    {
        $orderId = time();

        $data = [
            'service' => 'mer_register_person',
            'order_id' => $orderId,
            'mer_cust_id' => $user->getUserId(),
            'mer_cust_name' => $this->encrypt($user->getLegalName()),
            'identity_type' => 'IDENTITY_CARD',
            'identity_code' => $this->encrypt($user->getIdNo()),
            'mobile_id' => $user->getMobile(),
        ];

        return $this->doRequest($data);
    }

    /**
     * 4.5.2 查询用户的账号和协议签署情况.
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
     * 4.5.5 商户信息查询.
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
     * 4.2.2 绑定银行卡
     *
     * @param QpayBinding $bind
     */
    public function enableQpay(QpayBindInterface $bind)
    {
        $data = [
            'service' => 'ptp_mer_bind_card',
            'ret_url' => $this->clientOption['bind_ret_url'],
            'notify_url' => $this->clientOption['bind_notify_url'],
            'sourceV' => 'HTML5',
            'order_id' => $bind->getTxSn(),
            'mer_date' => $bind->getTxDate(),
            'user_id' => $bind->getEpayUserId(),
            'card_id' => $this->encrypt($bind->getCardNo()),
            'account_name' => $this->encrypt($bind->getLegalName()),
            'identity_type' => $bind->getIdType(),
            'identity_code' => $this->encrypt($bind->getIdNo()),
            'is_open_fastPayment' => '1',
        ];
        $params = $this->buildQuery($data);

        return $this->apiUrl.'?'.$params;
    }

    /**
     * 申请提现.
     *
     * @param WithdrawalInterface $draw
     * @param $channel 渠道识别标志 pc代表pc端
     *
     * @return type
     */
    public function initDraw(WithdrawalInterface $draw, $channel = null)
    {
        $data = [
            'service' => 'cust_withdrawals',
            'ret_url' => $this->clientOption['draw_ret_url'].('pc' === $channel ? '?channel=pc' : ''),
            'notify_url' => $this->clientOption['draw_notify_url'],
            'order_id' => $draw->getTxSn(),
            'mer_date' => date('Ymd', $draw->getTxDate()),
            'user_id' => $draw->getEpayUserId(),
            'amount' => $draw->getAmount() * 100,
            'com_amt_type' => 1, //前向手续费：交易方承担
        ];

        if ('pc' !== $channel) {
            $data['sourceV'] = 'HTML5';
        }

        $params = $this->buildQuery($data);

        return $this->apiUrl.'?'.$params;
    }

    /**
     * 4.3.1 发标(商户向平台).
     *
     * @param LoanInterface     $loan
     * @param BorrowerInterface $borrower
     *
     * @return Response
     */
    public function registerLoan(LoanInterface $loan, BorrowerInterface $borrower)
    {
        $data = [
            'service' => 'mer_bind_project',
            'project_id' => $loan->getLoanId(),
            'project_name' => $loan->getLoanName(),
            'project_amount' => $loan->getLoanAmount() * 100, // 单位分,最小1,最大9999999999999
            'project_expire_date' => date('Ymd', $loan->getLoanExpireDate()), // 只做格式校验。没有对时间做其他限制
            'loan_user_id' => $borrower->getLoanUserId(), // 会去联动一侧判断用户是否存在[测试上投资用户可以用来融资]
            'loan_acc_type' => (null === $borrower->getLoanAccountType() || 1 === $borrower->getLoanAccountType()) ? '01' : '02', //当为商户号时loan_acc_type 为必填字段，值02
        ];

        return $this->doRequest($data);
    }

    /**
     * 4.3.2 标的更新 更新状态
     *
     * @param string $loanId
     * @param int    $state
     *                       注：1建标状态修改为1失败
     *                       2跨状态修改失败
     *                       3建标状态修改状态值非法【00060700】请求的参数[project_state(123)]格式或值不正确
     *                       4建标状态修改不存在的标的编号【00240200】标的不存在
     *                       592-0-1-2-3-4,顺利进行的步骤
     *
     * @return Response
     */
    public function updateLoanState($loanId, $state)
    {
        $data = [
            'service' => 'mer_update_project',
            'project_id' => $loanId,
            'project_state' => $state, //*** 92状态的不能跨状态修改，比如从92->1[建标成功到投标中]错误代码00240400；92->0->1可以。初步测试1->2不可以【需要验证：是因为没投标实际融资额为0导致，所以不能】
            'change_type' => '01',
        ];

        return $this->doRequest($data);
    }

    /**
     * 4.3.2 标的更新 更新状态
     *
     * @param LoanInterface $loan
     *                            标的状态修改为1投标中的时候，不允许对标的进行change_type=01的更新
     *
     * @return Response
     */
    public function updateLoanInfo(LoanInterface $loan)
    {
        $data = [
            'service' => 'mer_update_project',
            'project_id' => $loan->getLoanId(),
            'project_name' => $loan->getLoanName(),
            'project_amount' => $loan->getLoanAmount(), // 单位分,最小1,最大9999999999999
            'change_type' => '01',
        ];

        return $this->doRequest($data);
    }

    /**
     * 4.3.2 标的更新 更新借款人.
     *
     * @param LoanInterface     $loan
     * @param BorrowerInterface $borrower
     *
     * @return Response
     */
    public function updateLoanBorrower(LoanInterface $loan, BorrowerInterface $borrower)
    {
        $data = [
            'service' => 'mer_update_project',
            'project_id' => $loan->getLoanId(),
            'loan_user_id' => $borrower->getLoanUserId(), // 会去联动一侧判断用户是否存在[测试上投资用户可以用来融资]
            'loan_acc_type' => (null === $borrower->getLoanAccountType() || 1 === $borrower->getLoanAccountType()) ? '01' : '02', //当为商户号时loan_acc_type 为必填字段，值02
            'option_type' => 0, //仅限建标状态【92】下可以替换借款人，从文档中来看，对借款人不可以注销，注销的只能是担保方和资金使用方
            'change_type' => '02',
        ];

        return $this->doRequest($data);
    }

    /**
     * 4.5.3 标的查询接口 查询标的账户状态及余额.
     *
     * @param type $loanId 商户端标的号
     *
     * @return type
     */
    public function getLoanInfo($loanId)
    {
        $data = [
            'service' => 'project_account_search',
            'project_id' => $loanId,
        ];

        return $this->doRequest($data);
    }

    /**
     * 4.4.1 个人客户充值申请.
     *
     * @param QpayTxInterface $qpay
     *
     * @return response
     */
    public function rechargeViaQpay(QpayTxInterface $qpay)
    {
        $data = [
            'service' => 'mer_recharge_person',
            'ret_url' => $this->clientOption['rec_ret_url'],
            'notify_url' => $this->clientOption['rec_notify_url'],
            'sourceV' => 'HTML5',
            'order_id' => $qpay->getTxSn(),
            'mer_date' => $qpay->getTxDate(),
            'pay_type' => 'DEBITCARD',
            'user_id' => $qpay->getEpayUserId(),
            'amount' => $qpay->getAmount(),
            'user_ip' => $qpay->getClientIp(),
            'com_amt_type' => 2,
        ];

        return $this->doRequest($data);
    }

    /**
     * 4.4.1 个人客户网银充值申请.
     *
     * @param QpayTxInterface $qpay
     * @param string          $gateId 银行简码
     *
     * @return response
     */
    public function rechargeViaBpay(QpayTxInterface $qpay, $gateId)
    {
        $data = [
            'service' => 'mer_recharge_person',
            'ret_url' => $this->clientOption['rec_pc_ret_url'],
            'notify_url' => $this->clientOption['rec_pc_notify_url'],
            'sourceV' => 'HTML5',
            'order_id' => $qpay->getTxSn(),
            'mer_date' => $qpay->getTxDate(),
            'pay_type' => 'B2CDEBITBANK',
            'user_id' => $qpay->getEpayUserId(),
            'amount' => $qpay->getAmount(),   //以分为单位
            'gate_id' => $gateId,
            'user_ip' => $qpay->getClientIp(),
            'com_amt_type' => 2,
        ];

        $params = $this->buildQuery($data);
        header('Location:'.$this->apiUrl.'?'.$params);
    }

    /**
     * 4.3.3 标的转账
     * 用户投标.
     *
     * @param OrderTxInterface $ord
     */
    public function registerOrder(OrderTxInterface $ord)
    {
        $data = [
            'service' => 'project_transfer',
            'ret_url' => $this->clientOption['order_ret_url'],
            'notify_url' => $this->clientOption['order_notify_url'],
            'sourceV' => 'HTML5',
            'order_id' => $ord->getTxSn(),
            'mer_date' => date('Ymd', $ord->getTxDate()),
            'project_id' => $ord->getLoanId(),
            'serv_type' => '01',
            'trans_action' => '01',
            'partic_type' => '01',
            'partic_acc_type' => '01',
            'partic_user_id' => $ord->getEpayUserId(),
            'amount' => $ord->getAmount() * 100,
        ];
        $params = $this->buildQuery($data);

        return $this->apiUrl.'?'.$params;
    }

    /**
     * 4.3.3 标的转账【由标的账户转到借款人同步请求】.
     *
     * @param LoanFkInterface $fk
     *
     * @return string
     */
    public function loanTransferToMer(LoanFkInterface $fk)
    {
        $data = [
            'service' => 'project_transfer',
            'sourceV' => 'HTML5',
            'order_id' => $fk->getTxSn(),
            'mer_date' => date('Ymd', $fk->getTxDate()),
            'project_id' => $fk->getLoanId(),
            'serv_type' => '53',
            'trans_action' => '02',
            'partic_type' => '02',
            'partic_acc_type' => '02',
            'partic_user_id' => $fk->getBorrowerId(),
            'amount' => $fk->getAmount() * 100,
        ];

        return $this->doRequest($data);
    }

    /**
     * 4.5.1 订单交易查询接口.
     *
     * @param OrderTxInterface $order 商户订单
     *
     * @return ret_code===0000 查询成功 tran_state:0初始,2成功,3失败,4不明,5交易关闭[超过七个自然日的初始状态会关闭],6其他
     */
    public function getOrderInfo(OrderTxInterface $order)
    {
        $data = [
            'service' => 'transfer_search',
            'order_id' => $order->getTxSn(),
            'mer_date' => date('Ymd', $order->getTxDate()),
            'busi_type' => '03',
        ];

        return $this->doRequest($data);
    }

    /**
     * 4.3.3 标的转账【用于流标同步请求】.
     *
     * @param OrderTxInterface $ord
     *
     * @return response
     */
    public function loanTransferToLender(OrderTxInterface $ord)
    {
        $data = [
            'service' => 'project_transfer',
            'sourceV' => 'HTML5',
            'order_id' => $ord->getTxSn(),
            'mer_date' => date('Ymd', $ord->getTxDate()),
            'project_id' => $ord->getLoanId(),
            'serv_type' => '51',
            'trans_action' => '02',
            'partic_type' => '01',
            'partic_acc_type' => '01',
            'partic_user_id' => $ord->getEpayUserId(),
            'amount' => $ord->getAmount() * 100,
        ];

        return $this->doRequest($data);
    }

    /**
     * 4.4.2 融资方充值申请.
     *
     * @param QpayTxInterface $recharge 充值记录对象
     * @param type            $payType  支付方式 取值范围：B2BBANK（企业网银）,B2CDEBITBANK（个人借记卡网银）
     * @param type            $merId    被充值企业资金账户托管平台商户号
     * @param type            $gateId   发卡行编号
     */
    public function OrgRechargeApply(QpayTxInterface $recharge, $payType, $merId, $gateId)
    {
        $data = [
            'service' => 'mer_recharge',
            'ret_url' => $this->clientOption['mer_recharge_ret_url'],
            'notify_url' => $this->clientOption['mer_recharge_notify_url'],
            'order_id' => $recharge->getTxSn(),
            'mer_date' => $recharge->getTxDate(),
            'pay_type' => $payType,
            'recharge_mer_id' => $merId,
            'account_type' => '01',
            'amount' => $recharge->getAmount(),
            'gate_id' => $gateId,
            'user_ip' => $recharge->getClientIp(),
            'com_amt_type' => 2,
        ];

        $params = $this->buildQuery($data);
        header('Location:'.$this->apiUrl.'?'.$params);
    }

    /**
     * 4.5.1 充值交易查询接口.
     *
     * @param type $txSn   商户订单号
     * @param type $txDate 商户订单日期
     *
     * @return ret_code===0000 查询成功 tran_state:0初始,2成功,3失败,4不明,5交易关闭[超过七个自然日的初始状态会关闭],6其他
     */
    public function getRechargeInfo($txSn, $txDate)
    {
        $data = [
            'service' => 'transfer_search',
            'order_id' => $txSn,
            'mer_date' => date('Ymd', $txDate),
            'busi_type' => '01',
        ];

        return $this->doRequest($data);
    }

    /**
     * 4.5.1 提现交易查询接口(商户->平台).
     *
     * @param WithdrawalInterface $draw
     *                                  return tran_state 成功状态2 失败状态3,5,15
     */
    public function getDrawInfo(WithdrawalInterface $draw)
    {
        $data = [
            'service' => 'transfer_search',
            'order_id' => $draw->getTxSn(), //商户订单号支持数字、英文字母，其他字符不建议使用
            'mer_date' => date('Ymd', $draw->getTxDate()), //商户生成订单的日期，格式YYYYMMDD
            'busi_type' => '02', //02提现
        ];

        return $this->doRequest($data);
    }

    /**
     * 4.4.6 融资用户提现申请(商户->平台).
     *
     * @param type $txSn   商户订单号
     * @param type $txDate 商户订单日期
     * @param type $merId  提现企业资金账户托管平台商户号
     * @param type $amount 提现金额
     */
    public function orgDrawApply(WithdrawalInterface $draw)
    {
        $data = [
            'service' => 'mer_withdrawals',
            'notify_url' => $this->clientOption['mer_draw_notify_url'],
            'order_id' => $draw->getTxSn(),
            'mer_date' => date('Ymd', $draw->getTxDate()), //商户生成订单的日期，格式YYYYMMDD
            'withdraw_mer_id' => $draw->getEpayUserId(),
            'amount' => $draw->getAmount() * 100, //单位为分
            'com_amt_type' => '1',
        ];

        return $this->doRequest($data);
    }

    /**
     * 4.3.3 投资用户返款接口(商户->平台)
     * @param type $txSn 商户订单号
     * @param type $txDate 商户订单日期
     * @param type $projectId 标的号
     * @param type $particUserId 转账方用户号 企业用户：联动开立的商户号
     * @param type $amount 金额
     */
    public function fankuan($txSn, $txDate, $projectId, $particUserId, $amount)
    {
        $data = [
            'service' => 'project_transfer',
            'order_id' => $txSn,
            'mer_date' => date('Ymd', $txDate),  //商户生成订单的日期，格式YYYYMMDD
            'project_id' => $projectId,  //标的号,本地id值
            'serv_type' => '54',
            'trans_action' => '02',
            'partic_type' => '01',
            'partic_acc_type' => '01',
            'partic_user_id' => $particUserId,
            'amount' => $amount * 100,   //单位为分
        ];

        return $this->doRequest($data);
    }

    /**
     * 4.3.5 融资用户还款(商户→平台)
     * @param type $txSn 商户订单号
     * @param type $txDate 商户订单日期
     * @param type $projectId 标的号
     * @param type $particUserId 转账方用户号 企业用户：联动开立的商户号
     * @param type $amount 金额
     */
    public function huankuan($txSn, $projectId, $particUserId, $amount)
    {
        $data = [
            'service' => 'project_transfer_nopwd',
            'order_id' => $txSn,
            'mer_date' => date('Ymd'),  //商户生成订单的日期，格式YYYYMMDD
            'project_id' => $projectId,  //标的号,本地id值
            'serv_type' => '03',
            'trans_action' => '01',
            'partic_type' => '02',
            'partic_acc_type' => '02',
            'partic_user_id' => $particUserId,
            'amount' => $amount * 100,  //单位为分
        ];

        return $this->doRequest($data);
    }

    /**
     * 4.2.13 充值交易密码
     *
     * @param UserInterface $user
     *
     * @return type
     */
    public function resetTradePass(UserInterface $user)
    {
        $data = [
            'service' => 'mer_send_sms_pwd',
            'user_id' => $user->getEpayUserId(),
            'order_id' => $this->generateSn('RP'),
            'identity_code' => $this->encrypt($user->getIdNo()), //GBK编码后使用联动公钥进行RSA加密，最后使用BASE64编码
        ];

        return $this->doRequest($data);
    }

    /**
     * 获取对账单（暂限定为标的交易）.
     *
     * @param string $date YYYYMMDD
     *
     * @return string
     */
    public function getSettlement($date)
    {
        $data = [
            'service' => 'download_settle_file_p',
            'settle_date_p2p' => $date,
            'settle_type_p2p' => '03',
        ];

        return $this->doRequest($data);
    }

    /**
     * 生成流水串号.
     *
     * @param type $prefix
     *
     * @return type
     */
    private function generateSn($prefix = '')
    {
        list($sec, $usec) = explode('.', sprintf('%.6f', microtime(true)));

        return $prefix
            .date('ymdHis', $sec)
            .$usec
            .mt_rand(1000, 9999);
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
        $starttime = microtime(true);
        // 添加协议参数
        $source_data = $data = array_merge($data, [
            'charset' => $this->charset,
            'mer_id' => $this->merchantId,
            'version' => $this->version,
        ]);

        // 签名
        $sign_data = $data['sign'] = $this->sign($data);
        $data['sign_type'] = $this->signType;

        $httpResponse = $this->getHttpClient()->request('POST', null, [
            'form_params' => $data,
        ]);
        $prorp = $this->processHttpResponse($httpResponse);
        $endtime = microtime(true);
        if (
                'mer_register_person' === $data['service']
                || 'mer_bind_project' === $data['service']
                || 'mer_update_project' === $data['service']
                || 'project_transfer' === $data['service']
                || 'mer_withdrawals' === $data['service']
                || 'mer_send_sms_pwd' === $data['service']
                || 'project_transfer_nopwd' === $data['service']
         ) {
            $this->logAdapter->log(1, $source_data, $sign_data, $prorp, $endtime - $starttime);
        }

        return $prorp;
    }

    public function buildQuery(array $data)
    {
        $starttime = microtime(true);
        // 添加协议参数
        $source_data = $data = array_merge($data, [
            'charset' => $this->charset,
            'mer_id' => $this->merchantId,
            'version' => $this->version,
        ]);

        // 签名
        $sign_data = $data['sign'] = $this->sign($data);
        $data['sign_type'] = $this->signType;
        $endtime = microtime(true);
        $this->logAdapter->log(1, $source_data, $sign_data, null, $endtime - $starttime);

        return http_build_query($data);
    }

    /**
     * 处理联动接口的返回.
     *
     * @param \Psr\Http\Message\ResponseInterface $response PSR-7 HTTP响应对象
     *
     * @return Response
     */
    protected function processHttpResponse(Psr7ResponseInterface $response)
    {
        $content = trim($response->getBody()->getContents());

        if (302 === $response->getStatusCode()) {
            return new Response([], $response->getHeader('Location')[0]);
        } elseif ($response->hasHeader('Content-Type')) {
            $contentType = $response->getHeader('Content-Type')[0];
            list($mimeType, $charsetString) = explode(';', $contentType);
            $mimeType = trim($mimeType);

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

                return new Response($pairs);
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
    public function verifySign(array $data)
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

        return CryptoUtils::verifySign($content, $sign, $this->umpCertPath);
    }

    /**
     * 签名.
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

        return base64_encode(CryptoUtils::encrypt($data, $this->umpCertPath));
    }
}
