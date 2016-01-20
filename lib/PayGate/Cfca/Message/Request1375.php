<?php

namespace PayGate\Cfca\Message;

use PayGate\Cfca\CfcaUtils;

/**
 * 快捷支付
 * 向中金发起支付确认，发短信
 * 构造函数需要传入机构ID【中金分配给机构的ID】，绑定流水号，金额
 */
class Request1375 extends AbstractRequest
{
    private $rechargeSn;//快捷充值单号
    private $remark;//描述
    private $bindingSn;//绑定流水号【绑卡时候的流水号】
    private $amount;//充值金额

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
            'amount' => $this->amount * 100,//中金已分为单位制
            'remark' => $this->remark
        ]);
    }
}
