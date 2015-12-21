<?php

namespace PayGate\Cfca\Account;

/**
 * 个人账户.
 */
class IndividualAccount
{
    const ACCT_TYPE_DEBIT = 10; // 借记卡

    private $bankId;
    private $acctType;
    private $acctNo;

    public static function getValidAcctTypes()
    {
        return [
            self::ACCT_TYPE_DEBIT,
        ];
    }

    public function __construct($bankId, $acctType, $acctNo)
    {
        $this->bankId = $bankId;
        $this->acctType = $acctType;
        $this->acctNo = $acctNo;
    }

    public function getBankId()
    {
        return $this->bankId;
    }

    public function getAcctNo()
    {
        return $this->acctNo;
    }

    public function getAcctType()
    {
        return $this->acctType;
    }
}
