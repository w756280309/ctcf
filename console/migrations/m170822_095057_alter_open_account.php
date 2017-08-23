<?php

use yii\db\Migration;

class m170822_095057_alter_open_account extends Migration
{
    public function safeUp()
    {
        $this->addColumn('open_account', 'sn', $this->string(30));
    }

    public function safeDown()
    {
        echo "m170822_095057_alter_open_account cannot be reverted.\n";

        return false;
    }
}
