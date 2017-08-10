<?php

namespace Test\Common\Models\Utils;

use common\utils\StringUtils;
use Test\YiiAppTestCase;

class StringUtilsTest extends YiiAppTestCase
{
    public function numTpRmbDataProvider()
    {
        return [
            ['0.01', '壹分'],
            ['0.10', '壹角'],
            ['1.00', '壹元整'],
            ['1.01', '壹元零壹分'],
            ['1.10', '壹元壹角'],
            ['1000.00', '壹仟元整'],
            ['1001000.00', '壹佰万壹仟元整'],
        ];
    }

    /**
     * 测试数值转整数
     * @dataProvider numTpRmbDataProvider
     *
     * @param $num
     * @param $rmb
     */
    public function testNumToRmb($num, $rmb)
    {
        $this->assertEquals($rmb, StringUtils::numToRmb($num));
    }
}