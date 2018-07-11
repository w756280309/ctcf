<?php

namespace Test\Common\Models\Product;

use common\models\product\RepaymentHelper;
use Test\YiiAppTestCase;
use Wcg\Math\Bc;

class DebxAnnualInvestTest extends YiiAppTestCase
{
    //测试单个用户某段时间内线上等额本息年化
    public function testDebxUserOnlineAnnualInvest()
    {
        $startDate = '2018-07-05';  // 标的购买日期
        $duration = 4;              //标的项目期限 expires
        $apr = 0.1;                 //订单的利率yield_rate
        $amount = 15000;            //订单金额order_money
        $plan = RepaymentHelper::calcDebxAnnualInvest($startDate, $duration, $apr, $amount);
        $plan = Bc::round($plan, 2);

        $this->assertEquals(3137.97, $plan);
    }
}
