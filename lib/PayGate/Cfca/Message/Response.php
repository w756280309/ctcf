<?php

namespace PayGate\Cfca\Message;

use SimpleXMLElement;

class Response
{
    private $text;
    private $xmlObj;
    private $code;
    private $message;

    public function __construct($text)
    {
        $this->text = $text;

        try {
            $xmlObj = new SimpleXMLElement($this->text);
            $this->xmlObj = $xmlObj;

            $nodes = $this->xmlObj->xpath('/Response/Head/Code/text()');
            if (1 === count($nodes)) {
                $this->code = (int) trim((string) $nodes[0]);
            }

            $nodes = $this->xmlObj->xpath('/Response/Head/Message/text()');
            if (1 === count($nodes)) {
                $this->message = (string) $nodes[0];
            }
        } catch (\Exception $ex) {
            throw new \RuntimeException('XML parsing failed.', 0, $ex);
        }
    }

    public function getText()
    {
        return $this->text;
    }

    public function getXmlObj()
    {
        return $this->xmlObj;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function isSuccess()
    {
        return 2000 === $this->code;
    }
}
