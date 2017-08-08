<?php

use yii\db\Migration;

class m170807_033449_alter_offline_user extends Migration
{
    public function Up()
    {
        $this->addColumn('offline_user', 'created_at', $this->integer(10)->null());
    }

    public function Down()
    {
        echo "m170807_033449_alter_offline_user cannot be reverted.\n";

        return false;
    }
}
