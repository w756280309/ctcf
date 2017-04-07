<?php

use yii\db\Migration;

class m170405_102910_alter_offline_oder_table extends Migration
{
    public function up()
    {
        $this->dropColumn('offline_order', 'realName');
    }

    public function down()
    {
        echo "m170405_102910_alter_offline_oder_table cannot be reverted.\n";

        return false;
    }

}
