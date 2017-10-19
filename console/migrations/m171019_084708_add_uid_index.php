<?php

use yii\db\Migration;

class m171019_084708_add_uid_index extends Migration
{
    public function safeUp()
    {
        $this->createIndex('idx_user_id', 'user_coupon', 'user_id');
        $this->createIndex('idx_uid', 'online_repayment_record', 'uid');
        $this->createIndex('idx_uid', 'user_account', 'uid');
    }

    public function safeDown()
    {
        echo "m171019_084708_add_uid_index cannot be reverted.\n";

        return false;
    }
}
