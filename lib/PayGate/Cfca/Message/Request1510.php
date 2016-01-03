<?php

namespace PayGate\Cfca\Message;

use Yii;
use PayGate\Cfca\CfcaUtils;

/**
 * 批量代付
 * 将批量代付的明细通过接口传给支付平台【后台放款时触发】
 * 构造函数需要传入机构ID【中金分配给机构的ID】，
 */
class Request1510 extends AbstractRequest {

    private $batch; //批量代付的对象
    private $batchSn; //批量代付代码
    private $batchItem; //批量代付包含批次数据

    public function __construct(
    $institutionId, $batch
    ) {
        $this->batch = $batch;
        $this->batchSn = $batch->sn;
        $items = $batch->items;
        $this->batchItem = $items[0];
        parent::__construct($institutionId, 1510);
    }

    /**
     * 返回批次数据
     * @return array
     */
    public function getBatchItem() {
        return $this->batchItem;
    }

    /**
     * 批量代付批次号
     * @return string
     */
    public function getBatchSn() {
        return $this->batchSn;
    }

    /**
     * 用作日志记录时候通用的方法，批量代付批次号
     * @return string
     */
    public function getTxSn() {
        return $this->batchSn;
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
	<BatchNo>{{ batchNo }}</BatchNo>
	<TotalAmount>{{ totalAmount }}</TotalAmount>
	<TotalCount>{{ totalCount }}</TotalCount>
	<Remark>{{ remark }}</Remark>
	<PaymentFlag>{{ paymentFlag }}</PaymentFlag>
        <Item>
            <ItemNo>{{ itemNo }}</ItemNo>
            <Amount>{{ amount }}</Amount>
            <BankID>{{ bankID }}</BankID>
            <AccountType>{{ accountType }}</AccountType>
            <AccountName>{{ accountName }}</AccountName>
            <AccountNumber>{{ accountNumber }}</AccountNumber>
            <BranchName>{{ branchName }}</BranchName>
            <Province>{{ province }}</Province>
            <City>{{ city }}</City>
            <PhoneNumber>{{ phoneNumber }}</PhoneNumber>
            <Email>{{ email }}</Email>
            <IdentificationType>{{ identificationType }}</IdentificationType>
            <IdentificationNumber>{{ identificationNumber }}</IdentificationNumber>
        </Item>
    </Body>
</Request>
TPL;
        return CfcaUtils::renderXml($tpl, [
                    'institutionId' => $this->getInstitutionId(),
                    'txCode' => $this->getTxCode(),
                    'batchNo' => $this->batch->sn,
                    'totalAmount' => $this->batch->total_amount,
                    "totalCount" => $this->batch->total_count,
                    'remark' => $this->batch->remark,
                    'payment_flag' => $this->batch->payment_flag,
                    'itemNo' => $this->batchItem->draw->sn,
                    'amount' => $this->batchItem->amount*100,
                    'bankID' => $this->batchItem->bank_id,
                    'accountType' => $this->batchItem->account_type,
                    'accountName' => $this->batchItem->account_name,
                    'accountNumber' => $this->batchItem->account_number,
                    'branchName' => $this->batchItem->branch_name,
                    'province' => $this->batchItem->province,
                    'city' => $this->batchItem->city,
                    'phoneNumber' => $this->batchItem->phone_number,
                    'email' => '',
                    'identificationType' => $this->batchItem->identification_type,
                    'identificationNumber' => $this->batchItem->identification_number
        ]);
    }

}
