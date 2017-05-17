<?php

use yii\db\Migration;

class m170512_055441_alter_perf extends Migration
{
    public function up()
    {
        $this->addColumn('perf', 'checkIn', $this->integer());
    }

    public function down()
    {
        echo "m170512_055441_alter_perf cannot be reverted.\n";

        return false;
    }
}
