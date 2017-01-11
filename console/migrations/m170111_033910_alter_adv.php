<?php

use yii\db\Migration;

class m170111_033910_alter_adv extends Migration
{
    public function up()
    {
        $this->alterColumn('adv', 'link', $this->string());
    }

    public function down()
    {
        echo "m170111_033910_alter_adv cannot be reverted.\n";

        return false;
    }
}
