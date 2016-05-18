<?php

use yii\db\Migration;

class m160518_155122_create_payment_log_table extends Migration
{
    public function up()
    {
        $this->createTable('payment_log', [
            'id' => $this->primaryKey(),
            'txSn' => $this->string(32)->notNull()->unique(),
            'amount' => $this->decimal(14, 2)->notNull(),
            'toParty_id' => $this->integer(11)->notNull(),
            'loan_id' => $this->integer(11)->notNull(),
            'createdAt' => $this->integer(11)->notNull(),
        ]);
    }

    public function down()
    {
        $this->dropTable('payment_log');
    }
}
