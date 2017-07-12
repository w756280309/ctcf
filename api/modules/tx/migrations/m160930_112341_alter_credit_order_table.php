<?php

use yii\db\Migration;

class m160930_112341_alter_credit_order_table extends Migration
{
    public function up()
    {
        $this->addColumn('credit_order', 'earnings', $this->decimal(14));
    }

    public function down()
    {
        echo "m160930_112341_alter_credit_order_table cannot be reverted.\n";

        return false;
    }
}
