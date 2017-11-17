<?php

use yii\db\Migration;

class m171116_084757_alter_user_info extends Migration
{
    public function safeUp()
    {
        $this->addColumn('user_info', 'creditInvestCount', $this->integer()->defaultValue(0));
        $this->addColumn('user_info', 'creditInvestTotal', $this->decimal(14,2)->defaultValue('0.00'));
    }

    public function safeDown()
    {
        echo "m171116_084757_alter_user_info cannot be reverted.\n";

        return false;
    }
}
