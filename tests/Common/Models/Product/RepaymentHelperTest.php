<?php

namespace Test\Common\Models\Product;

use common\models\product\RepaymentHelper;
use Test\YiiAppTestCase;

class RepaymentHelperTest extends YiiAppTestCase
{
    public function testDaoqibenxi()   //到期本息
    {
        $startDate = '2017-07-08';
        $endDate = '2017-07-23';
        $repaymentMethod = 1;
        $duration = 16;
        $paymentDay = null;
        $isCustomRepayment = false;
        $amount = 1000;
        $apr = 0.055;
        $plan = RepaymentHelper::calcRepaymentPlan($startDate, $endDate, $repaymentMethod, $duration, $paymentDay, $isCustomRepayment, $amount, $apr);

        $this->assertEquals([
            [
                'date' => '2017-07-23',
                'principal' => '1000.00',
                'interest' => '2.41'
            ],
        ], $plan);
    }

    public function testMoneth()    //按月计息
    {
        $startDate = '2016-05-13';
        $endDate = '2016-07-13';
        $repaymentMethod = 2;
        $duration = 2;
        $paymentDay = null;
        $isCustomRepayment = false;
        $amount = 10;
        $apr = 0.09;
        $plan = RepaymentHelper::calcRepaymentPlan($startDate, $endDate, $repaymentMethod, $duration, $paymentDay, $isCustomRepayment, $amount, $apr);

        $this->assertEquals([
            [
                'date' => '2016-06-13',
                'principal' => '0.00',
                'interest' => '0.07'
            ],
            [
                'date' => '2016-07-13',
                'principal' => '10.00',
                'interest' => '0.08'
            ],
        ], $plan);
    }

    public function testQuarter()   //按季计息
    {
        $startDate = '2016-04-27';
        $endDate = '2017-10-27';
        $repaymentMethod = 3;
        $duration = 18;
        $paymentDay = null;
        $isCustomRepayment = false;
        $amount = 10;
        $apr = 0.1;
        $plan = RepaymentHelper::calcRepaymentPlan($startDate, $endDate, $repaymentMethod, $duration, $paymentDay, $isCustomRepayment, $amount, $apr);

        $this->assertEquals([
            [
                'date' => '2016-07-27',
                'principal' => '0.00',
                'interest' => '0.25'
            ],
            [
                'date' => '2016-10-27',
                'principal' => '0.00',
                'interest' => '0.25'
            ],
            [
                'date' => '2017-01-27',
                'principal' => '0.00',
                'interest' => '0.25'
            ],
            [
                'date' => '2017-04-27',
                'principal' => '0.00',
                'interest' => '0.25'
            ],
            [
                'date' => '2017-07-27',
                'principal' => '0.00',
                'interest' => '0.25'
            ],
            [
                'date' => '2017-10-27',
                'principal' => '10.00',
                'interest' => '0.25'
            ],
        ], $plan);
    }

    public function testHalfYear()  //按半年计息
    {
        $startDate = '2016-05-12';
        $endDate = '2018-05-12';
        $repaymentMethod = 4;
        $duration = 24;
        $paymentDay = null;
        $isCustomRepayment = false;
        $amount = 12;
        $apr = 0.086;
        $plan = RepaymentHelper::calcRepaymentPlan($startDate, $endDate, $repaymentMethod, $duration, $paymentDay, $isCustomRepayment, $amount, $apr);

        $this->assertEquals([
            [
                'date' => '2016-11-12',
                'principal' => '0.00',
                'interest' => '0.51'
            ],
            [
                'date' => '2017-05-12',
                'principal' => '0.00',
                'interest' => '0.51'
            ],
            [
                'date' => '2017-11-12',
                'principal' => '0.00',
                'interest' => '0.51'
            ],
            [
                'date' => '2018-05-12',
                'principal' => '12.00',
                'interest' => '0.53'
            ],
        ], $plan);
    }

    public function testYear()  //按年计息
    {
        $startDate = '2016-04-30';
        $endDate = '2018-04-30';
        $repaymentMethod = 5;
        $duration = 24;
        $paymentDay = null;
        $isCustomRepayment = false;
        $amount = 100000;
        $apr = 0.08;
        $plan = RepaymentHelper::calcRepaymentPlan($startDate, $endDate, $repaymentMethod, $duration, $paymentDay, $isCustomRepayment, $amount, $apr);

        $this->assertEquals([
            [
                'date' => '2017-04-30',
                'principal' => '0.00',
                'interest' => '8000.00'
            ],
            [
                'date' => '2018-04-30',
                'principal' => '100000.00',
                'interest' => '8000.00'
            ],
        ], $plan);
    }

