<?php

namespace Lhjx\Identity;

use Lhjx\Identity\DataCenter\Client;
use Lhjx\Identity\DataCenter\Credit;

class IdentityVerifier
{
    private $dataCenter;
    public function __construct($dataCenter)
    {
        $this->dataCenter = $dataCenter;
    }

    /**
     * 通过第三方的接口验证用户开户结果
     * @param VerificationInterface $verification verification接口
     * @throws VerificationException
     */
    public function verify(VerificationInterface $verification)
    {
        $name = $verification->getName();
        $idCardNum = $verification->getIdCardNumber();
        $data = [
            'apiName' => [Credit::CREDIT_IDCARDELEMENTS],
            'name' => $name,
            'idCardNum' => $idCardNum,
        ];
        $dataCenter = $this->dataCenter;
        $baseUrl = $dataCenter['baseUrl'];
        $domain = $dataCenter['domain'];
        $email = $dataCenter['email'];
        $password = $dataCenter['password'];
        $client = new Client($baseUrl, $domain, $email, $password);
        $result = $client->getApiService($data);
        if ($result['认证情况'] != '匹配') {
            if ($result['认证情况'] == '超时') {
                throw new VerificationException($verification, $result['认证情况'], 201);
            }
            throw new VerificationException($verification, $result['认证情况'], 202);
        }
    }
}
