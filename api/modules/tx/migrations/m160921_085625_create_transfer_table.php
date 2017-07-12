<?php

use yii\db\Migration;

class m160921_085625_create_transfer_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->createTable('transfer', [
            'id' => $this->primaryKey(),
            'sn' => $this->string(60)->notNull()->unique(),
            'type' => $this->string(60)->notNull(),
            'amount' => $this->decimal(14)->notNull(),
            'fromAccount' => $this->string(60)->notNull(),
            'toAccount' => $this->string(60)->notNull(),
            'sourceType' => $this->string(60),
            'sourceTxSn' => $this->string(60),
            'status' => $this->string(60),
            'createTime' => $this->datetime()->notNull(),
            'updateTime' => $this->datetime(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        $this->dropTable('transfer');
    }
}
