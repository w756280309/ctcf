<?php

namespace PayGate\Cfca\Settlement;

/**
 * 结算.
 */
class AccountSettlement
{
    const ACCOUNT_TYPE = 12;//企业
    private $osn;
    private $pay_id;
    private $type;
    private $amount;
    private $bank_id;
    private $pay_bank_id;

    public function __construct($osn, $pay_id, $type, $amount, $bank_id, $pay_bank_id)
    {
        $this->osn = $osn;
        $this->pay_id = $pay_id;
        $this->type = $type;
        $this->amount = $amount;
        $this->bank_id = $bank_id;
        $this->pay_bank_id = $pay_bank_id;
    }

    public function getOsn()
    {
        return $this->osn;
    }

    public function getPayId()
    {
        return $this->pay_id;
    }

    public function getType()
    {
        return $this->type;
    }
    
    public function getAmount(){
        return $this->amount;
    }
    
    public function getBankId(){
        return $this->bank_id;
    }
    
    public function getPayBankId(){
        return $this->pay_bank_id;
    }
    
    public function getAccountType(){
        return self::ACCOUNT_TYPE;
    }
}
