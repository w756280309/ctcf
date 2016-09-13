<?php

use yii\db\Migration;

class m160913_091529_delete_credit_trade extends Migration
{
    public function up()
    {
        $this->dropTable('credit_trade');
    }

    public function down()
    {
        echo "m160913_091529_delete_credit_trade cannot be reverted.\n";

        return false;
    }
}
