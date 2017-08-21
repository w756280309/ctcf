<?php

use yii\db\Migration;

class m170821_052444_alter_open_account extends Migration
{
    public function safeUp()
    {
        $this->addColumn('open_account', 'message', $this->string());
    }

    public function safeDown()
    {
        echo "m170821_052444_alter_open_account cannot be reverted.\n";

        return false;
    }
}
