<?php

namespace PayGate\Cfca\Message;

use PayGate\Cfca\CfcaUtils;

/**
 * 中金对账单
 * 中金一般于凌晨5时出前一日的对账单
 * 构造函数需要传入机构ID【中金分配给机构的ID】，日期Y-m-d.
 */
class Request1810 extends AbstractRequest
{
    private $date; //对账日期，格式：Y-m-d

    public function __construct(
    $institutionId, $date
    ) {
        $this->date = $date;
        parent::__construct($institutionId, 1810);
    }

    public function getDate()
    {
        return $this->date;
    }

    /**
     * 用作日志记录时候通用的方法[时间].
     *
     * @return type
     */
    public function getTxSn()
    {
        return $this->date;
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
        <Date>{{ date }}</Date> 
    </Body>
</Request>
TPL;

        return CfcaUtils::renderXml($tpl, [
                    'institutionId' => $this->getInstitutionId(),
                    'txCode' => $this->getTxCode(),
                    'date' => $this->date,
        ]);
    }
}
