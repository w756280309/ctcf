<?php

use yii\db\Migration;

class m160918_020854_create_credit_note_order_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable('credit_order', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'note_id' => $this->integer()->notNull(),
            'asset_id' => $this->integer()->notNull(),
            'amount' => $this->decimal(14)->notNull(),
            'fee' => $this->decimal(14)->notNull(),
            'principal' => $this->decimal(14)->notNull(),
            'interest' => $this->decimal(14)->notNull(),
            'status' => $this->smallInteger()->notNull(),
            'createTime' => $this->dateTime()->notNull(),
            'updateTime' => $this->dateTime(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable('credit_order');
    }
}
