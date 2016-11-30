<?php

use yii\db\Migration;

class m161129_061331_alter_perf extends Migration
{
    public function up()
    {
        $this->addColumn('perf', 'repayMoney', $this->decimal(14,4));
        $this->addColumn('perf', 'repayLoanCount', $this->integer());
        $this->addColumn('perf', 'repayUserCount', $this->integer());
    }

    public function down()
    {
        echo "m161129_061331_alter_perf cannot be reverted.\n";

        return false;
    }
}
