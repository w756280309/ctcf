<?php

namespace PayGate\Cfca\Message;

use PayGate\Cfca\Account\IndividualAccount;
use PayGate\Cfca\CfcaUtils;
use PayGate\Cfca\Identity\IndividualIdentity;

/**
 * 绑卡
 * 向中金发起绑卡请求
 * 构造函数需要传入机构ID【中金分配给机构的ID】，以及封装过的用户身份类以及卡类
 */
class Request2531 extends AbstractRequest
{
    private $bindingSn;//绑定流水号
    private $identity;//用户身份类
    private $account;//用户卡类

    public function __construct(
        $institutionId,
        IndividualIdentity $identity,
        IndividualAccount $account
    ) {
        $this->bindingSn = CfcaUtils::generateSn();
        $this->identity = $identity;
        $this->account = $account;

        parent::__construct($institutionId, 2531);
    }

    public function getBindingSn()
    {
        return $this->bindingSn;
    }

    public function getTxSn() {
        return $this->bindingSn;
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
        <InstitutionID/>
        <TxSNBinding>{{ bindingSn }}</TxSNBinding>
        <BankID>{{ bankId }}</BankID>
        <AccountName>{{ realName }}</AccountName>
        <AccountNumber>{{ acctNo }}</AccountNumber>
        <IdentificationType>{{ idType }}</IdentificationType>
        <IdentificationNumber>{{ idNo }}</IdentificationNumber>
        <PhoneNumber>{{ mobile }}</PhoneNumber>
        <CardType>{{ acctType }}</CardType>
        <ValidDate/>
        <CVN2/>
    </Body>
</Request>
TPL;

        return CfcaUtils::renderXml($tpl, [
            'institutionId' => $this->getInstitutionId(),
            'txCode' => $this->getTxCode(),
            'bindingSn' => $this->bindingSn,
            'bankId' => $this->account->getBankId(),
            'realName' => $this->identity->getRealName(),
            'acctNo' => $this->account->getAcctNo(),
            'idType' => $this->identity->getIdType(),
            'idNo' => $this->identity->getIdNo(),
            'mobile' => $this->identity->getMobile(),
            'acctType' => $this->account->getAcctType(),
        ]);
    }
}
