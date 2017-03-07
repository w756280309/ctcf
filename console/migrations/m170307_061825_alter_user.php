<?php

use yii\db\Migration;

class m170307_061825_alter_user extends Migration
{
    public function up()
    {
        $this->alterColumn('user', 'birthdate', $this->date());
    }

    public function down()
    {
        echo "m170307_061825_alter_user cannot be reverted.\n";

        return false;
    }
}
