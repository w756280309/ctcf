<?php

use yii\db\Migration;

class m170412_012632_alter_online_order_table extends Migration
{
    public function up()
    {
        $this->dropColumn('online_order','mobile');
    }

    public function down()
    {
        echo "m170412_012632_alter_online_order_table cannot be reverted.\n";

        return false;
    }
}
