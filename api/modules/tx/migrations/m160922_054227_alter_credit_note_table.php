<?php

use yii\db\Migration;

class m160922_054227_alter_credit_note_table extends Migration
{
    public function up()
    {
        $this->addColumn('credit_note', 'loan_id', $this->integer()->notNull());
        $this->addColumn('credit_note', 'order_id', $this->integer()->notNull());
    }

    public function down()
    {
        echo "m160922_054227_alter_credit_note_table cannot be reverted.\n";

        return false;
    }
}
