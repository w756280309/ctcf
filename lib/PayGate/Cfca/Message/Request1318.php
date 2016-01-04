<?php

namespace PayGate\Cfca\Message;

use PayGate\Cfca\CfcaUtils;

class Request1318 extends AbstractRequest
{
    private $xml;
    private $paymentNo;
    private $amount;
    private $status;
    private $bankNotificationTime;

    public static function createFromXml($xml)
    {
        $xmlObj = simplexml_load_string($xml);

        return new self(
            $xmlObj->Body->InstitutionID,
            $xmlObj->Body->PaymentNo,
            $xmlObj->Body->Amount,
            $xmlObj->Body->Status,
            $xmlObj->Body->BankNotificationTime
        );
    }

    public function __construct(
        $institutionId,
        $paymentNo,
        $amount,
        $status,
        $bankNotificationTime
    )
    {
        parent::__construct($institutionId, 1318);

        $this->paymentNo = $paymentNo;
        $this->amount = $amount;
        $this->status = $status;
        $this->bankNotificationTime = $bankNotificationTime;
    }

    public function getPaymentNo()
    {
        return $this->paymentNo;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getBankNotificationTime()
    {
        return $this->bankNotificationTime;
    }

    public function getTxSn()
    {
        return $this->paymentNo;
    }

    public function getXml()
    {
        if (null === $this->xml) {
            $tpl = <<<TPL
<?xml version="1.0" encoding="UTF-8"?>
<Request version="2.0">
    <Head>
        <TxCode>{{ txCode }}</TxCode>
    </Head>
    <Body>
        <InstitutionID>{{ institutionId }}</InstitutionID>
        <PaymentNo>{{ paymentNo }}</PaymentNo>
        <Amount>{{ amount }}</Amount>
        <Status>{{ status }}</Status>
        <BankNotificationTime>{{ bankNotificationTime }}</BankNotificationTime>
    </Body>
</Request>
TPL;

            $this->xml = CfcaUtils::renderXml($tpl, [
                'txCode' => $this->getTxCode(),
                'institutionId' => $this->getInstitutionId(),
                'paymentNo' => $this->getPaymentNo(),
                'amount' => $this->getAmount(),
                'status' => $this->getStatus(),
                'bankNotificationTime' => $this->getBankNotificationTime(),
            ]);
        }

        return $this->xml;
    }
}
