<?php

use yii\db\Migration;

class m160906_022804_credit_trading_note extends Migration
{
    public function up()
    {
        $this->createTable('credit_note', [
            'id' => $this->primaryKey(),
            'asset_id' => $this->integer()->notNull(),
            'user_id' => $this->integer()->notNull(),
            'amount' => $this->decimal(14)->notNull(),
            'tradedAmount' => $this->decimal(14)->notNull(),
            'discountRate' => $this->decimal(5, 2)->notNull(),
            'isClosed' => $this->boolean()->notNull(),
            'isCancelled' => $this->boolean()->notNull(),
            'config' => $this->text()->notNull(),
            'isTest' => $this->boolean()->notNull(),
            'createTime' => $this->dateTime()->notNull(),
            'endTime' => $this->dateTime()->notNull(),
            'closeTime' => $this->dateTime(),
            'cancelTime' => $this->dateTime(),
        ]);
    }

    public function down()
    {
        $this->dropTable('credit_note');
    }
}
