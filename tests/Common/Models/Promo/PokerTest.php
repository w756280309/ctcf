<?php

namespace Test\Common\Models\Promo;

use common\models\promo\Poker;
use Test\YiiAppTestCase;

class PokerTest extends YiiAppTestCase
{
    /**
     * @dataProvider termProvider
     */
    public function testCalcTerm($timeAt, $term)
    {
        $this->assertEquals($term, Poker::calcTerm($timeAt));
    }

    public function termProvider()
    {
        return [
            [strtotime('2017-10-09 09:18:59'), '20171009'],
            [strtotime('2017-10-08 09:18:59'), '20171009'],
            [strtotime('2017-10-10 10:00:01'), '20171016'],
            [strtotime('2017-10-16 09:59:59'), '20171016'],
        ];
    }

    /**
     * @dataProvider winningNumberProvider
     */
    public function testCreateWinningNumber($term, $winNumber)
    {
        $this->assertEquals($winNumber, Poker::createWinningNumber($term));
    }

    public function winningNumberProvider()
    {
        return [
            ['20171016', 12],
            ['20171023', 7],
            ['20171030', 3],
            ['20171106', 8],
        ];
    }
}
