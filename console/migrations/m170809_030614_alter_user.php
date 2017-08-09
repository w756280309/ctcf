<?php

use yii\db\Migration;

class m170809_030614_alter_user extends Migration
{
    public function Up()
    {
        $this->dropIndex('unique_account_id', 'user');
    }

    public function Down()
    {
        echo "m170809_030614_alter_user cannot be reverted.\n";

        return false;
    }
}
