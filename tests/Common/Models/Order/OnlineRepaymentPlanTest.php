<?php

use common\models\order\OnlineOrder;
use common\models\order\OnlineRepaymentPlan;
use common\models\product\OnlineProduct;
use Test\YiiAppTestCase;

class OnlineRepaymentPlanTest extends YiiAppTestCase
{
    private function getOrderMock(OnlineProduct $obj)
    {
        $ord = $this->getMockBuilder(OnlineOrder::class)//类名
            ->setMethods(['getLoan'])
            ->getMock(); //创建桩件

        $ord->expects($this->any())
            ->method('getLoan')
            ->will($this->returnValue($obj));

        return $ord;
    }

    public function testDaoqibenxi()   //到期本息
    {
        $loan = new OnlineProduct([
            'refund_method' => 1,
            'jixi_time' => 1467907200,
            'finish_date' => 1469203200,
            'expires' => 16,
            'paymentDay' => null,
        ]);

        $ord = $this->getOrderMock($loan);
        $ord->order_money = 1000;
        $ord->yield_rate = 0.055;

        $this->assertEquals(['2016-07-23', $ord->order_money, '2.41'], OnlineRepaymentPlan::calcBenxi($ord));
    }

    public function testMoneth()    //按月计息
    {
        $loan = new OnlineProduct([
            'refund_method' => 2,
            'jixi_time' => 1463068800,
            'finish_date' => 1468339200,
            'expires' => 2,
            'paymentDay' => null,
        ]);

        $ord = $this->getOrderMock($loan);
        $ord->order_money = 10;
        $ord->yield_rate = 0.09;

        $this->assertEquals([
            0 => ['2016-06-13', 0, '0.08'],
            1 => ['2016-07-13', 10, '0.07'],
        ], OnlineRepaymentPlan::calcBenxi($ord));
    }

    public function testQuarter()   //按季计息
    {
        $loan = new OnlineProduct([
            'refund_method' => 3,
            'jixi_time' => 1461686400,
            'finish_date' => 1509033600,
            'expires' => 18,
            'paymentDay' => null,
        ]);

        $ord = $this->getOrderMock($loan);
        $ord->order_money = 10;
        $ord->yield_rate = 0.1;

        $this->assertEquals([
            0 => ['2016-07-27', 0, '0.25'],
            1 => ['2016-10-27', 0, '0.25'],
            2 => ['2017-01-27', 0, '0.25'],
            3 => ['2017-04-27', 0, '0.25'],
            4 => ['2017-07-27', 0, '0.25'],
            5 => ['2017-10-27', 10, '0.25'],
        ], OnlineRepaymentPlan::calcBenxi($ord));
    }

    public function testHalfYear()  //按半年计息
    {
        $loan = new OnlineProduct([
            'refund_method' => 4,
            'jixi_time' => 1462982400,
            'finish_date' => 1526054400,
            'expires' => 24,
            'paymentDay' => null,
        ]);

        $ord = $this->getOrderMock($loan);
        $ord->order_money = 12;
        $ord->yield_rate = 0.086;

        $this->assertEquals([
            0 => ['2016-11-12', 0, '0.52'],
            1 => ['2017-05-12', 0, '0.52'],
            2 => ['2017-11-12', 0, '0.52'],
            3 => ['2018-05-12', 12, '0.50'],
        ], OnlineRepaymentPlan::calcBenxi($ord));
    }

    public function testYear()  //按年计息
    {
        $loan = new OnlineProduct([
            'refund_method' => 5,
            'jixi_time' => 1461945600,
            'finish_date' => 1525017600,
            'expires' => 24,
            'paymentDay' => null,
        ]);

        $ord = $this->getOrderMock($loan);
        $ord->order_money = 100000;
        $ord->yield_rate = 0.08;

        $this->assertEquals([
            0 => ['2017-04-30', 0, '8000'],
            1 => ['2018-04-30', 100000, '8000'],
        ], OnlineRepaymentPlan::calcBenxi($ord));
    }

    public function testNatureMonth()   //按自然月计息
     {
        $loan = new OnlineProduct([
            'refund_method' => 6,
            'jixi_time' => 1469894400,
            'finish_date' => 1477843200,
            'expires' => 3,
            'paymentDay' => 20,
        ]);

        $ord = $this->getOrderMock($loan);
        $ord->order_money = 1000;
        $ord->yield_rate = 0.055;

        $this->assertEquals([
            0 => ['2016-08-20', 0, '2.99'],
            1 => ['2016-09-20', 0, '4.63'],
            2 => ['2016-10-20', 0, '4.48'],
            3 => ['2016-10-31', 1000, '1.65'],
        ], OnlineRepaymentPlan::calcBenxi($ord));
    }

