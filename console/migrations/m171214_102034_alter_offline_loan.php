<?php

use yii\db\Migration;

class m171214_102034_alter_offline_loan extends Migration
{
    public function up()
    {
        $this->addColumn('offline_loan', 'is_jixi', $this->boolean()->notNull()->defaultValue(0)->comment('是否计息'));
    }

    public function down()
    {
        echo "m171214_102034_alter_offline_loan cannot be reverted.\n";

        return false;
    }
}
