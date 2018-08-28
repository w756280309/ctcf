<?php

namespace Lhjx\Identity;

use Throwable;

class VerificationException extends \Exception
{
    public $verification;
    public function __construct(VerificationInterface $verification, $result, $code = 0, \Exception $previous = null)
    {
        $this->verification = $verification;
        $message = '用户进行实名验证失败，认证流水id为' . $verification->id . ' ,失败原因' . $result . PHP_EOL;
        parent::__construct($message, $code, $previous);
    }

    /**
     * 获取verificaion对象
     * @return VerificationInterface
     */
    public function getVerification()
    {
        return $this->verification;
    }
}
