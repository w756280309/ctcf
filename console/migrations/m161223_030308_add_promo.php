<?php

use yii\db\Migration;

class m161223_030308_add_promo extends Migration
{
    public function up()
    {
        $this->insert('promo', [
            'title' => '购买标的送积分',
            'startAt' => strtotime('2017-01-01 00:00:00'),
            'endAt' => strtotime('2117-01-01 00:00:00'),
            'key' => 'loan_order_points',
            'promoClass' => \common\models\promo\LoanOrderPoints::class,
            'isOnline' => 1,
        ]);
    }

    public function down()
    {
        echo "m161223_030308_add_promo cannot be reverted.\n";

        return false;
    }
}
