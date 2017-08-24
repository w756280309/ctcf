<?php

use yii\db\Migration;

class m170824_114808_alter_award extends Migration
{
    public function safeUp()
    {
        $this->addColumn('award', 'reward_id', $this->integer()->null());
    }

    public function safeDown()
    {
        echo "m170824_114808_alter_award cannot be reverted.\n";

        return false;
    }
}
