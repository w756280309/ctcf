<?php

use yii\db\Migration;

class m160815_060433_alter_online_product_table extends Migration
{
    public function up()
    {
        $this->addColumn('online_product', 'filingAmount', $this->decimal(14, 2));
    }

    public function down()
    {
        echo "m160815_060433_alter_online_product_table cannot be reverted.\n";

        return false;
    }
}