    public function testNatureQuarter()   //按自然季计息
    {
        $loan = new OnlineProduct([
            'refund_method' => 7,
            'jixi_time' => 1468598400,
            'finish_date' => 1500134400,
            'expires' => 12,
            'paymentDay' => 28,
        ]);

        $ord = $this->getOrderMock($loan);
        $ord->order_money = 8000;
        $ord->yield_rate = 0.1;

        $this->assertEquals([
            0 => ['2016-09-28', 0, '162.19'],
            1 => ['2016-12-28', 0, '199.45'],
            2 => ['2017-03-28', 0, '197.26'],
            3 => ['2017-06-28', 0, '201.64'],
            4 => ['2017-07-16', 8000, '39.46'],
        ], OnlineRepaymentPlan::calcBenxi($ord));
    }

    public function testNatureHalfYear()    //按自然半年计息
    {
        $loan = new OnlineProduct([
            'refund_method' => 8,
            'jixi_time' => 1582905600,
            'finish_date' => 1614441600,
            'expires' => 12,
            'paymentDay' => 20,
        ]);

        $ord = $this->getOrderMock($loan);
        $ord->order_money = 10000;
        $ord->yield_rate = 0.055;

        $this->assertEquals([
            0 => ['2020-06-20', 0, '168.77'],
            1 => ['2020-12-20', 0, '275.75'],
            2 => ['2021-02-28', 10000, '105.48'],
        ], OnlineRepaymentPlan::calcBenxi($ord));
    }

    public function testNatureYear()    //按自然年计息
    {
        $loan = new OnlineProduct([
            'refund_method' => 9,
            'jixi_time' => 1469894400,
            'finish_date' => 1564502400,
            'expires' => 36,
            'paymentDay' => 20,
        ]);

        $ord = $this->getOrderMock($loan);
        $ord->order_money = 10000;
        $ord->yield_rate = 0.055;

        $this->assertEquals([
            0 => ['2016-12-20', 0, '213.97'],
            1 => ['2017-12-20', 0, '550.00'],
            2 => ['2018-12-20', 0, '550.00'],
            3 => ['2019-07-31', 10000, '336.03'],
        ], OnlineRepaymentPlan::calcBenxi($ord));
    }

    public function testLeapYear()  //闰年,项目期限包含二月份(最后一期为1天，前面还了所有利息的情况)  优先测试
    {
        $loan = new OnlineProduct([
            'refund_method' => 6,
            'jixi_time' => 1482249600,
            'finish_date' => 1487606400,
            'expires' => 2,
            'paymentDay' => 20,
        ]);

        $ord = $this->getOrderMock($loan);
        $ord->order_money = 10000;
        $ord->yield_rate = 0.055;

        $this->assertEquals([
            0 => ['2017-01-20', 0, '44.36'],
            1 => ['2017-02-20', 0, '45.84'],
            2 => ['2017-02-21', 10000, '1.47'],
        ], OnlineRepaymentPlan::calcBenxi($ord));
    }

    public function testNegativeLixi()  //当投资金额很少的时候,最后一期利息出现负数,当抛异常 优先测试
    {
        $loan = new OnlineProduct([
            'refund_method' => 6,
            'jixi_time' => 1469894400,
            'finish_date' => 1501430400,
            'expires' => 12,
            'paymentDay' => 20,
        ]);

        $ord = $this->getOrderMock($loan);
        $ord->order_money = 1;
        $ord->yield_rate = 0.088;

        $this->assertEquals($this->expectException(\Exception::class), OnlineRepaymentPlan::calcBenxi($ord));
    }

    public function testZeroLixi()  //当投资金额很少的时候,利息正常算出来是0,应自动加0.01元  优先测试
    {
        $loan = new OnlineProduct([
            'refund_method' => 6,
            'jixi_time' => 1468857600,
            'finish_date' => 1474214400,
            'expires' => 2,
            'paymentDay' => 20,
        ]);

        $ord = $this->getOrderMock($loan);
        $ord->order_money = 1;
        $ord->yield_rate = 0.1;

        $this->assertEquals([
            0 => ['2016-07-20', 0, '0.01'],   //实际应为0,额外增加0.01元
            1 => ['2016-08-20', 0, '0.01'],
            2 => ['2016-09-19', 1, '0.01'],   //利息最后本为0,额外加了0.01元
        ], OnlineRepaymentPlan::calcBenxi($ord));
    }
}