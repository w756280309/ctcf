<?php

use yii\db\Migration;

class m170406_051337_create_reward extends Migration
{
    public function up()
    {
        $this->createTable('reward', [
            'id' => $this->primaryKey(),
            'sn' => $this->string(50)->unique()->null(),
            'name' => $this->string(100)->notNull(),
            'limit' => $this->integer()->unsigned()->defaultValue(0),
            'ref_type' => $this->string()->notNull(),
            'ref_amount' => $this->decimal(14, 2),
            'path' => $this->string()->null(),
            'promo_id' => $this->integer()->null(),
            'createTime' => $this->dateTime()->null(),
        ]);
    }

    public function down()
    {
        echo "m170406_051337_create_reward cannot be reverted.\n";

        return false;
    }
}
