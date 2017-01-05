<?php

use yii\db\Migration;

class m170105_073832_alter_loan_sort extends Migration
{
    public function up()
    {
        $this->update('online_product', ['sort' => 70], ['sort' => 50]);
    }

    public function down()
    {
        echo "m170105_073832_alter_loan_sort cannot be reverted.\n";

        return false;
    }
}
