<?php

use yii\db\Migration;

class m160809_052857_insert_promo_table extends Migration
{
    public function up()
    {
        $this->insert('promo', [
            'title' => '新手迎奥运，投资赢大奖',
            'key' => 'OLYMPIC_PROMO_160809',
            'startAt' => strtotime('2016-08-12 00:00:00'),
            'endAt' => strtotime('2016-08-31 23:59:59'),
        ]);
    }

    public function down()
    {
        echo "m160809_052857_insert_promo_table cannot be reverted.\n";

        return false;
    }
}
