<?php

namespace Test\Common\Models\Product;

use common\models\product\OnlineProduct;
use Test\YiiAppTestCase;
use yii\base\Exception;

class OnlineProductTest extends YiiAppTestCase
{
    /**
     * 测试没有计息时间标的
     * @expectedException Exception
     */
    public function testPaymentDates1()
    {
        $loan = new OnlineProduct([
            'jixi_time' => 0,
        ]);
        $loan->getPaymentDates();
    }

    /**
     * 测试   没有截止日期的到期本息
     */
    public function testPaymentDates2()
    {
        $loan = new OnlineProduct([
            'jixi_time' => strtotime('2016-01-02'),
            'expires' => 12,
            'refund_method' => 1,
        ]);
        $res = $loan->getPaymentDates();
        $this->assertEquals($res, ['2016-01-14']);
    }

    /**
     * 测试   有截止日期的到期本息
     */
    public function testPaymentDates3()
    {
        $loan = new OnlineProduct([
            'jixi_time' => strtotime('2016-06-08'),
            'expires' => 2,
            'refund_method' => 1,
            'finish_date' => strtotime('2016-06-09'),
        ]);
        $res = $loan->getPaymentDates();
        $this->assertEquals($res, ['2016-06-09']);
    }

    /**
     * 测试   按月付息，到期本息
     */
    public function testPaymentDates4()
    {
        $loan = new OnlineProduct([
            'jixi_time' => strtotime('2016-07-06'),
            'expires' => 2,
            'refund_method' => 2,
        ]);
        $res = $loan->getPaymentDates();
        $this->assertEquals($res, ['2016-08-06', '2016-09-06']);
    }

    /**
     * 测试   按季付息，到期本息, 不到一季
     */
    public function testPaymentDates5()
    {
        $loan = new OnlineProduct([
            'jixi_time' => strtotime('2016-07-06'),
            'expires' => 2,
            'refund_method' => 3,
        ]);
        $res = $loan->getPaymentDates();
        $this->assertEquals($res, ['2016-09-06']);
    }

    /**
     * 测试   按季付息，到期本息, 超过一季，不足两季
     */
    public function testPaymentDates6()
    {
        $loan = new OnlineProduct([
            'jixi_time' => strtotime('2016-07-06'),
            'expires' => 4,
            'refund_method' => 3,
        ]);
        $res = $loan->getPaymentDates();
        $this->assertEquals($res, ['2016-10-06', '2016-11-06']);
    }

    /**
     * 测试   按半年付息，到期本息
     */
    public function testPaymentDates7()
    {
        $loan = new OnlineProduct([
            'jixi_time' => strtotime('2016-07-06'),
            'expires' => 6,
            'refund_method' => 4,
        ]);
        $res = $loan->getPaymentDates();
        $this->assertEquals($res, ['2017-01-06']);
    }

    /**
     * 测试   按年付息，到期本息
     */
    public function testPaymentDates8()
    {
        $loan = new OnlineProduct([
            'jixi_time' => strtotime('2016-07-06'),
            'expires' => 12,
            'refund_method' => 5,
        ]);
        $res = $loan->getPaymentDates();
        $this->assertEquals($res, ['2017-07-06']);
    }

    /**
     * 测试   按月付息，到期本息 月底计算
     */
    public function testPaymentDates9()
    {
        $loan = new OnlineProduct([
            'jixi_time' => strtotime('2016-01-30'),
            'expires' => 1,
            'refund_method' => 2,
        ]);
        $res = $loan->getPaymentDates();
        $this->assertEquals($res, ['2016-02-29']);
    }

    /**
     * 测试   按自然月付息，到期本息
     */
    public function testPaymentDates10()
    {
        $loan = new OnlineProduct([
            'jixi_time' => strtotime('2016-07-06'),
            'expires' => 2,
            'refund_method' => 6,
            'paymentDay' => 20,
        ]);
        $res = $loan->getPaymentDates();
        $this->assertEquals($res, ['2016-07-20', '2016-08-20', '2016-09-06']);
    }

