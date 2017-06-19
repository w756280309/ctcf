<?php

use yii\db\Migration;

class m170619_032659_alter_transfer extends Migration
{
    public function up()
    {
        $this->addColumn('transfer', 'sn', $this->string());
    }

    public function down()
    {
        echo "m170619_032659_alter_transfer cannot be reverted.\n";

        return false;
    }
}
