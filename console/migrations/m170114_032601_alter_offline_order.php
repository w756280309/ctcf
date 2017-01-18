<?php

use yii\db\Migration;

class m170114_032601_alter_offline_order extends Migration
{
    public function up()
    {
        $this->alterColumn('offline_order', 'valueDate', $this->date());
    }

    public function down()
    {
        echo "m170114_032601_alter_offline_order cannot be reverted.\n";

        return false;
    }
}
