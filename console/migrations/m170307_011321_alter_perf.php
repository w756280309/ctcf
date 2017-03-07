<?php

use yii\db\Migration;

class m170307_011321_alter_perf extends Migration
{
    public function up()
    {
        $this->addColumn('perf', 'newRegAndNewInveAmount', $this->decimal(14, 2));
        $this->addColumn('perf', 'preRegAndNewInveAmount', $this->decimal(14, 2));
    }

    public function down()
    {
        echo "m170307_011321_alter_perf cannot be reverted.\n";

        return false;
    }
}
