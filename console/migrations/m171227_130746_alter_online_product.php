<?php

use yii\db\Migration;

class m171227_130746_alter_online_product extends Migration
{
    public function safeUp()
    {
        $this->addColumn('online_product', 'isDailyAccrual', $this->boolean()->defaultValue(false));
    }

    public function safeDown()
    {
        echo "m171227_130746_alter_online_product cannot be reverted.\n";

        return false;
    }
}
