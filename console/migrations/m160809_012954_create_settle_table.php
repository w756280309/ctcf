<?php

use yii\db\Migration;

class m160809_012954_create_settle_table extends Migration
{
    public function up()
    {
        $this->createTable('settle', [
            'id' => $this->primaryKey(),
            'txSn' => $this->string(60)->notNull(),
            'txDate' => $this->date()->notNull(),
            'money' => $this->decimal(14, 2)->notNull(),
            'fee' => $this->decimal(4, 2)->notNull(),
            'serviceSn' => $this->string(60)->notNull(),
            'txType' => $this->integer()->notNull(),
            'status' => $this->integer()->defaultValue(0),
        ]);
    }

    public function down()
    {
        $this->dropTable('settle');
    }
}
