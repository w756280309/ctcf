<?php

use yii\db\Migration;

class m160902_085005_settle extends Migration
{
    public function up()
    {
        $this->createTable('settle', [
            'id' => $this->primaryKey(),
            'txSn' => $this->string(60)->notNull()->unique(),
            'fee' => $this->decimal(14),
            'amount' => $this->decimal(14),
            'txType' => $this->smallInteger()->notNull(),
            'txDate' => $this->date(),
            'fcFee' => $this->decimal(14),
            'fcAmount' => $this->decimal(14),
            'fcDate' => $this->date(),
            'fcSn' => $this->string(60),
            'settleDate' => $this->date(),
            'isChecked' => $this->boolean()->notNull(),
            'isSettled' => $this->boolean()->notNull(),
        ]);
    }

    public function down()
    {
        echo "m160902_085005_settle cannot be reverted.\n";

        return false;
    }
}
