<?php

namespace PayGate\Cfca\Message;

use PayGate\Cfca\CfcaUtils;

class Request1376 extends AbstractRequest
{
    private $rechargeSn;
    private $smsCode;

    public function __construct(
        $institutionId,
        $rechargeSn,
        $smsCode
    ) {
        $this->rechargeSn = $rechargeSn;
        $this->smsCode = $smsCode;

        parent::__construct($institutionId, 1376);
    }

    /**
     * 用作日志记录时候通用的方法
     * @return type
     */
    public function getTxSn(){
        return $this->rechargeSn;
    }
    
    public function getRechargeSn()
    {
        return $this->rechargeSn;
    }

    public function getXml()
    {
        $tpl = <<<TPL
<?xml version="1.0" encoding="UTF-8"?>
<Request version="2.0">
    <Head>
        <InstitutionID>{{ institutionId }}</InstitutionID>
        <TxCode>{{ txCode }}</TxCode>
    </Head>
    <Body>
        <OrderNo>{{ ordNo }}</OrderNo>  
	<PaymentNo>{{ paymentNo }}</PaymentNo>  
	<SMSValidationCode>{{ smsCode }}</SMSValidationCode>
    </Body>
</Request>
TPL;

        return CfcaUtils::renderXml($tpl, [
            'institutionId' => $this->getInstitutionId(),
            'txCode' => $this->getTxCode(),
            'ordNo' => $this->rechargeSn,
            'paymentNo' => $this->rechargeSn,
            'smsCode' => $this->smsCode
        ]);
    }
}
