<?php

use yii\db\Migration;

class m170106_083641_alter_offline_order extends Migration
{
    public function up()
    {
        $this->truncateTable('offline_order');
        $this->addColumn('offline_order', 'user_id', $this->integer()->notNull());
        $this->addColumn('offline_order', 'idCard', $this->string(30)->notNull());
        $this->addColumn('offline_order', 'accBankName', $this->string()->notNull());
        $this->addColumn('offline_order', 'bankCardNo', $this->string(30)->notNull());
        $this->addColumn('offline_order', 'valueDate', $this->date()->notNull());
    }

    public function down()
    {
        echo "m170106_083641_alter_offline_order cannot be reverted.\n";

        return false;
    }
}
