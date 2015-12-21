<?php

namespace PayGate\Cfca\Message;

use PayGate\Cfca\CfcaUtils;

class Request2532 extends AbstractRequest
{
    private $bindingSn;
    private $smsCode;

    public function __construct(
        $institutionId,
        $bindingSn,
        $smsCode
    ) {
        $this->bindingSn = $bindingSn;
        $this->smsCode = $smsCode;

        parent::__construct($institutionId, 2532);
    }

    public function getBindingSn()
    {
        return $this->bindingSn;
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
        <TxSNBinding>{{ bindingSn }}</TxSNBinding>
        <SMSValidationCode>{{ smsCode }}</SMSValidationCode>
    </Body>
</Request>
TPL;

        return CfcaUtils::renderXml($tpl, [
            'institutionId' => $this->getInstitutionId(),
            'txCode' => $this->getTxCode(),
            'bindingSn' => $this->bindingSn,
            'smsCode' => $this->smsCode,
        ]);
    }
}
