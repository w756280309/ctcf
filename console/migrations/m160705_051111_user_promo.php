<?php

use yii\db\Migration;

class m160705_051111_user_promo extends Migration
{
    public function up()
    {
        $this->createTable('user_promo', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'promo_key' => $this->string(50),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);
        $this->createIndex('unique_uid_key', 'user_promo', ['user_id', 'promo_key'], true);
    }

    public function down()
    {
        $this->dropTable('user_promo');
        return false;
    }
}
