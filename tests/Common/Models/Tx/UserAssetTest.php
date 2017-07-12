<?php

namespace tests\src\Model;

use common\models\tx\FinUtils;
use Test\YiiAppTestCase;

class UserAssetTest extends YiiAppTestCase
{
    //测试日期

    /**
     * @expectedException \Exception
     */
    public function testDate1()
    {
        $res = FinUtils::canBuildCreditByDate([
            'startDate' => '2016-09-12',
            'repaymentDate' => [
                '2016-10-30',
            ],
            'graceDays' => 0,
            'expires' => 48,
            'isAmortized' => false,
        ], [
            'holdDays' => 30,
            'duration' => 3,
            'loan_fenqi_limit' => 1,
            'loan_daoqi_limit' => 30,
        ], '2016-10-11');
        $this->assertTrue($res);
    }

    public function testDate2()
    {
        $res = FinUtils::canBuildCreditByDate([
            'startDate' => '2016-09-12',
            'repaymentDate' => [
                '2016-10-30',
            ],
            'graceDays' => 0,
            'expires' => 48,
            'isAmortized' => false,
        ], [
            'holdDays' => 30,
            'duration' => 3,
            'loan_fenqi_limit' => 1,
            'loan_daoqi_limit' => 30,
        ], '2016-10-12');
        $this->assertTrue($res);
    }

    /**
     * @expectedException \Exception
     */
    public function testDate3()
    {
        $res = FinUtils::canBuildCreditByDate([
            'startDate' => '2016-09-12',
            'repaymentDate' => [
                '2016-10-30',
            ],
            'graceDays' => 0,
            'expires' => 48,
            'isAmortized' => false,
        ], [
            'holdDays' => 30,
            'duration' => 3,
            'loan_fenqi_limit' => 1,
            'loan_daoqi_limit' => 30,
        ], '2016-10-27');
        $this->assertTrue($res);
    }

    public function testDate4()
    {
        $res = FinUtils::canBuildCreditByDate([
            'startDate' => '2016-09-12',
            'repaymentDate' => [
                '2016-10-30',
            ],
            'graceDays' => 0,
            'expires' => 48,
            'isAmortized' => false,
        ], [
            'holdDays' => 30,
            'duration' => 3,
            'loan_fenqi_limit' => 1,
            'loan_daoqi_limit' => 30,
        ], '2016-10-26');
        $this->assertTrue($res);
    }

    /**
     * @expectedException \Exception
     */
    public function testDate5()
    {
        $res = FinUtils::canBuildCreditByDate([
            'startDate' => '2016-09-12',
            'repaymentDate' => [
                '2016-10-30',
            ],
            'graceDays' => 5,
            'expires' => 48,
            'isAmortized' => false,
        ], [
            'holdDays' => 30,
            'duration' => 3,
            'loan_fenqi_limit' => 1,
            'loan_daoqi_limit' => 30,
        ], '2016-10-22');
        $this->assertTrue($res);
    }

    public function testDate6()
    {
        $res = FinUtils::canBuildCreditByDate([
            'startDate' => '2016-09-12',
            'repaymentDate' => [
                '2016-10-30',
            ],
            'graceDays' => 5,
            'expires' => 48,
            'isAmortized' => false,
        ], [
            'holdDays' => 30,
            'duration' => 3,
            'loan_fenqi_limit' => 1,
            'loan_daoqi_limit' => 30,
        ], '2016-10-21');
        $this->assertTrue($res);
    }

    /**
     * @expectedException \Exception
     */
    public function testDate7()
    {
        $res = FinUtils::canBuildCreditByDate([
            'startDate' => '2016-09-12',
            'repaymentDate' => [
                '2016-10-12',
                '2016-11-12',
                '2016-12-12',
            ],
            'graceDays' => 0,
            'expires' => 3,
            'isAmortized' => true,
        ], [
            'holdDays' => 15,
            'duration' => 3,
            'loan_fenqi_limit' => 1,
            'loan_daoqi_limit' => 30,
        ], '2016-09-26');
        $this->assertTrue($res);
    }

