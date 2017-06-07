<?php

use yii\db\Migration;

class m170606_024931_social_connect_log extends Migration
{
    public function up()
    {
        $this->createTable('social_connect_log', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->unsigned()->notNull(),
            'resourceOwner_id' => $this->string(128)->notNull(),
            'action' => $this->string(20),
            'provider_type' => $this->string(20),
            'data' => $this->text(),
            'createTime' => $this->dateTime(),
        ]);
    }

    public function down()
    {
        echo "m170606_024931_social_connect_log cannot be reverted.\n";

        return false;
    }
}
