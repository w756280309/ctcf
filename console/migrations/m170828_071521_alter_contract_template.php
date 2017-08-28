<?php

use yii\db\Migration;

class m170828_071521_alter_contract_template extends Migration
{
    public function safeUp()
    {
        $this->createIndex('pid', 'contract_template', 'pid');
    }

    public function safeDown()
    {
        echo "m170828_071521_alter_contract_template cannot be reverted.\n";

        return false;
    }
}
