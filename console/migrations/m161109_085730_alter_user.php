<?php

use yii\db\Migration;

class m161109_085730_alter_user extends Migration
{
    public function up()
    {
        $this->addColumn('user', 'sort', $this->integer(3)->defaultValue(0));
    }

    public function down()
    {
        echo "m161109_085730_alter_user cannot be reverted.\n";

        return false;
    }
}
