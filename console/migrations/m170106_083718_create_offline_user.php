<?php

use yii\db\Migration;

class m170106_083718_create_offline_user extends Migration
{
    public function up()
    {
        $this->createTable('offline_user', [
            'id' => $this->primaryKey(),
            'realName' => $this->string(50)->notNull(),
            'mobile' => $this->string(20)->notNull(),
            'idCard' => $this->string(30)->notNull(),
        ]);
    }

    public function down()
    {
        echo "m170106_083718_create_offline_user cannot be reverted.\n";

        return false;
    }
}