    /**
     * 测试   按自然月付息，到期本息
     */
    public function testPaymentDates11()
    {
        $loan = new OnlineProduct([
            'jixi_time' => strtotime('2016-07-20'),
            'expires' => 2,
            'refund_method' => 6,
            'paymentDay' => 20,
        ]);
        $res = $loan->getPaymentDates();
        $this->assertEquals($res, ['2016-08-20', '2016-09-20']);
    }

    /**
     * 测试   按自然月付息，到期本息
     */
    public function testPaymentDates12()
    {
        $loan = new OnlineProduct([
            'jixi_time' => strtotime('2016-07-21'),
            'expires' => 2,
            'refund_method' => 6,
            'paymentDay' => 20,
        ]);
        $res = $loan->getPaymentDates();
        $this->assertEquals($res, ['2016-08-20', '2016-09-20', '2016-09-21']);
    }

    /**
     * 测试   按自然季度付息，到期本息
     */
    public function testPaymentDates13()
    {
        $loan = new OnlineProduct([
            'jixi_time' => strtotime('2016-07-09'),
            'expires' => 4,
            'refund_method' => 7,
            'paymentDay' => 20,
        ]);
        $res = $loan->getPaymentDates();
        $this->assertEquals($res, ['2016-09-20', '2016-11-09']);
    }

    /**
     * 测试   按自然季度付息，到期本息
     */
    public function testPaymentDates14()
    {
        $loan = new OnlineProduct([
            'jixi_time' => strtotime('2016-09-21'),
            'expires' => 4,
            'refund_method' => 7,
            'paymentDay' => 20,
        ]);
        $res = $loan->getPaymentDates();
        $this->assertEquals($res, ['2016-12-20', '2017-01-21']);
    }

    /**
     * 测试   按自然季度付息，到期本息
     */
    public function testPaymentDates15()
    {
        $loan = new OnlineProduct([
            'jixi_time' => strtotime('2016-06-20'),
            'expires' => 6,
            'refund_method' => 7,
            'paymentDay' => 20,
        ]);
        $res = $loan->getPaymentDates();
        $this->assertEquals($res, ['2016-09-20', '2016-12-20']);
    }

    /**
     * 测试   按自然半年付息，到期本息
     */
    public function testPaymentDates16()
    {
        $loan = new OnlineProduct([
            'jixi_time' => strtotime('2016-06-15'),
            'expires' => 6,
            'refund_method' => 8,
            'paymentDay' => 20,
        ]);
        $res = $loan->getPaymentDates();
        $this->assertEquals($res, ['2016-06-20', '2016-12-15']);
    }

    /**
     * 测试   按自然年付息，到期本息
     */
    public function testPaymentDates17()
    {
        $loan = new OnlineProduct([
            'jixi_time' => strtotime('2016-06-15'),
            'expires' => 13,
            'refund_method' => 9,
            'paymentDay' => 20,
        ]);
        $res = $loan->getPaymentDates();
        $this->assertEquals($res, ['2016-12-20', '2017-07-15']);
    }

    /**
     * 测试   按月付息，到期本息
     */
    public function testPaymentDates18()
    {
        $loan = new OnlineProduct([
            'jixi_time' => strtotime('2016-07-06'),
            'expires' => 3,
            'refund_method' => 2,
        ]);
        $res = $loan->getPaymentDates();
        $this->assertEquals($res, ['2016-08-06', '2016-09-06', '2016-10-06']);
    }

    /**
     * 测试   按年付息，到期本息
     */
    public function testPaymentDates19()
    {
        $loan = new OnlineProduct([
            'jixi_time' => strtotime('2016-07-06'),
            'expires' => 36,
            'refund_method' => 5,
        ]);
        $res = $loan->getPaymentDates();
        $this->assertEquals($res, ['2017-07-06', '2018-07-06', '2019-07-06']);
    }

    /**
     * 测试   按季付息，到期本息, 超过两季
     */
    public function testPaymentDates20()
    {
        $loan = new OnlineProduct([
            'jixi_time' => strtotime('2016-07-06'),
            'expires' => 9,
            'refund_method' => 3,
        ]);
        $res = $loan->getPaymentDates();
        $this->assertEquals($res, ['2016-10-06', '2017-01-06', '2017-04-06']);
    }
}