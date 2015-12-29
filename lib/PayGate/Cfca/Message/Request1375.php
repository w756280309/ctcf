<?php

namespace PayGate\Cfca\Message;

use PayGate\Cfca\CfcaUtils;

class Request1375 extends AbstractRequest
{
    private $rechargeSn;
    private $remark;
    private $bindingSn;
    private $amount;

    public function __construct(
        $institutionId,
        $bindingSn,
        $amount,
        $remark=""
    ) {
        $this->rechargeSn = CfcaUtils::generateSn('RC');
        $this->bindingSn = $bindingSn;
        $this->amount = $amount;
        $this->remark = $remark;

        parent::__construct($institutionId, 1375);
    }

    public function getRechargeSn()
    {
        return $this->rechargeSn;
    }
    
    /**
     * 用作日志记录时候通用的方法
     * @return type
     */
    public function getTxSn(){
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
	<TxSNBinding>{{ bingdingSn }}</TxSNBinding>
        <Amount>{{ amount }}</Amount>
        <Remark>{{ remark }}</Remark>
    </Body>
</Request>
TPL;

        return CfcaUtils::renderXml($tpl, [
            'institutionId' => $this->getInstitutionId(),
            'txCode' => $this->getTxCode(),
            'ordNo' => $this->rechargeSn,
            'paymentNo' => $this->rechargeSn,
            'bingdingSn' => $this->bindingSn,
            'amount' => $this->amount,
            'remark' => $this->remark
        ]);
    }
}
