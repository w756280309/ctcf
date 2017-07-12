<?php

use yii\db\Migration;

class m161011_080742_alter_credit_order_table extends Migration
{
    public function up()
    {
        $this->dropColumn('credit_order', 'earnings');
    }

    public function down()
    {
        echo "m161011_080742_alter_credit_order_table cannot be reverted.\n";

        return false;
    }
}
