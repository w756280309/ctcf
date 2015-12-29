<?php

namespace PayGate\Cfca\Message;

abstract class AbstractRequest implements RequestInterface
{
    protected $institutionId;
    protected $txCode;

    public function __construct($institutionId, $txCode)
    {
        $this->institutionId = $institutionId;
        $this->txCode = $txCode;
    }

    public function getInstitutionId()
    {
        return $this->institutionId;
    }

    public function getTxCode()
    {
        return $this->txCode;
    }

    abstract public function getXml();
    abstract public function getTxSn();
}
