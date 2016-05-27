<?php

use yii\db\Migration;

class m160526_081854_alter_perf extends Migration
{
    public function up()
    {
        $this->alterColumn('perf', 'chargeViaPos', $this->decimal(10, 2));
        $this->alterColumn('perf', 'chargeViaEpay', $this->decimal(10, 2));
        $this->alterColumn('perf', 'drawAmount', $this->decimal(10, 2));
        $this->alterColumn('perf', 'investmentInWyj', $this->decimal(10, 2));
        $this->alterColumn('perf', 'investmentInWyb', $this->decimal(10, 2));
        $this->alterColumn('perf', 'totalInvestment', $this->decimal(10, 2));
        $this->alterColumn('perf', 'successFound', $this->decimal(10, 2));
        $this->alterColumn('perf', 'rechargeMoney', $this->decimal(10, 2));
        $this->alterColumn('perf', 'rechargeCost', $this->decimal(10, 2));
        $this->alterColumn('perf', 'draw', $this->decimal(10, 2));
        $this->alterColumn('perf', 'remainMoney', $this->decimal(10, 2));
        $this->alterColumn('perf', 'usableMoney', $this->decimal(10, 2));
        $this->addColumn('perf', 'newRegisterAndInvestor', $this->integer());
    }

    public function down()
    {
        echo "m160526_081854_alter_perf cannot be reverted.\n";

        return false;
    }
}
