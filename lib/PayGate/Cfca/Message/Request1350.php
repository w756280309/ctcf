<?php

namespace PayGate\Cfca\Message;

use PayGate\Cfca\CfcaUtils;

/**
 * 结算
 * 查询市场订单结算交易结果【定时任务，中金于发起之日次日11-15时出金】
 * 构造函数需要传入机构ID【中金分配给机构的ID】，.
 */
class Request1350 extends AbstractRequest
{
    private $settlementSn;//结算号

    public function __construct(
    $institutionId, $settlementSn
    ) {
        $this->settlementSn = $settlementSn;
        parent::__construct($institutionId, 1350);
    }

    /**
     * 结算号.
     *
     * @return string
     */
    public function getSettlementSn()
    {
        return $this->settlementSn;
    }

    /**
     * 用作日志记录时候通用的方法.
     *
     * @return string
     */
    public function getTxSn()
    {
        return $this->settlementSn;
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
        <InstitutionID>{{ institutionId }}</InstitutionID>        
        <SerialNumber>{{ sn }}</SerialNumber>
    </Body>
</Request>
TPL;

        return CfcaUtils::renderXml($tpl, [
                    'institutionId' => $this->getInstitutionId(),
                    'txCode' => $this->getTxCode(),
                    'sn' => $this->settlementSn,
        ]);
    }
}
