<?php

use yii\db\Migration;

class m160811_103351_update_settle_table extends Migration
{
    public function up()
    {
        $this->alterColumn('settle', 'fee', 'decimal(14, 2)');
    }

    public function down()
    {
        echo "m160811_103351_update_settle_table cannot be reverted.\n";

        return false;
    }
}
