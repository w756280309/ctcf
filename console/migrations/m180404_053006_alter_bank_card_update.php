<?php

use yii\db\Migration;

class m180404_053006_alter_bank_card_update extends Migration
{
    public function safeUp()
    {
        $this->createIndex('idx_uid', 'bank_card_update', 'uid');
        $this->createIndex('idx_oldSn', 'bank_card_update', 'oldSn');
    }

    public function safeDown()
    {
        echo "m180404_053006_alter_bank_card_update cannot be reverted.\n";

        return false;
    }
}
