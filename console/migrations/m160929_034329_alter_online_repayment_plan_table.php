<?php

use yii\db\Migration;

class m160929_034329_alter_online_repayment_plan_table extends Migration
{
    public function up()
    {
        $this->addColumn('online_repayment_plan', 'asset_id', $this->integer(10));
    }

    public function down()
    {
        echo "m160929_034329_alter_online_repayment_plan_table cannot be reverted.\n";

        return false;
    }
}
