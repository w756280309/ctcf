<?php

namespace Test\Common\Models\Loan;

use common\models\product\RedeemHelper;
use Test\YiiAppTestCase;

class RedeemHelperTest extends YiiAppTestCase
{
    /**
     * 测试多个时间段 - 非正常情况
     */
    public function testGetClosedPeriodExpireTime()
    {
        $dateStr = '20171010,20171020
20171110,20171120';
        $expireTime = RedeemHelper::getClosedPeriodExpireTime($dateStr);

        $this->assertEquals($expireTime, null);
    }

    /**
     * 测试一个时间段 - 正常情况
     */
    public function testGetClosedPeriodExpireTime1()
    {
        $dateStr = '20171010,20171020';
        $expireTime = RedeemHelper::getClosedPeriodExpireTime($dateStr);

        $this->assertEquals($expireTime, (new \DateTime('20171009')));
    }

    /**
     * 测试一个时间段 - 非正常情况（末尾加换行符）
     */
    public function testGetClosedPeriodExpireTime2()
    {
        $dateStr = '20171010,20171020
';
        $expireTime = RedeemHelper::getClosedPeriodExpireTime($dateStr);

        $this->assertEquals($expireTime, null);
    }

    /**
     * 测试一个时间段 - 非正常情况（非标准可识别时间）
     */
    public function testGetClosedPeriodExpireTime3()
    {
        $dateStr = '2017s010,20171020';
        $expireTime = RedeemHelper::getClosedPeriodExpireTime($dateStr);

        $this->assertEquals($expireTime, null);
    }
}