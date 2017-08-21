<?php

use yii\db\Migration;

class m170817_023510_add_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('open_account', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'encryptedName' => $this->string(),
            'encryptedIdCard' => $this->string(),
            'status' => $this->string(10),
            'ip' => $this->string(),
            'createTime' => $this->dateTime(),
            'updateTime' => $this->dateTime(),
        ]);
    }

    public function safeDown()
    {
        echo "m170817_023510_add_table cannot be reverted.\n";

        return false;
    }
}
