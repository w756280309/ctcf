<?php

use yii\db\Migration;

class m180205_070810_create_session extends Migration
{
    public function safeUp()
    {
        $this->createTable('session', [
            'id' => $this->primaryKey(),
            'userId' => $this->integer()->notNull(),
            'batchSn' => $this->string()->notNull(),
            'createTime' => $this->dateTime(),
        ]);
    }

    public function safeDown()
    {
        echo "m180205_070810_create_session cannot be reverted.\n";

        return false;
    }
}
