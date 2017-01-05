<?php

use yii\db\Migration;

class m170103_071042_alter_promo_table extends Migration
{
    public function up()
    {
        $this->alterColumn('promo', 'endAt', $this->integer());
    }

    public function down()
    {
        echo "m170103_071042_alter_promo_table cannot be reverted.\n";

        return false;
    }
}
