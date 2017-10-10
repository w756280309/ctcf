<?php

use yii\db\Migration;

class m171010_024147_alter_online_product extends Migration
{
    public function safeUp()
    {
        $this->alterColumn('online_product', 'internalTitle', $this->string()->null());
    }

    public function safeDown()
    {
        echo "m171010_024147_alter_online_product cannot be reverted.\n";

        return false;
    }
}
