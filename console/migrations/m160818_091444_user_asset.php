<?php

use yii\db\Migration;

class m160818_091444_user_asset extends Migration
{
    public function up()
    {
        $this->createTable('user_asset', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'order_id' => $this->integer(),
            'loan_id' => $this->integer(),
            'amount' => $this->decimal(14, 2),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);
    }

    public function down()
    {
        echo "m160818_091444_user_asset cannot be reverted.\n";
        $this->dropTable('user_asset');
        return false;
    }
}
