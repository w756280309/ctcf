<?php

use yii\db\Migration;

class m160530_080514_alter_perf extends Migration
{
    public function up()
    {
        $this->addColumn('perf', 'investAndLogin', $this->integer());//已投资用户登录数
        $this->addColumn('perf', 'notInvestAndLogin', $this->integer());//未投资用户登录数
    }

    public function down()
    {
        echo "m160530_080514_alter_perf cannot be reverted.\n";

        return false;
    }
}