    public function testDate8()
    {
        $res = FinUtils::canBuildCreditByDate([
            'startDate' => '2016-09-12',
            'repaymentDate' => [
                '2016-10-12',
                '2016-11-12',
                '2016-12-12',
            ],
            'graceDays' => 0,
            'expires' => 3,
            'isAmortized' => true,
        ], [
            'holdDays' => 15,
            'duration' => 3,
            'loan_fenqi_limit' => 1,
            'loan_daoqi_limit' => 30,
        ], '2016-09-27');
        $this->assertTrue($res);
    }

    /**
     * @expectedException \Exception
     */
    public function testDate9()
    {
        $res = FinUtils::canBuildCreditByDate([
            'startDate' => '2016-09-12',
            'repaymentDate' => [
                '2016-10-12',
                '2016-11-12',
                '2016-12-12',
            ],
            'graceDays' => 0,
            'expires' => 3,
            'isAmortized' => true,
        ], [
            'holdDays' => 30,
            'duration' => 3,
            'loan_fenqi_limit' => 1,
            'loan_daoqi_limit' => 30,
        ], '2016-10-12');
        $this->assertTrue($res);
    }

    public function testDate10()
    {
        $res = FinUtils::canBuildCreditByDate([
            'startDate' => '2016-09-12',
            'repaymentDate' => [
                '2016-10-12',
                '2016-11-12',
                '2016-12-12',
            ],
            'graceDays' => 0,
            'expires' => 3,
            'isAmortized' => true,
        ], [
            'holdDays' => 30,
            'duration' => 3,
            'loan_fenqi_limit' => 1,
            'loan_daoqi_limit' => 30,
        ], '2016-10-13');
        $this->assertTrue($res);
    }

    public function testDate11()
    {
        $res = FinUtils::canBuildCreditByDate([
            'startDate' => '2016-09-12',
            'repaymentDate' => [
                '2016-10-12',
                '2016-11-12',
                '2016-12-12',
            ],
            'graceDays' => 0,
            'expires' => 3,
            'isAmortized' => true,
        ], [
            'holdDays' => 30,
            'duration' => 3,
            'loan_fenqi_limit' => 1,
            'loan_daoqi_limit' => 30,
        ], '2016-12-08');
        $this->assertTrue($res);
    }

    /**
     * @expectedException \Exception
     */
    public function testDate12()
    {
        $res = FinUtils::canBuildCreditByDate([
            'startDate' => '2016-09-12',
            'repaymentDate' => [
                '2016-10-12',
                '2016-11-12',
                '2016-12-12',
            ],
            'graceDays' => 0,
            'expires' => 3,
            'isAmortized' => true,
        ], [
            'holdDays' => 30,
            'duration' => 3,
            'loan_fenqi_limit' => 1,
            'loan_daoqi_limit' => 30,
        ], '2016-12-09');
        $this->assertTrue($res);
    }

    /**
     * @expectedException \Exception
     */
    public function testDate13()
    {
        $res = FinUtils::canBuildCreditByDate([
            'startDate' => '2016-09-12',
            'repaymentDate' => [
                '2016-10-12',
                '2016-11-12',
                '2016-12-12',
            ],
            'graceDays' => 0,
            'expires' => 3,
            'isAmortized' => true,
        ], [
            'holdDays' => 30,
            'duration' => 3,
            'loan_fenqi_limit' => 1,
            'loan_daoqi_limit' => 30,
        ], '2016-09-07');
        $this->assertTrue($res);
    }

    /**
     * @expectedException \Exception
     */
    public function testDate14()
    {
        $res = FinUtils::canBuildCreditByDate([
            'startDate' => '2016-09-12',
            'repaymentDate' => [
                '2016-10-12',
                '2016-11-12',
                '2016-12-12',
            ],
            'graceDays' => 0,
            'expires' => 3,
            'isAmortized' => true,

        ], [
            'holdDays' => 30,
            'duration' => 3,
            'loan_fenqi_limit' => 1,
            'loan_daoqi_limit' => 30,
        ], '2016-12-12');
        $this->assertTrue($res);
    }

