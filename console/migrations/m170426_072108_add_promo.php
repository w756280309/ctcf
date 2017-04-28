<?php

use yii\db\Migration;

class m170426_072108_add_promo extends Migration
{
    public function up()
    {
        $config = [
            'init' => [
                [
                    'startTime' => '2017-04-29 00:00:00',
                    'endTime' => '2017-05-20 23:59:59',
                    'ticketSource' => 'init',
                    'limit' => 1,
                    'moneyLimit' => 0,
                ],
            ],
            'order' => [
                [
                    'startTime' => '2017-04-29 00:00:00',
                    'endTime' => '2017-05-01 23:59:59',
                    'ticketSource' => 'order_0501',
                    'limit' => 1,
                    'moneyLimit' => 100000,
                ],
                [
                    'startTime' => '2017-05-04 00:00:00',
                    'endTime' => '2017-05-07 23:59:59',
                    'ticketSource' => 'order_0504',
                    'limit' => 1,
                    'moneyLimit' => 100000,
                ],
                [
                    'startTime' => '2017-05-08 00:00:00',
                    'endTime' => '2017-05-14 23:59:59',
                    'ticketSource' => 'order_0510',
                    'limit' => 1,
                    'moneyLimit' => 100000,
                ],
                [
                    'startTime' => '2017-05-15 00:00:00',
                    'endTime' => '2017-05-19 23:59:59',
                    'ticketSource' => 'order_0515',
                    'limit' => 1,
                    'moneyLimit' => 100000,
                ],
                [
                    'startTime' => '2017-05-20 00:00:00',
                    'endTime' => '2017-05-20 23:59:59',
                    'ticketSource' => 'order_0520',
                    'limit' => 1,
                    'moneyLimit' => 100000,
                ],
            ],
            'invite' => [
                [
                    'startTime' => '2017-04-29 00:00:00',
                    'endTime' => '2017-05-20 23:59:59',
                    'ticketSource' => '',
                    'limit' => 3,
                ],
            ],
        ];

        $this->insert('promo', [
            'title' => '2017年5月狂欢节',
            'startTime' => '2017-04-29 00:00:00',
            'endTime' => '2017-05-20 00:00:00',
            'key' => 'promo_201705',
            'promoClass' => 'common\models\promo\Promo201705',
            'isOnline' => false,
            'config' => json_encode($config),
        ]);
    }

    public function down()
    {
        echo "m170426_072108_add_promo cannot be reverted.\n";

        return false;
    }
}
