<?php

use yii\db\Migration;

class m170713_011630_ticket_token extends Migration
{
    public function safeUp()
    {
        $this->createTable('ticket_token', [
            'id' => $this->primaryKey(),
            'key' => $this->string(50)->unique()->notNull(),
        ]);
    }

    public function safeDown()
    {
        echo "m170713_011630_ticket_token cannot be reverted.\n";

        return false;
    }
}
