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

        $this->assertEquals([
            [
                'date' => '2016-07-23',
                'principal' => $ord->order_money,
                'interest' => '2.41'
            ],
        ], OnlineRepaymentPlan::calcBenxi($ord));
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
            [
                'date' => '2016-06-13',
                'principal' => 0,
                'interest' => '0.07'
            ],
            [
                'date' => '2016-07-13',
                'principal' => 10,
                'interest' => '0.08'
            ],
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
            [
                'date' => '2016-07-27',
                'principal' => 0,
                'interest' => '0.25'
            ],
            [
                'date' => '2016-10-27',
                'principal' => 0,
                'interest' => '0.25'
            ],
            [
                'date' => '2017-01-27',
                'principal' => 0,
                'interest' => '0.25'
            ],
            [
                'date' => '2017-04-27',
                'principal' => 0,
                'interest' => '0.25'
            ],
            [
                'date' => '2017-07-27',
                'principal' => 0,
                'interest' => '0.25'
            ]
            ,
            [
                'date' => '2017-10-27',
                'principal' => 10,
                'interest' => '0.25'
            ],
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
            [
                'date' => '2016-11-12',
                'principal' => 0,
                'interest' => '0.51'
            ],
            [
                'date' => '2017-05-12',
                'principal' => 0,
                'interest' => '0.51'
            ],
            [
                'date' => '2017-11-12',
                'principal' => 0,
                'interest' => '0.51'
            ],
            [
                'date' => '2018-05-12',
                'principal' => 12,
                'interest' => '0.53'
            ],
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
            [
                'date' => '2017-04-30',
                'principal' => 0,
                'interest' => '8000'
            ],
            [
                'date' => '2018-04-30',
                'principal' => 100000,
                'interest' => '8000'
            ],
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
            [
                'date' => '2016-08-20',
                'principal' => 0,
                'interest' => '2.98'
            ],
            [
                'date' => '2016-09-20',
                'principal' => 0,
                'interest' => '4.63'
            ],
            [
                'date' => '2016-10-20',
                'principal' => 0,
                'interest' => '4.48'
            ],
            [
                'date' => '2016-10-31',
                'principal' => 1000,
                'interest' => '1.66'
            ],
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
            [
                'date' => '2016-09-28',
                'principal' => 0,
                'interest' => '162.19'
            ],
            [
                'date' => '2016-12-28',
                'principal' => 0,
                'interest' => '199.45'
            ],
            [
                'date' => '2017-03-28',
                'principal' => 0,
                'interest' => '197.26'
            ],
            [
                'date' => '2017-06-28',
                'principal' => 0,
                'interest' => '201.64'
            ],
            [
                'date' => '2017-07-16',
                'principal' => 8000,
                'interest' => '39.46'
            ],
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
            [
                'date' => '2020-06-20',
                'principal' => 0,
                'interest' => '168.76'
            ],
            [
                'date' => '2020-12-20',
                'principal' => 0,
                'interest' => '275.75'
            ],
            [
                'date' => '2021-02-28',
                'principal' => 10000,
                'interest' => '105.49'
            ],
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
            [
                'date' => '2016-12-20',
                'principal' => 0,
                'interest' => '213.97'
            ],
            [
                'date' => '2017-12-20',
                'principal' => 0,
                'interest' => '550'
            ],
            [
                'date' => '2018-12-20',
                'principal' => 0,
                'interest' => '550'
            ],
            [
                'date' => '2019-07-31',
                'principal' => 10000,
                'interest' => '336.03'
            ],
        ], OnlineRepaymentPlan::calcBenxi($ord));
    }

    public function testLeapYear()  //闰年,项目期限包含二月份(最后一期为1天，前面多出1天的情况)  优先测试
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
            [
                'date' => '2017-01-20',
                'principal' => 0,
                'interest' => '44.35'
            ],
            [
                'date' => '2017-02-20',
                'principal' => 0,
                'interest' => '45.83'
            ],
            [
                'date' => '2017-02-21',
                'principal' => 10000,
                'interest' => '1.49'
            ],
        ], OnlineRepaymentPlan::calcBenxi($ord));
    }

    public function testNegativeLixi()  //当投资金额很少的时候,最后一期利息出现负数的情况 优先测试
    {
        $loan = new OnlineProduct([    //自然方式计息
            'refund_method' => 6,
            'jixi_time' => 1469894400,
            'finish_date' => 1501430400,
            'expires' => 12,
            'paymentDay' => 20,
        ]);

        $ord = $this->getOrderMock($loan);
        $ord->order_money = 1;
        $ord->yield_rate = 0.088;

        $this->assertEquals([
            [
                'date' => '2016-08-20',
                'principal' => 0,
                'interest' => '0.00'
            ],
            [
                'date' => '2016-09-20',
                'principal' => 0,
                'interest' => '0.00'
            ],
            [
                'date' => '2016-10-20',
                'principal' => 0,
                'interest' => '0.00'
            ],
            [
                'date' => '2016-11-20',
                'principal' => 0,
                'interest' => '0.00'
            ],
            [
                'date' => '2016-12-20',
                'principal' => 0,
                'interest' => '0.00'
            ],
            [
                'date' => '2017-01-20',
                'principal' => 0,
                'interest' => '0.00'
            ],
            [
                'date' => '2017-02-20',
                'principal' => 0,
                'interest' => '0.00'
            ],
            [
                'date' => '2017-03-20',
                'principal' => 0,
                'interest' => '0.00'
            ],
            [
                'date' => '2017-04-20',
                'principal' => 0,
                'interest' => '0.00'
            ],
            [
                'date' => '2017-05-20',
                'principal' => 0,
                'interest' => '0.00'
            ],
            [
                'date' => '2017-06-20',
                'principal' => 0,
                'interest' => '0.00'
            ],
            [
                'date' => '2017-07-20',
                'principal' => 0,
                'interest' => '0.00'
            ],
            [
                'date' => '2017-07-31',
                'principal' => 1,
                'interest' => '0.09'
            ],
        ], OnlineRepaymentPlan::calcBenxi($ord));

        $product = new OnlineProduct([      //普通计息
            'refund_method' => 4,
            'jixi_time' => 1462982400,
            'finish_date' => 1526054400,
            'expires' => 24,
            'paymentDay' => null,
        ]);

        $order = $this->getOrderMock($product);
        $order->order_money = 1;
        $order->yield_rate = 0.01;

        $this->assertEquals([
            [
                'date' => '2016-11-12',
                'principal' => 0,
                'interest' => '0.00'
            ],
            [
                'date' => '2017-05-12',
                'principal' => 0,
                'interest' => '0.00'
            ],
            [
                'date' => '2017-11-12',
                'principal' => 0,
                'interest' => '0.00'
            ],
            [
                'date' => '2018-05-12',
                'principal' => 1,
                'interest' => '0.02'
            ],
        ], OnlineRepaymentPlan::calcBenxi($order));
    }

    public function testZeroLixi()  //当投资金额很少的时候,总利息正常算出来是0,应自动加0.01元  优先测试
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
        $ord->yield_rate = 0.025;

        $this->assertEquals([
            [
                'date' => '2016-07-20',
                'principal' => 0,
                'interest' => '0'
            ],
            [
                'date' => '2016-08-20',
                'principal' => 0,
                'interest' => '0'
            ],
            [
                'date' => '2016-09-19',
                'principal' => 1,
                'interest' => '0.01'
            ],
        ], OnlineRepaymentPlan::calcBenxi($ord));
    }

    //测试等额本息
    public function testDebx()
    {
        $loan = new OnlineProduct([
            'refund_method' => OnlineProduct::REFUND_METHOD_DEBX,//还款方式，等额本息
            'jixi_time' => strtotime('2017-04-01'),//计息日期
            'expires' => 2,//项目期限
        ]);
        $order = $this->getOrderMock($loan);
        $order->order_money = 1000;
        $order->yield_rate = 0.02;

        $this->assertEquals([
            [
                'date' => '2017-05-01',
                'principal' => '499.58',
                'interest' => '1.67'
            ],//['还款日', '应还本金', '应还利息'],
            [
                'date' => '2017-06-01',
                'principal' => '500.42',
                'interest' => '0.83'
            ],
        ], OnlineRepaymentPlan::calcBenxi($order));
    }

    public function testMergeRepayment()
    {
        $loan = new OnlineProduct([
            'refund_method' => 9,
            'jixi_time' => strtotime('2017-06-21'),
            'finish_date' => strtotime('2018-12-21'),
            'expires' => 18,
            'paymentDay' => 20,
            'isCustomRepayment' => true,
        ]);

        $ord = $this->getOrderMock($loan);
        $ord->order_money = 200000.00;
        $ord->yield_rate = 0.079000;

        $this->assertEquals([
            [
                'date' => '2017-12-20',
                'principal' => 0,
                'interest' => '7871.16'
            ],
            [
                'date' => '2018-12-21',
                'principal' => 200000,
                'interest' => '15828.84'
            ],
        ], OnlineRepaymentPlan::calcBenxi($ord));
    }
}