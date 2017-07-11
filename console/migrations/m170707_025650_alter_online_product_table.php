<?php

use yii\db\Migration;

class m170707_025650_alter_online_product_table extends Migration
{
    public function safeUp()
    {
        $this->addColumn('online_product', 'publishTime', $this->dateTime());
    }

    public function safeDown()
    {
        echo "m170707_025650_alter_online_product_table cannot be reverted.\n";

        return false;
    }
}
