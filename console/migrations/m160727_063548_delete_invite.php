<?php

use yii\db\Migration;

class m160727_063548_delete_invite extends Migration
{
    public function up()
    {
        $this->dropTable('invite');
    }

    public function down()
    {
        echo "m160727_063548_delete_invite cannot be reverted.\n";

        return false;
    }
}
