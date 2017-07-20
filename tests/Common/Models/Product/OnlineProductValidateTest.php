<?php

namespace Test\Common\Models\Product;

use common\models\product\OnlineProduct;
use Test\YiiAppTestCase;

class OnlineProductValidateTest extends YiiAppTestCase
{
    public function repaymentDayData()
    {
        return [
            [6, 28, true],
            [6, 29, false],
            [6, 30, false],
            [6, 31, false],
            [7, 28, true],
            [7, 29, true],
            [7, 30, true],
            [7, 31, false],
            [8, 28, true],
            [8, 29, true],
            [8, 30, true],
            [8, 31, false],
            [9, 28, true],
            [9, 29, true],
            [9, 30, true],
            [9, 31, true],
            [9, 32, false],
        ];
    }

    /**
     * 固定还款日测试
     * @param $repaymentMethod
     * @param $paymentDay
     * @param $res
     *
     * @dataProvider repaymentDayData
     */
    public function testPaymentDay($repaymentMethod, $paymentDay, $res)
    {
        $loan = new OnlineProduct([
            'refund_method' => $repaymentMethod,
            'paymentDay' => $paymentDay
        ]);
        $loan->scenario = 'create';
        $this->assertEquals($res, $loan->validate(['paymentDay']));
    }
}