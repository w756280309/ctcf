<?php

use yii\db\Migration;

class m160815_052503_alter_user_bank_table extends Migration
{
    public function up()
    {
        $this->dropIndex('card_number', 'user_bank');
    }

    public function down()
    {
        echo "m160815_052503_alter_user_bank_table cannot be reverted.\n";

        return false;
    }
}
