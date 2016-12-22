<?php

use yii\db\Migration;

class m161222_013843_alter_user extends Migration
{
    public function up()
    {
        $this->addColumn('user', 'points', $this->integer());
        $this->addColumn('user', 'coins', $this->integer());
        $this->addColumn('user', 'level', $this->smallInteger());
    }

    public function down()
    {
        echo "m161222_013843_alter_user cannot be reverted.\n";

        return false;
    }
}
