<?php

namespace Test\Common\Models\PromoLotteryTicket;

use common\models\promo\PromoLotteryTicket;
use Test\YiiAppTestCase;

class PromoLotteryTicketTest extends YiiAppTestCase
{
    /**
     * 验证完整的夺宝码是否为七位.
     */
    public function testDuobaoCode()
    {
        $ticket = new PromoLotteryTicket([
            'duobaoCode' => 345,
        ]);

        $this->assertEquals($ticket->getCode(), 1000345);

        $ticket->duobaoCode = 888;

        $this->assertEquals($ticket->getCode(), 1000888);
    }
}