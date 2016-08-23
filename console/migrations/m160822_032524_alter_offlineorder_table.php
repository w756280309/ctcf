<?php

use yii\db\Migration;

class m160822_032524_alter_offlineorder_table extends Migration
{
    public function up()
    {
        $this->renameColumn('offline_order', 'branch_id', 'affiliator_id');
    }

    public function down()
    {
        echo "m160822_032524_alter_offlineorder_table cannot be reverted.\n";

        return false;
    }
}
