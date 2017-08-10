<?php

use yii\db\Migration;

class m170809_030405_alter_offline_loan extends Migration
{
    public function safeUp()
    {
        $this->addColumn('offline_loan', 'repaymentMethod', $this->smallInteger());
    }

    public function safeDown()
    {
        echo "m170809_030405_alter_offline_loan cannot be reverted.\n";

        return false;
    }
}
