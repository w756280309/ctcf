<?php

namespace Test\Common\Models\Reward;

use common\models\promo\Reward;
use Test\YiiAppTestCase;

class DrawTest extends YiiAppTestCase
{
    /**
     * 测试奖池概率配置数组为空
     */
    public function testPoolEmpty()
    {
        $pool = [];

        $this->assertEquals(false, Reward::draw($pool));
    }

    /**
     * 测试奖池概率的数据类型错误的情况
     */
    public function testTypeError()
    {
        $pool = [
            '123' => 0.1,
            '111' => 0.9,
        ];

        $this->assertEquals(false, Reward::draw($pool));
    }

    /**
     * 概率配置数组中存在一个概率<0
     */
    public function testValueRange()
    {
        $pool = [
            '1' => '0.2',
            '2' => '0.8',
            '123' => '-0.1',
        ];

        $this->assertEquals(false, Reward::draw($pool));
    }

    /**
     * 测试奖池概率应小于等于1
     */
    public function testValueRangeOne()
    {
        $pool = [
            '123' => '1.01',
        ];

        $this->assertEquals(false, Reward::draw($pool));
    }

    /**
     * 测试奖池概率相加和应小于等于1分
     */
    public function testValueRangeTwo()
    {
        $pool = [
            '123' => '0.5',
            '234' => '0.51',
        ];

        $this->assertEquals(false, Reward::draw($pool));
    }

    /**
     * 由于最小概率的精确度为0.0001，则验证最小的抽奖次数应为10000次
     */
    public function testDraw10000()
    {
        $pool = [
            'packet_0.66' => '0.1',
            'packet_0.88' => '0.2',
            'packet_1.66' => '0.2',
            'packet_1.88' => '0.2',
            'packet_2.66' => '0.1',
            'packet_2.88' => '0.1',
            'packet_5.2' => '0.0899',
            'packet_16' => '0.01',
            'packet_520_1' => '0.0001',
        ];

        $res = $this->drawResultBytimes(10000, $pool);
        $resStats = array_count_values($res);

        //抽样检查
        $this->assertEquals(0.1, round($resStats['packet_2.66'] / 10000, 1));
        $this->assertEquals(0.01, round($resStats['packet_16'] / 10000, 2));
        $this->assertEquals(true, 799 < $resStats['packet_5.2'] && $resStats['packet_5.2'] < 999);
    }

    /**
     * 大量次数下，概率为0的key不应存在且次数没有缺项
     */
    /*public function testDraw100000()
    {
        $pool = [
            'packet_0.66' => '0.5',
            'packet_0.88' => '0.5',
            'packet_0' => '0.0',
        ];
        $res = $this->drawResultBytimes(100000, $pool);
        $resStats = array_count_values($res);

        $this->assertEquals(100000, array_sum($resStats));
        $this->assertEquals(true, !isset($resStats['packet_0']));
    }*/

    private function drawResultBytimes($times, $poolSetting)
    {
        $drawRes = [];

        for ($i = 0; $i < $times; $i++) {
            array_push($drawRes, Reward::draw($poolSetting));
        }

        return $drawRes;
    }
}
