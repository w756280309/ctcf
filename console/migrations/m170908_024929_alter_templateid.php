<?php

use yii\db\Migration;

class m170908_024929_alter_templateid extends Migration
{
    public function safeUp()
    {
        $this->alterColumn('sms_message', 'template_id', $this->string(32));
    }

    public function safeDown()
    {
        echo "m170908_024929_alter_templateid cannot be reverted.\n";

        return false;
    }
}
