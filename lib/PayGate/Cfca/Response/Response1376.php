<?php

namespace PayGate\Cfca\Response;

class Response1376 extends Response
{
    protected $orderNo;
    protected $paymentNo;
    protected $verifyStatus;
    protected $paymentStatus;
    protected $bankTxTime;

    public function isSuccess()
    {
        return 40 === $this->verifyStatus
            && 20 === $this->paymentStatus;
    }
    
    public function getSerializationData()
    {
        return [
            'orderNo' => $this->orderNo,
            'paymentNo' => $this->paymentNo,
            'verifyStatus' => $this->verifyStatus,
            'paymentStatus' => $this->paymentStatus,
            'bankTxTime' => $this->bankTxTime,
        ];
    }

    public function getOrderNo()
    {
        return $this->orderNo;
    }

    public function getPaymentNo()
    {
        return $this->paymentNo;
    }

    public function getVerifyStatus()
    {
        return $this->verifyStatus;
    }

    public function getPaymentStatus()
    {
        return $this->paymentStatus;
    }

    public function getBankTxTime()
    {
        return $this->bankTxTime;
    }

    protected function populate()
    {
        $this->orderNo = (string) $this->xmlObj->Body->OrderNo;
        $this->paymentNo = (string) $this->xmlObj->Body->PaymentNo;
        $this->verifyStatus = (int) $this->xmlObj->Body->VerifyStatus;
        $this->paymentStatus = (int) $this->xmlObj->Body->Status;
        $this->bankTxTime = (string) $this->xmlObj->Body->BankTxTime;
    }
}
