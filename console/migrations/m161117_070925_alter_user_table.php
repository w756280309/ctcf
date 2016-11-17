<?php

use yii\db\Migration;

class m161117_070925_alter_user_table extends Migration
{
    public function up()
    {
        $this->addColumn('user', 'regContext', $this->string()->notNull());
    }

    public function down()
    {
        echo "m161117_070925_alter_user_table cannot be reverted.\n";

        return false;
    }
}
