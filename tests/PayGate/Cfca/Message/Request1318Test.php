<?php

use PayGate\Cfca\Message\Request1318;

class Request1318Test extends PHPUnit_Framework_TestCase
{
    public function testCreateFromXml()
    {
        $xml1318 = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<Request version="2.0">
    <Head>
        <TxCode>1318</TxCode>
    </Head>
    <Body>
        <InstitutionID>1</InstitutionID>
        <PaymentNo>1</PaymentNo>
        <Amount>100</Amount>
        <Status>20</Status>
        <BankNotificationTime>20160101080808</BankNotificationTime>
    </Body>
</Request>
XML;

        $req = Request1318::createFromXml($xml1318);

        $this->assertEquals(1318, $req->getTxCode());
        $this->assertEquals('1', $req->getInstitutionId());
        $this->assertEquals('1', $req->getPaymentNo());
        $this->assertEquals('100', $req->getAmount());
        $this->assertEquals('20', $req->getStatus());
        $this->assertEquals('20160101080808', $req->getBankNotificationTime());

        $this->assertEquals($xml1318, $req->getXml());
    }
}