    /**
     * @expectedException \Exception
     */
    public function testDate15()
    {
        $res = FinUtils::canBuildCreditByDate([
            'startDate' => '2016-09-12',
            'repaymentDate' => [
                '2016-10-12',
                '2016-11-12',
                '2016-12-12',
            ],
            'graceDays' => 0,
            'expires' => 3,
            'isAmortized' => true,
        ], [
            'holdDays' => 1,
            'duration' => 3,
            'loan_fenqi_limit' => 1,
            'loan_daoqi_limit' => 30,
        ], '2016-9-12');
        $this->assertTrue($res);
    }

    public function testDate16()
    {
        $res = FinUtils::canBuildCreditByDate([
            'startDate' => '2016-09-12',
            'repaymentDate' => [
                '2016-10-12',
                '2016-11-12',
                '2016-12-12',
            ],
            'graceDays' => 0,
            'expires' => 3,
            'isAmortized' => true,
        ], [
            'holdDays' => 1,
            'duration' => 3,
            'loan_fenqi_limit' => 1,
            'loan_daoqi_limit' => 30,
        ], '2016-09-13');
        $this->assertTrue($res);
    }

    public function testDate17()
    {
        $res = FinUtils::canBuildCreditByDate([
            'startDate' => '2016-09-12',
            'repaymentDate' => [
                '2016-10-12',
                '2016-11-12',
                '2016-12-12',
            ],
            'graceDays' => 0,
            'expires' => 3,
            'isAmortized' => true,
        ], [
            'holdDays' => 0,
            'duration' => 3,
            'loan_fenqi_limit' => 1,
            'loan_daoqi_limit' => 30,
        ], '2016-09-12');
        $this->assertTrue($res);
    }

    public function testDate18()
    {
        $res = FinUtils::canBuildCreditByDate([
            'startDate' => '2016-09-12',
            'repaymentDate' => [
                '2016-10-12',
                '2016-11-12',
                '2016-12-12',
            ],
            'graceDays' => 0,
            'expires' => 3,
            'isAmortized' => true,
        ], [
            'holdDays' => 0,
            'duration' => 0,
            'loan_fenqi_limit' => 1,
            'loan_daoqi_limit' => 30,
        ], '2016-10-11');
        $this->assertTrue($res);
    }

    //金额判断
    public function testAmount1()
    {
        $res = FinUtils::canBuildCreditByAmount(10000, 8000, 1000, 1000);
        $this->assertTrue($res);
    }

    /**
     * @expectedException \Exception
     */
    public function testAmount2()
    {
        $res = FinUtils::canBuildCreditByAmount(10000, 9500, 1000, 1000);
        $this->assertTrue($res);
    }

    /**
     * @expectedException \Exception
     */
    public function testAmount3()
    {
        $res = FinUtils::canBuildCreditByAmount(10000, 8500, 1000, 1000);
        $this->assertTrue($res);
    }

    /**
     * @expectedException \Exception
     */
    public function testAmount4()
    {
        $res = FinUtils::canBuildCreditByAmount(10000, 500, 1000, 1000);
        $this->assertTrue($res);
    }

    /**
     * @expectedException \Exception
     */
    public function testAmount5()
    {
        $res = FinUtils::canBuildCreditByAmount(10000, 0, 1000, 1000);
        $this->assertTrue($res);
    }

    /**
     * @expectedException \Exception
     */
    public function testAmount6()
    {
        $res = FinUtils::canBuildCreditByAmount(10000, 11000, 1000, 1000);
        $this->assertTrue($res);
    }

    /**
     * @expectedException \Exception
     */
    public function testAmount7()
    {
        $res = FinUtils::canBuildCreditByAmount(10000, 0, 1000, 1000);
        $this->assertTrue($res);
    }
}
