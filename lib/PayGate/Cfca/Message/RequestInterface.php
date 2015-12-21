<?php

namespace PayGate\Cfca\Message;

interface RequestInterface
{
    public function getInstitutionId();
    public function getTxCode();
    public function getXml();
}
