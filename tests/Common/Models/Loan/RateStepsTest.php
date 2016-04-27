<?php

namespace Test\Common\Models\Loan;

use common\models\product\RateSteps;
use Test\YiiAppTestCase;

class RateStepsTest extends YiiAppTestCase
{
    public function testParse()
    {
        $str = <<<STEPS
1000,8
2000,9
STEPS;

        $this->assertEquals($this->getRateStepsConfig(), RateSteps::parse($str));
    }

    public function testGetTopRate()
    {
        $this->assertEquals(9, RateSteps::getTopRate($this->getRateStepsConfig()));
    }

    public function testGetRateForAmount()
    {
        $this->assertEquals(false, RateSteps::getRateForAmount($this->getRateStepsConfig(), 500));
    }

    private function getRateStepsConfig()
    {
        return [
            [
                'min' => 1000,
                'rate' => 8,
            ],
            [
                'min' => 2000,
                'rate' => 9,
            ],
        ];
    }
}
