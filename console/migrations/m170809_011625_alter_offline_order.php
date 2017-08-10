<?php

use yii\db\Migration;

class m170809_011625_alter_offline_order extends Migration
{
    public function safeUp()
    {
        $this->addColumn('offline_order', 'apr', $this->decimal(14, 6));
    }

    public function safeDown()
    {
        echo "m170809_011625_alter_offline_order cannot be reverted.\n";

        return false;
    }
}
