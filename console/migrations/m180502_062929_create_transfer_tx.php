<?php

use yii\db\Migration;

class m180502_062929_create_transfer_tx extends Migration
{
    public function safeUp()
    {
        $this->createTable('transfer_tx', [
            'id' => $this->primaryKey(),
            'sn' => $this->string()->unique()->notNull()->comment('流水号'),
            'userId' => $this->integer()->notNull()->comment('转账方用户ID'),
            'money' => $this->decimal(10, 2)->notNull()->comment('转账金额'),
            'status' => $this->smallInteger()->notNull()->defaultValue(0)->comment('转账状态'),
            'ref_sn' => $this->string()->null()->comment('关联业务流水号'),
            'lastCronCheckTime' => $this->dateTime()->null()->comment('上次查询时间'),
            'createTime' => $this->dateTime(),
            'updateTime' => $this->dateTime(),
        ]);
    }

    public function safeDown()
    {
        echo "m180502_062929_create_transfer_tx cannot be reverted.\n";

        return false;
    }
}
