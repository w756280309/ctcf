<?php

use yii\db\Migration;

class m160527_070253_alter_perf extends Migration
{
    public function up()
    {
        $this->alterColumn('perf', 'chargeViaPos', $this->decimal(14, 2));
        $this->alterColumn('perf', 'chargeViaEpay', $this->decimal(14, 2));
        $this->alterColumn('perf', 'drawAmount', $this->decimal(14, 2));
        $this->alterColumn('perf', 'investmentInWyj', $this->decimal(14, 2));
        $this->alterColumn('perf', 'investmentInWyb', $this->decimal(14, 2));
        $this->alterColumn('perf', 'totalInvestment', $this->decimal(14, 2));
        $this->alterColumn('perf', 'successFound', $this->decimal(14, 2));
        $this->alterColumn('perf', 'rechargeMoney', $this->decimal(14, 2));
        $this->alterColumn('perf', 'rechargeCost', $this->decimal(14, 2));
        $this->alterColumn('perf', 'draw', $this->decimal(14, 2));
    }

    public function down()
    {
        echo "m160527_070253_alter_perf cannot be reverted.\n";

        return false;
    }
}