    public function testNatureMonth()   //按自然月计息
    {
        $startDate = '2016-07-31';
        $endDate = '2016-10-31';
        $repaymentMethod = 6;
        $duration = 3;
        $paymentDay = 20;
        $isCustomRepayment = false;
        $amount = 1000;
        $apr = 0.055;
        $plan = RepaymentHelper::calcRepaymentPlan($startDate, $endDate, $repaymentMethod, $duration, $paymentDay, $isCustomRepayment, $amount, $apr);

        $this->assertEquals([
            [
                'date' => '2016-08-20',
                'principal' => '0.00',
                'interest' => '2.98'
            ],
            [
                'date' => '2016-09-20',
                'principal' => '0.00',
                'interest' => '4.63'
            ],
            [
                'date' => '2016-10-20',
                'principal' => '0.00',
                'interest' => '4.48'
            ],
            [
                'date' => '2016-10-31',
                'principal' => '1000.00',
                'interest' => '1.66'
            ],
        ], $plan);
    }

    public function testNatureQuarter()   //按自然季计息
    {
        $startDate = '2016-07-16';
        $endDate = '2017-07-16';
        $repaymentMethod = 7;
        $duration = 12;
        $paymentDay = 28;
        $isCustomRepayment = false;
        $amount = 8000;
        $apr = 0.1;
        $plan = RepaymentHelper::calcRepaymentPlan($startDate, $endDate, $repaymentMethod, $duration, $paymentDay, $isCustomRepayment, $amount, $apr);

        $this->assertEquals([
            [
                'date' => '2016-09-28',
                'principal' => '0.00',
                'interest' => '162.19'
            ],
            [
                'date' => '2016-12-28',
                'principal' => '0.00',
                'interest' => '199.45'
            ],
            [
                'date' => '2017-03-28',
                'principal' => '0.00',
                'interest' => '197.26'
            ],
            [
                'date' => '2017-06-28',
                'principal' => '0.00',
                'interest' => '201.64'
            ],
            [
                'date' => '2017-07-16',
                'principal' => '8000.00',
                'interest' => '39.46'
            ],
        ], $plan);
    }

    public function testNatureHalfYear()    //按自然半年计息
    {
        $startDate = '2020-02-29';
        $endDate = '2021-02-28';
        $repaymentMethod = 8;
        $duration = 12;
        $paymentDay = 20;
        $isCustomRepayment = false;
        $amount = 10000;
        $apr = 0.055;
        $plan = RepaymentHelper::calcRepaymentPlan($startDate, $endDate, $repaymentMethod, $duration, $paymentDay, $isCustomRepayment, $amount, $apr);

        $this->assertEquals([
            [
                'date' => '2020-06-20',
                'principal' => '0.00',
                'interest' => '168.76'
            ],
            [
                'date' => '2020-12-20',
                'principal' => '0.00',
                'interest' => '275.75'
            ],
            [
                'date' => '2021-02-28',
                'principal' => '10000.00',
                'interest' => '105.49'
            ],
        ], $plan);
    }

    public function testNatureYear()    //按自然年计息
    {
        $startDate = '2016-07-31';
        $endDate = '2019-07-31';
        $repaymentMethod = 9;
        $duration = 36;
        $paymentDay = 20;
        $isCustomRepayment = false;
        $amount = 10000;
        $apr = 0.055;
        $plan = RepaymentHelper::calcRepaymentPlan($startDate, $endDate, $repaymentMethod, $duration, $paymentDay, $isCustomRepayment, $amount, $apr);

        $this->assertEquals([
            [
                'date' => '2016-12-20',
                'principal' => '0.00',
                'interest' => '213.97'
            ],
            [
                'date' => '2017-12-20',
                'principal' => '0.00',
                'interest' => '550.00'
            ],
            [
                'date' => '2018-12-20',
                'principal' => '0.00',
                'interest' => '550.00'
            ],
            [
                'date' => '2019-07-31',
                'principal' => '10000.00',
                'interest' => '336.03'
            ],
        ], $plan);
    }

    public function testLeapYear()  //闰年,项目期限包含二月份(最后一期为1天，前面多出1天的情况)  优先测试
    {

        $startDate = '2016-12-21';
        $endDate = '2017-02-21';
        $repaymentMethod = 6;
        $duration = 2;
        $paymentDay = 20;
        $isCustomRepayment = false;
        $amount = 10000;
        $apr = 0.055;
        $plan = RepaymentHelper::calcRepaymentPlan($startDate, $endDate, $repaymentMethod, $duration, $paymentDay, $isCustomRepayment, $amount, $apr);

        $this->assertEquals([
            [
                'date' => '2017-01-20',
                'principal' => '0.00',
                'interest' => '44.35'
            ],
            [
                'date' => '2017-02-20',
                'principal' => '0.00',
                'interest' => '45.83'
            ],
            [
                'date' => '2017-02-21',
                'principal' => '10000.00',
                'interest' => '1.49'
            ],
        ], $plan);
    }

