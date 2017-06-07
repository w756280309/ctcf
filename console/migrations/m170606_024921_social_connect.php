<?php

use yii\db\Migration;

class m170606_024921_social_connect extends Migration
{
    public function up()
    {
        $this->createTable('social_connect', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->unsigned()->notNull(),
            'resourceOwner_id' => $this->string(128)->notNull(),
            'provider_type' => $this->string(20),
            'createTime' => $this->dateTime(),
        ]);
        $this->createIndex('unique_owner', ['resourceOwner_id', 'provider_type'], true);
    }

    public function down()
    {
        echo "m170606_024921_social_connect cannot be reverted.\n";

        return false;
    }
}
