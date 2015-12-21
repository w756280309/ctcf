<?php

use PayGate\Cfca\CfcaUtils;

class CfcaUtilsTest extends PHPUnit_Framework_TestCase
{
    public function testSnLenWithPrefix()
    {
        $this->assertEquals(22, strlen(CfcaUtils::generateSn()));
    }

    public function testSnLenWithoutPrefix()
    {
        $this->assertEquals(26, strlen(CfcaUtils::generateSn('cfca')));
    }

    public function testSnAllDigits()
    {
        $this->assertRegExp('/^\d+$/', CfcaUtils::generateSn());
        $this->assertRegExp('/^\d+$/', CfcaUtils::generateSn('1'));
    }
}