    public function testNegativeLixi()  //当投资金额很少的时候,最后一期利息出现负数的情况 优先测试
    {

        $startDate = '2016-07-31';
        $endDate = '2017-07-31';
        $repaymentMethod = 6;
        $duration = 12;
        $paymentDay = 20;
        $isCustomRepayment = false;
        $amount = 1;
        $apr = 0.088;
        $plan = RepaymentHelper::calcRepaymentPlan($startDate, $endDate, $repaymentMethod, $duration, $paymentDay, $isCustomRepayment, $amount, $apr);

        $this->assertEquals([
            [
                'date' => '2016-08-20',
                'principal' => '0.00',
                'interest' => '0.00'
            ],
            [
                'date' => '2016-09-20',
                'principal' => '0.00',
                'interest' => '0.00'
            ],
            [
                'date' => '2016-10-20',
                'principal' => '0.00',
                'interest' => '0.00'
            ],
            [
                'date' => '2016-11-20',
                'principal' => '0.00',
                'interest' => '0.00'
            ],
            [
                'date' => '2016-12-20',
                'principal' => '0.00',
                'interest' => '0.00'
            ],
            [
                'date' => '2017-01-20',
                'principal' => '0.00',
                'interest' => '0.00'
            ],
            [
                'date' => '2017-02-20',
                'principal' => '0.00',
                'interest' => '0.00'
            ],
            [
                'date' => '2017-03-20',
                'principal' => '0.00',
                'interest' => '0.00'
            ],
            [
                'date' => '2017-04-20',
                'principal' => '0.00',
                'interest' => '0.00'
            ],
            [
                'date' => '2017-05-20',
                'principal' => '0.00',
                'interest' => '0.00'
            ],
            [
                'date' => '2017-06-20',
                'principal' => '0.00',
                'interest' => '0.00'
            ],
            [
                'date' => '2017-07-20',
                'principal' => '0.00',
                'interest' => '0.00'
            ],
            [
                'date' => '2017-07-31',
                'principal' => '1.00',
                'interest' => '0.09'
            ],
        ], $plan);
    }

    public function testNormalLixi()
    {
        $startDate = '2016-05-12';
        $endDate = '2018-05-12';
        $repaymentMethod = 4;
        $duration = 24;
        $paymentDay = null;
        $isCustomRepayment = false;
        $amount = 1;
        $apr = 0.01;
        $plan = RepaymentHelper::calcRepaymentPlan($startDate, $endDate, $repaymentMethod, $duration, $paymentDay, $isCustomRepayment, $amount, $apr);

        $this->assertEquals([
            [
                'date' => '2016-11-12',
                'principal' => '0.00',
                'interest' => '0.00'
            ],
            [
                'date' => '2017-05-12',
                'principal' => '0.00',
                'interest' => '0.00'
            ],
            [
                'date' => '2017-11-12',
                'principal' => '0.00',
                'interest' => '0.00'
            ],
            [
                'date' => '2018-05-12',
                'principal' => '1.00',
                'interest' => '0.02'
            ],
        ], $plan);
    }

    public function testZeroLixi()  //当投资金额很少的时候,总利息正常算出来是0,应自动加0.01元  优先测试
    {

        $startDate = '2016-07-19';
        $endDate = '2016-09-19';
        $repaymentMethod = 6;
        $duration = 2;
        $paymentDay = 20;
        $isCustomRepayment = false;
        $amount = 1;
        $apr = 0.025;
        $plan = RepaymentHelper::calcRepaymentPlan($startDate, $endDate, $repaymentMethod, $duration, $paymentDay, $isCustomRepayment, $amount, $apr);

        $this->assertEquals([
            [
                'date' => '2016-07-20',
                'principal' => '0.00',
                'interest' => '0.00'
            ],
            [
                'date' => '2016-08-20',
                'principal' => '0.00',
                'interest' => '0.00'
            ],
            [
                'date' => '2016-09-19',
                'principal' => '1.00',
                'interest' => '0.01'
            ],
        ], $plan);
    }

    //测试等额本息
    public function testDebx()
    {
        $startDate = '2017-04-01';
        $endDate = null;
        $repaymentMethod = 10;
        $duration = 2;
        $paymentDay = null;
        $isCustomRepayment = false;
        $amount = 1000;
        $apr = 0.02;
        $plan = RepaymentHelper::calcRepaymentPlan($startDate, $endDate, $repaymentMethod, $duration, $paymentDay, $isCustomRepayment, $amount, $apr);

        $this->assertEquals([
            [
                'date' => '2017-05-01',
                'principal' => '499.58',
                'interest' => '1.67'
            ],
            [
                'date' => '2017-06-01',
                'principal' => '500.42',
                'interest' => '0.83'
            ],
        ], $plan);
    }

    public function testMergeRepayment()
    {
        $startDate = '2017-06-21';
        $endDate = '2018-12-21';
        $repaymentMethod = 9;
        $duration = 18;
        $paymentDay = 20;
        $isCustomRepayment = true;
        $amount = 200000;
        $apr = 0.079;
        $plan = RepaymentHelper::calcRepaymentPlan($startDate, $endDate, $repaymentMethod, $duration, $paymentDay, $isCustomRepayment, $amount, $apr);

        $this->assertEquals([
            [
                'date' => '2017-12-20',
                'principal' => '0.00',
                'interest' => '7871.16'
            ],
            [
                'date' => '2018-12-21',
                'principal' => '200000.00',
                'interest' => '15828.84'
            ],
        ], $plan);
    }
}