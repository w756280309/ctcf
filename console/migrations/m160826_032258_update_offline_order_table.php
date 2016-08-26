<?php

use yii\db\Migration;

class m160826_032258_update_offline_order_table extends Migration
{
    public function up()
    {
        $this->alterColumn('offline_order', 'mobile', $this->string(20)->notNull());
    }

    public function down()
    {
        echo "m160826_032258_update_offline_order_table cannot be reverted.\n";

        return false;
    }
}
