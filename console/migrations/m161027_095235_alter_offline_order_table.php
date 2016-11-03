<?php

use yii\db\Migration;

class m161027_095235_alter_offline_order_table extends Migration
{
    public function up()
    {
        $this->addColumn('offline_order', 'isDeleted', $this->boolean()->notNull());
    }

    public function down()
    {
        echo "m161027_095235_alter_offline_order_table cannot be reverted.\n";

        return false;
    }
}
