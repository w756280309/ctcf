<?php

use yii\db\Migration;

class m160810_030509_alter_loan extends Migration
{
    public function up()
    {
        //标的上添加是否是测试标，默认为false
        $this->addColumn('online_product', 'isTest', $this->boolean()->defaultValue(0));
    }

    public function down()
    {
        echo "m160810_030509_alter_loan cannot be reverted.\n";

        return false;
    }
}
