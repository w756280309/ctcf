<?php

use yii\db\Migration;

class m180326_062021_update_qpayconfig extends Migration
{
    public function safeUp()
    {
        $this->getDb()->createCommand('update qpayconfig set allowBind = true where isDisabled = false
')->query();
    }

    public function safeDown()
    {
        echo "m180326_062021_update_qpayconfig cannot be reverted.\n";

        return false;
    }
}
