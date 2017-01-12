<?php

use yii\db\Migration;

class m170111_060444_alter_coins_record extends Migration
{
    public function up()
    {
        $this->addColumn('coins_record', 'isOffline', $this->boolean()->defaultValue(false));
    }

    public function down()
    {
        echo "m170111_060444_alter_coins_record cannot be reverted.\n";

        return false;
    }
}
