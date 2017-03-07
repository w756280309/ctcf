<?php

use yii\db\Migration;

class m170228_082125_alter_user extends Migration
{
    public function up()
    {
        $this->addColumn('user', 'birthdate', $this->string(8));
    }

    public function down()
    {
        echo "m170228_082125_alter_user cannot be reverted.\n";

        return false;
    }
}
