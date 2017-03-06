<?php

use yii\db\Migration;

class m170306_084543_insert_promo_table extends Migration
{
    public function up()
    {
        $this->insert('promo', [
            'title' => '女神节理财送大礼',
            'startTime' => '2017-03-06 00:00:00',
            'endTime' => '2017-03-08 23:59:59',
            'key' => 'women_promo',
            'promoClass' => '',
            'isOnline' => true,
        ]);
    }

    public function down()
    {
        echo "m170306_084543_insert_promo_table cannot be reverted.\n";

        return false;
    }
}
