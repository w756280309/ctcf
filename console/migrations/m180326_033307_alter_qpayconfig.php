<?php

use yii\db\Migration;

class m180326_033307_alter_qpayconfig extends Migration
{
    public function safeUp()
    {
        $this->addColumn('qpayconfig', 'allowBind', $this->boolean()->defaultValue(false));
    }

    public function safeDown()
    {
        echo "m180326_033307_alter_qpayconfig cannot be reverted.\n";

        return false;
    }
}
