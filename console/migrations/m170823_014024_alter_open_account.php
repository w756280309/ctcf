<?php

use yii\db\Migration;

class m170823_014024_alter_open_account extends Migration
{
    public function safeUp()
    {
        $this->addColumn('open_account', 'code', $this->string(30));
    }

    public function safeDown()
    {
        echo "m170823_014024_alter_open_account cannot be reverted.\n";

        return false;
    }
}
