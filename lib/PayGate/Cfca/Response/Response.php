<?php

namespace PayGate\Cfca\Response;

use JsonSerializable;
use SimpleXMLElement;

abstract class Response implements JsonSerializable
{
    protected $xml;
    protected $xmlObj;
    protected $institutionId;
    protected $code = 2000;
    protected $message;

    abstract protected function populate();
    abstract public function getSerializationData();

    public function __construct($xml)
    {
        $this->xml = $xml;
        $this->xmlObj = new SimpleXMLElement($this->xml);

        $this->institutionId = (string) $this->xmlObj->Body->InstitutionID;
        $this->message = (string) $this->xmlObj->Head->Message;
        $this->populate();
    }
    
    public function jsonSerialize()
    {
        return array_merge([
            'institutionId' => $this->institutionId,
            'code' => $this->code,
            'message' => $this->message,
        ], $this->getSerializationData());
    }
    
    public function getXml()
    {
        return $this->xml;
    }

    public function getXmlObj()
    {
        return $this->xmlObj;
    }

    public function getInstitutionId()
    {
        return $this->institutionId;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getMessage()
    {
        return $this->message;
    }
}
