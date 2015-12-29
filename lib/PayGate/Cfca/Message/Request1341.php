<?php

namespace PayGate\Cfca\Message;

use Yii;
use PayGate\Cfca\CfcaUtils;
use PayGate\Cfca\Settlement\AccountSettlement;

/**
 * 结算
 * 向中金发起结算请求【定时任务，筛选成功支付的尚未发起结算的充值记录】
 * 构造函数需要传入机构ID【中金分配给机构的ID】，以及封装过的充值类
 */
class Request1341 extends AbstractRequest {

    const BANK_ID = '424';//银行id，对照中金提供的银行编码
    const ACCOUNT_NAME = '温都金服';//账户名
    const BRANCH_NAME = '南京银行鸿信大厦支行';//分支行信息
    const ACCOUNT_NUMBER = '017601205400022';//卡号
    const PROVINCE = '江苏省';//分支行所属省份
    const CITY = '南京市';//分支行所属市

    private $settlementSn;//结算号
    private $settlement;//结算类【封装过的充值对象】

    public function __construct(
    $institutionId, AccountSettlement $settlement
    ) {
        $this->settlementSn = CfcaUtils::generateSn("S");
        $this->settlement = $settlement;
        parent::__construct($institutionId, 1341);
    }

    /**
     * 结算号
     * @return string【格式：时间精度毫秒级+4位随机数】
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

    /**
     * 获取结算对象
     * @return type
     */
    public function getSettlement() {
        return $this->settlement;
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
        <SerialNumber>{{ sn }}</SerialNumber>
	<OrderNo>{{ orderNo }}</OrderNo>
	<Amount>{{ amount }}</Amount>
	<Remark>{{ remark }}</Remark>
	<AccountType>{{ accountType }}</AccountType>
	<PaymentAccountName></PaymentAccountName>
	<PaymentAccountNumber></PaymentAccountNumber>
	<BankAccount>
	<BankID>{{ bankID }}</BankID>
	<AccountName>{{ accountName }}</AccountName>
	<AccountNumber>{{ accountNumber }}</AccountNumber>
	<BranchName>{{ branchName }}</BranchName>
	<Province>{{ province }}</Province>
	<City>{{ city }}</City>
	</BankAccount>
    </Body>
</Request>
TPL;
        $recharge = $this->settlement->getRecharge();
        return CfcaUtils::renderXml($tpl, [
                    'institutionId' => $this->getInstitutionId(),
                    'txCode' => $this->getTxCode(),
                    'sn' => $this->settlementSn,
                    'orderNo' => $recharge->sn,
                    'amount' => $recharge->fund * 100,//中金以分计算
                    'remark' => '',
                    'accountType' => $this->settlement->getAccountType(),
                    'bankID' => self::BANK_ID,
                    'accountName' => self::ACCOUNT_NAME,
                    'accountNumber' => self::ACCOUNT_NUMBER,
                    'branchName' => self::BRANCH_NAME,
                    'province' => self::PROVINCE,
                    'city' => self::CITY
        ]);
    }

}
