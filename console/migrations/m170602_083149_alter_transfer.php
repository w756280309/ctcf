<?php

use yii\db\Migration;

class m170602_083149_alter_transfer extends Migration
{
    public function up()
    {
        $this->addColumn('transfer', 'lastCronCheckTime', $this->integer()->null());
    }

    public function down()
    {
        echo "m170602_083149_alter_transfer cannot be reverted.\n";

        return false;
    }
}
