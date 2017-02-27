<?php

use yii\db\Migration;

class m170224_085557_alter_user extends Migration
{
    public function up()
    {
        $this->addColumn('user', 'safeMobile', $this->string());
        $this->addColumn('user', 'safeIdCard', $this->string());
    }

    public function down()
    {
        echo "m170224_085557_alter_user cannot be reverted.\n";

        return false;
    }
}
