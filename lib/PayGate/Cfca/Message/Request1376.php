<?php

namespace PayGate\Cfca\Message;

use PayGate\Cfca\CfcaUtils;

/**
 * 快捷支付
 * 向中金发起绑卡确认【同步】：2531是发短信，2532确认
 * 构造函数需要传入机构ID【中金分配给机构的ID】，绑定流水号，短信码
 */
class Request1376 extends AbstractRequest
{
    private $rechargeSn;//充值号
    private $smsCode;//短信码

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
     * 用作日志记录时候通用的方法.
     *
     * @return type
     */
    public function getTxSn()
    {
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
            'smsCode' => $this->smsCode,
        ]);
    }
}
