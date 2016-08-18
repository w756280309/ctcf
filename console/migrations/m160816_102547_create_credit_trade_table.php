<?php

use yii\db\Migration;

class m160816_102547_create_credit_trade_table extends Migration
{
    public function up()
    {
        $this->createTable('credit_trade', [
            'id' => $this->primaryKey(),
            'loan_id' => $this->integer(10)->notNull(),
            'order_id' => $this->integer(10)->notNull(),
            'user_id' => $this->integer(10)->notNull(),
            'amount' => $this->decimal(14, 2)->notNull(),
            'tradedAmount' => $this->decimal(14, 2)->notNull(),
            'discountRate' => $this->decimal(4, 2)->notNull(),
            'status' => $this->integer(1)->notNull(),
            'holdDays' => $this->integer(5)->notNull(),
            'maxDiscountRate' => $this->decimal(4, 2)->notNull(),
            'tradeCountLimit' => $this->integer(2)->notNull(),
            'feeRate' => $this->decimal(8, 6)->notNull(),
            'minOrderAmount' => $this->decimal(14, 2)->notNull(),
            'incrOrderAmount' => $this->decimal(14, 2)->notNull(),
            'isTest' => $this->boolean()->notNull(),
            'createTime' => $this->dateTime()->notNull(),
            'endTime' => $this->dateTime()->notNull(),
            'closeTime' => $this->dateTime(),
            'cancelTime' => $this->dateTime(),
        ]);
    }

    public function down()
    {
        $this->dropTable('credit_trade');
    }
}
