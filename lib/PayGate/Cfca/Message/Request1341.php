<?php

namespace PayGate\Cfca\Message;

use Yii;
use PayGate\Cfca\CfcaUtils;
use PayGate\Cfca\Settlement\AccountSettlement;

class Request1341 extends AbstractRequest
{
    const BANK_ID = '424';
    const ACCOUNT_NAME = '温都金服';
    const BRANCH_NAME = '南京银行鸿信大厦支行';
    const ACCOUNT_NUMBER = '017601205400022';
    const PROVINCE = '江苏省';
    const CITY = '南京市';
    
    private $bindingSn;
    private $settlement;

    public function __construct(
        $institutionId,AccountSettlement $settlement
    )
    {
        $this->bindingSn = CfcaUtils::generateSn("S");
        $this->settlement = $settlement;
        parent::__construct($institutionId, 1341);
    }

    public function getBindingSn()
    {
        return $this->bindingSn;
    }
    
    public function getSettlement(){
        return $this->settlement;
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
	<OrderNo>{{ orderNo }}</OrderNo/>  
	<Amount>{{ amount }}</Amount/>    
	<Remark>{{ remark }}</Remark/>  
	<AccountType>{{ accountType }}</AccountType/>  
	<PaymentAccountName></PaymentAccountName/>  
	<PaymentAccountNumber></PaymentAccountNumber/>  
	<BankAccount>  
	<BankID>{{ bankID }}</BankID/> 
	<AccountName>{{ accountName }}</AccountName/>  
	<AccountNumber>{{ accountNumber }}</AccountNumber/>  
	<BranchName>{{ branchName }}</BranchName/>  
	<Province>{{ province }}</Province/>  
	<City>{{ city }}</City/> 
	</BankAccount>
    </Body>
</Request>
TPL;

        return CfcaUtils::renderXml($tpl, [
            'institutionId' => $this->getInstitutionId(),
            'txCode' => $this->getTxCode(),
            'sn' => $this->bindingSn,'orderNo' => $this->settlement->getOsn(),
            'amount' => $this->settlement->getAmount(),'remark' => '','accountType' => $this->settlement->getAccountType(),'bankID' => self::BANK_ID,
            'accountName' => self::ACCOUNT_NAME,'accountNumber' => self::ACCOUNT_NUMBER,'branchName' => self::BRANCH_NAME,
            'province' => self::PROVINCE,'city' => self::CITY
        ]);
    }
}
