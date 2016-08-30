<?php

use yii\db\Migration;

class m160829_092707_alter_admin_log extends Migration
{
    public function up()
    {
        $this->alterColumn('admin_log', 'admin_id', $this->integer());
    }

    public function down()
    {
        echo "m160829_092707_alter_admin_log cannot be reverted.\n";

        return false;
    }
}
