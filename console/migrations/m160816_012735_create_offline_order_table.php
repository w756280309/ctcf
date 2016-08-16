<?php

use yii\db\Migration;

class m160816_012735_create_offline_order_table extends Migration
{
    public function up()
    {
        $this->createTable('offline_order', [
            'id' => $this->primaryKey(),
            'branch_id' => $this->integer(10)->notNull(),
            'loan_id' => $this->integer(10)->notNull(),
            'realName' => $this->string(50)->notNull(),
            'mobile' => $this->string(11)->notNull(),
            'money' => $this->decimal(14, 2)->notNull(),
            'orderDate' => $this->date()->notNull(),
            'created_at' => $this->integer(10)->notNull(),
        ]);
    }

    public function down()
    {
        $this->dropTable('offline_order');
    }
}
