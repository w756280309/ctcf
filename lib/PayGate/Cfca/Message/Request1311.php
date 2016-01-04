<?php

namespace PayGate\Cfca\Message;

use PayGate\Cfca\CfcaUtils;

/**
 * 中金接口 用于发起PC端大额充值
 */
class Request1311 extends AbstractRequest {

    const TRADEDESC = '大额充值';  //交易描述信息

    private $recharge, $account_type;

    /**
     * 构造函数
     * @param $institutionId 机构编号
     * @param $recharge 充值类对象
     * @param $account_type 付款方账户类型 11个人 12企业
     */
    public function __construct(
    $institutionId, $recharge, $account_type
    ) {
        $this->recharge = $recharge;
        $this->account_type = $account_type;
        parent::__construct($institutionId, 1311);
    }

    public function getRechargeSn() {
        return $this->recharge->sn;
    }

    public function getTxSn() {
        return $this->recharge->sn;
    }

    public function getXml() {
        $tpl = <<<TPL
<?xml version="1.0" encoding="UTF-8"?>
<Request version="2.0">
    <Head>
        <TxCode>{{ TxCode }}</TxCode>
    </Head>
    <Body>
        <InstitutionID>{{ InstitutionID }}</InstitutionID>
        <OrderNo>{{ OrderNo }}</OrderNo>
        <PaymentNo>{{ PaymentNo }}</PaymentNo>
        <Amount>{{ Amount }}</Amount>
        <Fee>{{ Fee }}</Fee>
        <PayerID></PayerID>
        <PayerName></PayerName>
        <Usage>{{ Usage }}</Usage>
        <Remark>{{ Remark }}</Remark>
        <NotificationURL>{{ NotificationURL }}</NotificationURL>
        <PayeeList></PayeeList>
        <Payee></Payee>
        <PayeeList></PayeeList>
        <BankID>{{ BankID }}</BankID>
        <AccountType>{{ AccountType }}</AccountType>
    </Body>
</Request>
TPL;
        return CfcaUtils::renderXml($tpl, [
                    'TxCode' => $this->getTxCode(),
                    'InstitutionID' => $this->getInstitutionId(),
                    'OrderNo' => $this->recharge->sn,
                    'PaymentNo' => $this->recharge->sn,
                    'Amount' => $this->recharge->fund * 100, //中金以分计算
                    'Fee' => 0,
                    'Usage' => self::TRADEDESC,
                    'Remark' => self::TRADEDESC,
                    'NotificationURL' => \Yii::$app->params['main_url'] . '/user/recharge/rechargecallback',
                    'BankID' => $this->recharge->pay_bank_id,
                    'AccountType' => $this->account_type
        ]);
    }

}
