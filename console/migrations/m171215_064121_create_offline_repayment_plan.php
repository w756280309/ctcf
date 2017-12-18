<?php

use yii\db\Migration;

class m171215_064121_create_offline_repayment_plan extends Migration
{
    public function up()
    {
        $this->createTable('offline_repayment_plan', [
            'id' => $this->primaryKey()->unsigned(),
            'loan_id' => $this->integer()->notNull()->comment('线下标的id'),
            'sn' => $this->string()->unique()->notNull()->comment('计划编号'),
            'order_id' => $this->integer()->notNull()->comment('线下订单id'),
            'qishu' => $this->integer()->notNull()->comment('还款期数'),
            'uid' => $this->integer()->notNull()->comment('用户id'),
            'benxi' => $this->decimal(14,2)->notNull()->comment('本息'),
            'benjin' => $this->decimal(14,2)->notNull()->defaultValue(0)->comment('应还本金'),
            'lixi' => $this->decimal(14,2)->notNull()->comment('应还利息'),
            'refund_time' => $this->date()->notNull()->comment('计划还款时间'),
            'actualRefundTime' => $this->date()->notNull()->defaultValue(null)->comment('实际还款时间'),
            'status' => $this->integer()->notNull()->defaultValue(0)->comment('0、未还 1、已还 2、提前还款 3，无效'),
            'created_at' => $this->integer()->notNull(),
            'updated_at' => $this->integer()->notNull(),
            'operator' => $this->integer()->notNull()->comment('操作人'),
        ]);
    }

    public function down()
    {
        echo "m171215_064121_create_offline_repayment_plan cannot be reverted.\n";

        return false;
    }
}
