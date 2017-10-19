<?php

use yii\db\Migration;

class m171019_103209_add_idx_user_id extends Migration
{
    public function safeUp()
    {
        $this->createIndex('idx_user_id', 'point_record', 'user_id');
        $this->createIndex('idx_status', 'sms_message', 'status');
        $this->createIndex('idx_uid', 'money_record', 'uid');
        $this->createIndex('idx_status', 'transfer', 'status');
        $this->createIndex('idx_uid', 'draw_record', 'uid');
    }

    public function safeDown()
    {
        echo "m171019_103209_add_idx_user_id cannot be reverted.\n";

        return false;
    }
}
