<?php

namespace PayGate\Cfca\Message;

use Yii;
use PayGate\Cfca\CfcaUtils;
use PayGate\Cfca\Settlement\AccountSettlement;

/**
 * 批量代付
 * 将批量代付的明细通过接口传给支付平台【后台放款时触发】
 * 构造函数需要传入机构ID【中金分配给机构的ID】，
 */
class Request1510 extends AbstractRequest {

    private $settlementSn;//结算号

    public function __construct(
    $institutionId, $settlementSn
    ) {
        $this->settlementSn = $settlementSn;
        parent::__construct($institutionId, 1510);
    }

    /**
     * 结算号
     * @return string
     */
    public function getSettlementSn() {
        return $this->settlementSn;
    }

    /**
     * 用作日志记录时候通用的方法
     * @return string
     */
    public function getTxSn() {
        return $this->settlementSn;
    }

    public function getXml() {
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
                    'sn' => $this->settlementSn
        ]);
    }

}
