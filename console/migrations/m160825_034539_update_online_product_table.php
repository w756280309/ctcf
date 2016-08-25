<?php

use yii\db\Migration;

class m160825_034539_update_online_product_table extends Migration
{
    public function up()
    {
        $this->createIndex('sn', 'online_product', ['sn'], true);
    }

    public function down()
    {
        echo "m160825_034539_update_online_product_table cannot be reverted.\n";

        return false;
    }
}
