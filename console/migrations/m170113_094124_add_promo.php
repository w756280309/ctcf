<?php

use yii\db\Migration;

class m170113_094124_add_promo extends Migration
{
    public function up()
    {
        $this->insert('promo', [
            'title' => '首次投资送积分',
            'starTime' => '2017-01-18 00:00:00',
            'endTime' => '2017-02-08 23:59:59',
            'key' => 'first_order_point',
            'promoClass' => \common\models\promo\FirstOrderPoints::class,
            'isOnline' => 0,
        ]);
    }

    public function down()
    {
        echo "m170113_094124_add_promo cannot be reverted.\n";

        return false;
    }
}
