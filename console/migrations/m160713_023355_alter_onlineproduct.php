<?php

use yii\db\Migration;

class m160713_023355_alter_onlineproduct extends Migration
{
    public function up()
    {
        $this->addColumn('online_product', 'paymentDay', $this->integer(5));//按照自然（余、季……）付息的  固定还款日
    }

    public function down()
    {
        echo "m160713_023355_alter_onlineproduct cannot be reverted.\n";

        return false;
    }
}
