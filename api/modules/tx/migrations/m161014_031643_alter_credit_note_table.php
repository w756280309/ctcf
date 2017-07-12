<?php

use yii\db\Migration;

class m161014_031643_alter_credit_note_table extends Migration
{
    public function up()
    {
        $this->addColumn('credit_note', 'isManualCanceled', $this->boolean()->notNull());
    }

    public function down()
    {
        echo "m161014_031643_alter_credit_note_table cannot be reverted.\n";

        return false;
    }
}
