<?php

use yii\db\Migration;

class m171218_010420_alter_offline_repayment_plan extends Migration
{
    public function up()
    {
        $this->addColumn('offline_repayment_plan', 'yuqi_day', $this->integer()->notNull()->defaultValue(0)->comment('逾期天数'));
        $this->addColumn('offline_repayment_plan', 'tiexi', $this->decimal(14,2)->notNull()->defaultValue(0)->comment('贴息(逾期费用)'));
    }

    public function down()
    {
        echo "m171218_010420_alter_offline_repayment_plan cannot be reverted.\n";

        return false;
    }
}
