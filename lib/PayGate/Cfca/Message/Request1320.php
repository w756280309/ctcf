<?php

namespace PayGate\Cfca\Message;

use PayGate\Cfca\CfcaUtils;

/**
 * 支付查询
 * 发起一笔订单支付查询到中金【定时任务，修改查询成功的】
 * 构造函数需要传入机构ID【中金分配给机构的ID】，充值单号.
 */
class Request1320 extends AbstractRequest
{
    private $paymentNo; //支付交易号

    public function __construct(
    $institutionId, $rechargeSn
    ) {
        $this->paymentNo = $rechargeSn;
        parent::__construct($institutionId, 1320);
    }

    /**
     * 结算号.
     *
     * @return string【格式：时间精度毫秒级+4位随机数】
     */
    public function getPaymentNo()
    {
        return $this->paymentNo;
    }

    /**
     * 用作日志记录时候通用的方法.
     *
     * @return string
     */
    public function getTxSn()
    {
        return $this->paymentNo;
    }

    public function getXml()
    {
        $tpl = <<<TPL
<?xml version="1.0" encoding="UTF-8"?>
<Request version="2.0">
    <Head>
        <TxCode>{{ txCode }}</TxCode>
    </Head>
    <Body>
        <InstitutionID>{{ institutionId }}</InstitutionID>  
        <PaymentNo>{{ paymentNo }}</PaymentNo>
    </Body>
</Request>
TPL;

        return CfcaUtils::renderXml($tpl, [
                    'institutionId' => $this->getInstitutionId(),
                    'txCode' => $this->getTxCode(),
                    'paymentNo' => $this->paymentNo,
        ]);
    }
}
