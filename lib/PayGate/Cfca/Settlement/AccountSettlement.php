<?php

namespace PayGate\Cfca\Settlement;

/**
 * 结算【只对充值做结算】.
 */
class AccountSettlement
{
    const ACCOUNT_TYPE = 12;//企业
    private $recharge;

    public function __construct($recharge, $type = 1)
    {
        $this->type = $type;
        $this->recharge = $recharge;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getRecharge()
    {
        return $this->recharge;
    }

    public function getAccountType()
    {
        return self::ACCOUNT_TYPE;
    }
}
