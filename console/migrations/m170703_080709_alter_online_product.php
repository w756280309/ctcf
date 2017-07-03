<?php

use yii\db\Migration;

class m170703_080709_alter_online_product extends Migration
{
    public function safeUp()
    {
        $this->addColumn('online_product', 'internalTitle', $this->string(30)->defaultValue(null));
    }

    public function safeDown()
    {
        echo "m170703_080709_alter_online_product cannot be reverted.\n";

        return false;
    }
}
