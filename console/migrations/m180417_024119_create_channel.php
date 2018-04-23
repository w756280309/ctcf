<?php

use yii\db\Migration;

class m180417_024119_create_channel extends Migration
{
    public function safeUp()
    {
        $this->createTable('channel', [
            'id' => $this->primaryKey(),
            'userId' => $this->integer()->notNull()->unique(),
            'thirdPartyUser_id' => $this->string()->notNull()->unique(),
            'createTime' => $this->dateTime(),
        ]);
    }

    public function safeDown()
    {
        echo "m180417_024119_create_njq cannot be reverted.\n";

        return false;
    }
}
