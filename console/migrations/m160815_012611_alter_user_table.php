<?php

use yii\db\Migration;

class m160815_012611_alter_user_table extends Migration
{
    public function up()
    {
        $this->dropIndex('idcard', 'user');
    }

    public function down()
    {
        echo "m160815_012611_alter_user_table cannot be reverted.\n";

        return false;
    }
}