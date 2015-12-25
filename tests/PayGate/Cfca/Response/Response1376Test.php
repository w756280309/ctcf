<?php

use PayGate\Cfca\Response\Response1376;

class Response1376Test extends PHPUnit_Framework_TestCase
{
    public function testSuccess()
    {
        $xml = file_get_contents(dirname(__DIR__).'/res/response_1376.xml');
        $resp = new Response1376($xml);

        // 检查各变量的值
        $this->assertEquals('1', $resp->getInstitutionId());
        $this->assertEquals('ORDER_NO', $resp->getOrderNo());
        $this->assertEquals('PAYMENT_NO', $resp->getPaymentNo());
        $this->assertEquals(40, $resp->getVerifyStatus());
        $this->assertEquals(20, $resp->getPaymentStatus());
        $this->assertEquals('20151225091847', $resp->getBankTxTime());

        // 检查40/20的状态组合下，isSuccess()返回是true
        $this->assertTrue($resp->isSuccess());

        // 检查json
        $this->assertEquals(
            '{"institutionId":"1","code":2000,"message":"MESSAGE","orderNo":"ORDER_NO","paymentNo":"PAYMENT_NO","verifyStatus":40,"paymentStatus":20,"bankTxTime":"20151225091847"}',
            json_encode($resp)
        );
    }
}
