<?php

namespace Test\Common\Models\Promo;

use common\models\promo\PromoPoker;
use Test\YiiAppTestCase;
use wap\modules\promotion\models\RankingPromo;

class PromoPokerTest extends YiiAppTestCase
{
    /**
     * @dataProvider poolProvider
     *
     * @param string $term
     * @param array  $pool
     *
     */
    public function testCreatePool($term, array $pool)
    {
        $promo = RankingPromo::findOne(['key' => 'promo_poker']);
        $promoPoker = new PromoPoker($promo);
        $method = new \ReflectionMethod('common\models\promo\PromoPoker', 'createPool');
        $method->setAccessible(true);
        $this->assertEquals($pool, $method->invoke($promoPoker, $term));
    }

    public function poolProvider()
    {
        return [
            [
                '20171016', [
                    1 => '0.05',
                    2 => '0.05',
                    3 => '0.05',
                    4 => '0.05',
                    5 => '0.05',
                    6 => '0.05',
                    7 => '0.05',
                    8 => '0.05',
                    9 => '0.05',
                    10 => '0.05',
                    11 => '0.05',
                    12 => '0.4',
                    13 => '0.05',
                ],
            ],
            [
                '20171023', [
                    1 => '0.05',
                    2 => '0.05',
                    3 => '0.05',
                    4 => '0.05',
                    5 => '0.05',
                    6 => '0.05',
                    7 => '0.4',
                    8 => '0.05',
                    9 => '0.05',
                    10 => '0.05',
                    11 => '0.05',
                    12 => '0.05',
                    13 => '0.05',
                ],
            ],
        ];
    }
}