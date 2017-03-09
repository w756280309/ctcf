<?php

use yii\db\Migration;

class m170308_113144_create_virtual_card extends Migration
{
    public function up()
    {
        $this->createTable('virtual_card', [
            'id' => $this->primaryKey(),
            'serial' => $this->string(50)->unique()->notNull(),
            'secret' => $this->string(),
            'user_id' => $this->integer(),
            'isPull' => $this->boolean()->defaultValue(false),
            'pullTime' => $this->dateTime(),
            'isUsed' => $this->boolean()->defaultValue(false),
            'usedTime' => $this->dateTime(),
            'createTime' => $this->dateTime(),
            'goodsType_id' => $this->integer()->notNull(),
        ]);
    }

    public function down()
    {
        echo "m170308_113144_create_virtual_card cannot be reverted.\n";

        return false;
    }
}
