<?php

use yii\db\Migration;

class m170119_012424_add_promo extends Migration
{
    public function up()
    {
        $this->insert('promo', [
            'title' => '温都猫',
            'key' => 'wendumao',
            'isOnline' => 1,
            'startTime' => '2017-01-18 00:00:00',
            'endTime' => '2018-02-08 :23:59:59',
        ]);
    }

    public function down()
    {
        echo "m170119_012424_add_promo cannot be reverted.\n";

        return false;
    }
}
