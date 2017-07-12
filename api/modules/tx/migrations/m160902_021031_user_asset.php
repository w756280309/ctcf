<?php

use yii\db\Migration;

class m160902_021031_user_asset extends Migration
{
    public function up()
    {
        $this->createTable('user_asset', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'loan_id' => $this->integer()->notNull(),
            'order_id' => $this->integer()->notNull(),
            'isRepaid' => $this->boolean()->notNull(),
            'amount' => $this->decimal(14)->notNull(),
            'orderTime' => $this->dateTime()->notNull(),
            'createTime' => $this->datetime()->notNull(),
            'updateTime' => $this->datetime(),
        ]);
    }

    public function down()
    {
        echo "m160902_021031_user_asset cannot be reverted.\n";

        return false;
    }
}
