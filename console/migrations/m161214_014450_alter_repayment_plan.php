<?php

use yii\db\Migration;

class m161214_014450_alter_repayment_plan extends Migration
{
    public function up()
    {
        $this->addColumn('online_repayment_plan', 'actualRefundTime', $this->dateTime());
    }

    public function down()
    {
        echo "m161214_014450_alter_repayment_plan cannot be reverted.\n";

        return false;
    }
}
