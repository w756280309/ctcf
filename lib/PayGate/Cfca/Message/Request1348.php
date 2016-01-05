<?php

namespace PayGate\Cfca\Message;

use PayGate\Cfca\CfcaUtils;

class Request1348 extends AbstractRequest {

    private $xml;
    private $serialNumber;
    private $orderNo;
    private $amount;
    private $status;
    private $transferTime;
    private $successTime;

    public static function createFromXml($xml) {
        $xmlObj = simplexml_load_string($xml);

        return new self(
                $xmlObj->Body->InstitutionID, 
                $xmlObj->Body->SerialNumber,
                $xmlObj->Body->OrderNo,
                $xmlObj->Body->Amount, 
                $xmlObj->Body->Status, 
                $xmlObj->Body->TransferTime,
                $xmlObj->Body->SuccessTime
        );
    }

    public function __construct(
        $institutionId, 
        $serialNumber, 
        $orderNo, 
        $amount, 
        $status, 
        $transferTime, 
        $successTime
    ) {
        parent::__construct($institutionId, 1348);

        $this->serialNumber = $serialNumber;
        $this->orderNo = $orderNo;
        $this->amount = $amount;
        $this->status = $status;
        $this->transferTime = $transferTime;
        $this->successTime = $successTime;
    }

    public function getSerialNumber() {
        return $this->serialNumber;
    }

    public function getOrderNo() {
        return $this->orderNo;
    }

    public function getAmount() {
        return $this->amount;
    }

    public function getStatus() {
        return $this->status;
    }

    public function getTransferTime() {
        return $this->transferTime;
    }

    public function getSuccessTime() {
        return $this->successTime;
    }

    public function getTxSn() {
        return $this->paymentNo;
    }

    public function getXml() {
        if (null === $this->xml) {
            $tpl = <<<TPL
<?xml version="1.0" encoding="UTF-8"?>
<Request version="2.0">
    <Head>
        <TxCode>{{ txCode }}</TxCode>
    </Head>
    <Body>
        <InstitutionID>{{ institutionId }}</InstitutionID>
        <SerialNumber>{{ serialNumber }}</SerialNumber>
        <OrderNo>{{ orderNo }}</OrderNo>
        <Amount>{{ amount }}</Amount>
        <Status>{{ status }}</Status>
        <TransferTime>{{ transferTime }}</TransferTime>
        <SuccessTime>{{ successTime }}</SuccessTime>
        <ErrorMessage></ErrorMessage>
    </Body>
</Request>
TPL;

            $this->xml = CfcaUtils::renderXml($tpl, [
                        'txCode' => $this->getTxCode(),
                        'institutionId' => $this->getInstitutionId(),
                        'serialNumber' => $this->getSerialNumber(),
                        'orderNo' => $this->getOrderNo(),
                        'amount' => $this->getAmount(),
                        'status' => $this->getStatus(),
                        'transferTime' => $this->getTransferTime(),
                        'successTime' => $this->getSuccessTime()
            ]);
        }

        return $this->xml;
    }

}
