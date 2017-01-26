<?php

use yii\db\Migration;

class m170126_080032_insert_promo_table extends Migration
{
    public function up()
    {
        $this->insert('promo', [
            'title' => '太空展套票',
            'startTime' => '2017-01-26 0:0:0',
            'endTime' => '2017-03-10 23:59:59',
            'key' => 'promo_space',
            'promoClass' => '',
            'isOnline' => true,
        ]);
    }

    public function down()
    {
        echo "m170126_080032_insert_promo_table cannot be reverted.\n";

        return false;
    }
}
