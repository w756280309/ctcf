<?php

use yii\db\Migration;

class m171105_024905_alter_payment_log extends Migration
{
    public function safeUp()
    {
        $this->addColumn('payment_log', 'ref_id', $this->integer()->null());
        $this->addColumn('payment_log', 'ref_type', $this->smallInteger()->defaultValue(0));
    }

    public function safeDown()
    {
        echo "m171105_024905_alter_payment_log cannot be reverted.\n";

        return false;
    }
}
