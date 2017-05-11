<?php

use yii\db\Migration;

class m170510_095636_alter_user_table extends Migration
{
    public function up()
    {
        $this->addColumn('user', 'regLocation', $this->string());
    }

    public function down()
    {
        echo "m170510_095636_alter_user_table cannot be reverted.\n";

        return false;
    }
}
