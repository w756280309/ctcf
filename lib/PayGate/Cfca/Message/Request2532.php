<?php

namespace PayGate\Cfca\Message;

use PayGate\Cfca\CfcaUtils;

/**
 * 绑卡
 * 向中金发起绑卡确认【同步】：2531是发短信，2532确认
 * 构造函数需要传入机构ID【中金分配给机构的ID】，绑定流水号，短信码
 */
class Request2532 extends AbstractRequest
{
    private $bindingSn;//绑定流水号
    private $smsCode;//短信码【中金提供】

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

    public function getTxSn()
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
