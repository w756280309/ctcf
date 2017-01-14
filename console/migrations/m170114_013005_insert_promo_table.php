<?php

use yii\db\Migration;

class m170114_013005_insert_promo_table extends Migration
{
    public function up()
    {
        $this->insert('promo', [
            'title' => '首次投资送电影票',
            'startTime' => '2017-01-18 0:0:0',
            'endTime' => '2017-02-08 23:59:59',
            'key' => 'promo_movie',
            'promoClass' => '',
            'isOnline' => true,
        ]);
    }

    public function down()
    {
        echo "m170114_013005_insert_promo_table cannot be reverted.\n";

        return false;
    }
}
