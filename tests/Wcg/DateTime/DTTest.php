<?php
namespace Test\Wcg\DateTime;

use Test\YiiAppTestCase;
use Wcg\DateTime\DT;

/**
 * Created by PhpStorm.
 * User: yang
 * Date: 16-11-1
 * Time: 下午2:04
 */
class DTTest extends YiiAppTestCase
{
    //测试给指定日期加月份
    public function testAddMonth1()
    {
        $res = (new DT('2016-01-31'))->addMonth(1);
        $this->assertEquals($res->format('Y-m-d'), '2016-02-29');
    }

    public function testAddMonth2()
    {
        $res = (new DT('2016-01-31'))->addMonth(13);
        $this->assertEquals($res->format('Y-m-d'), '2017-02-28');
    }

    public function testAddMonth3()
    {
        $res = (new DT('2016-01-31'))->addMonth(3);
        $this->assertEquals($res->format('Y-m-d'), '2016-04-30');
    }

    public function testAddMonth4()
    {
        $res = (new DT('2016-11-01'))->addMonth(6);
        $this->assertEquals($res->format('Y-m-d'), '2017-05-01');
    }

    public function testAddMonth5()
    {
        $res = (new DT('2017-01-31'))->addMonth(1);
        $this->assertEquals($res->format('Y-m-d'), '2017-02-28');
    }

    public function testAddMonth6()
    {
        $res = (new DT('2017-02-28'))->addMonth(1);
        $this->assertEquals($res->format('Y-m-d'), '2017-03-28');
    }

    public function testAddMonth7()
    {
        $res = (new DT('2017-01-31'))->addMonth(0);
        $this->assertEquals($res->format('Y-m-d'), '2017-01-31');
    }

    public function testAddMonth8()
    {
        $res = (new DT('2017-12-01'))->addMonth(0);
        $this->assertEquals($res->format('Y-m-d'), '2017-12-01');
    }

    public function testAddMonth9()
    {
        $res = (new DT('2017-11-30'))->addMonth(1);
        $this->assertEquals($res->format('Y-m-d'), '2017-12-30');
    }

    public function testAddMonth10()
    {
        $res = (new DT('2017-11-11'))->addMonth(13);
        $this->assertEquals($res->format('Y-m-d'), '2018-12-11');
    }

    public function testAddMonth11()
    {
        $res = (new DT('2017-11-30'))->addMonth(25);
        $this->assertEquals($res->format('Y-m-d'), '2019-12-30');
    }

    //测试两个日期函数时间差
    public function testDiff1()
    {
        $res = (new DT('2016-10-31'))->humanDiff(new DT('2016-12-26'));
        $this->assertEquals([$res['y'], $res['m'], $res['d']], [0, 1, 26]);
    }

    public function testDiff2()
    {
        $res = (new DT('2016-10-31'))->humanDiff(new DT('2017-03-12'));
        $this->assertEquals([$res['y'], $res['m'], $res['d']], [0, 4, 12]);
    }

    public function testDiff3()
    {
        $res = (new DT('2016-10-31'))->humanDiff(new DT('2018-03-24'));
        $this->assertEquals([$res['y'], $res['m'], $res['d']], [1, 4, 24]);
    }

    public function testDiff4()
    {
        $res = (new DT('2016-12-01'))->humanDiff(new DT('2016-12-26'));
        $this->assertEquals([$res['y'], $res['m'], $res['d']], [0, 0, 25]);
    }

    public function testDiff5()
    {
        $res = (new DT('2016-11-01'))->humanDiff(new DT('2017-05-01'));
        $this->assertEquals([$res['y'], $res['m'], $res['d']], [0, 6, 0]);
    }

    public function testDiff6()
    {
        $res = (new DT('2016-11-01'))->humanDiff(new DT('2017-03-11'));
        $this->assertEquals([$res['y'], $res['m'], $res['d']], [0, 4, 10]);
    }

    public function testDiff7()
    {
        $res = (new DT('2016-11-01'))->humanDiff(new DT('2020-03-01'));
        $this->assertEquals([$res['y'], $res['m'], $res['d']], [3, 4, 0]);
    }

    public function testDiff8()
    {
        $res = (new DT('2016-11-01'))->humanDiff(new DT('2020-03-04'));
        $this->assertEquals([$res['y'], $res['m'], $res['d']], [3, 4, 3]);
    }

    public function testDiff9()
    {
        $res = (new DT('2016-11-30'))->humanDiff(new DT('2017-01-05'));
        $this->assertEquals([$res['y'], $res['m'], $res['d']], [0, 1, 6]);
    }

    public function testDiff10()
    {
        $res = (new DT('2016-11-30'))->humanDiff(new DT('2017-02-28'));
        $this->assertEquals([$res['y'], $res['m'], $res['d']], [0, 3, 0]);
    }

    public function testDiff11()
    {
        $res = (new DT('2017-02-28'))->humanDiff(new DT('2017-03-31'));
        $this->assertEquals([$res['y'], $res['m'], $res['d']], [0, 1, 3]);
    }

    public function testDiff12()
    {
        $res = (new DT('2017-02-28'))->humanDiff(new DT('2017-03-01'));
        $this->assertEquals([$res['y'], $res['m'], $res['d']], [0, 0, 1]);
    }

    public function testDiff13()
    {
        $res = (new DT('2020-02-29'))->humanDiff(new DT('2020-03-01'));
        $this->assertEquals([$res['y'], $res['m'], $res['d']], [0, 0, 1]);
    }

    public function testDiff14()
    {
        $res = (new DT('2020-02-29'))->humanDiff(new DT('2020-03-31'));
        $this->assertEquals([$res['y'], $res['m'], $res['d']], [0, 1, 2]);
    }
}