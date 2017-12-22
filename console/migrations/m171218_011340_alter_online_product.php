<?php

use yii\db\Migration;

class m171218_011340_alter_online_product extends Migration
{
    public function safeUp()
    {
        $this->addColumn('online_product', 'isRedeemable', $this->boolean()->defaultValue(false));
        $this->addColumn('online_product', 'redemptionPeriods', $this->string()->null());
        $this->addColumn('online_product', 'redemptionPaymentDates', $this->string()->null());
    }

    public function safeDown()
    {
        echo "m171218_011340_alter_online_product cannot be reverted.\n";

        return false;
    }
}
