<?php

use yii\db\Migration;

class m160720_033626_repayment extends Migration
{
    public function up()
    {
        $this->createTable('repayment', [
            'id' => $this->primaryKey(),
            'loan_id' => $this->integer(),//标的ID
            'term' => $this->integer(2),//分期期数
            'dueDate' => $this->date(),//预期还款时间
            'amount' => $this->decimal(14, 2),//应还总金额
            'principal' => $this->decimal(14, 2),//本金
            'interest' => $this->decimal(14, 2),//利息
            'isRepaid' => $this->integer(1)->defaultValue(0),//是否还款，指融资用户扣钱
            'repaidAt' => $this->dateTime(),//还款时间，融资用户扣完钱之后
            'isRefunded' => $this->integer(1)->defaultValue(0),//是否回款,指给用户转钱
            'refundedAt' => $this->dateTime(),//回款时间,当期所有订单还完之后
        ]);
    }

    public function down()
    {
        $this->dropTable('repayment');
    }
}
