<?php

use yii\db\Migration;

class m170307_053851_alter_perf extends Migration
{
    public function up()
    {
        $this->addColumn('perf', 'licaiNewInvCount', $this->integer());
        $this->addColumn('perf', 'licaiNewInvSum', $this->decimal(14, 2));
        $this->addColumn('perf', 'licaiInvCount', $this->integer());
        $this->addColumn('perf', 'licaiInvSum', $this->decimal(14, 2));

        $this->addColumn('perf', 'xsNewInvCount', $this->integer());
        $this->addColumn('perf', 'xsNewInvSum', $this->decimal(14, 2));
        $this->addColumn('perf', 'xsInvCount', $this->integer());
        $this->addColumn('perf', 'xsInvSum', $this->decimal(14, 2));
    }

    public function down()
    {
        echo "m170307_053851_alter_perf cannot be reverted.\n";

        return false;
    }
}
