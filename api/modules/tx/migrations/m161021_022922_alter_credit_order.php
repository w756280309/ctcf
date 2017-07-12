<?php

use yii\db\Migration;

class m161021_022922_alter_credit_order extends Migration
{
    public function up()
    {
        $this->addColumn('credit_order', 'buyerAmount', $this->decimal(14));
        $this->addColumn('credit_order', 'sellerAmount', $this->decimal(14));
        $this->addColumn('credit_order', 'settleTime', $this->dateTime());
    }

    public function down()
    {
        echo "m161021_022922_alter_credit_order cannot be reverted.\n";

        return false;
    }
}
