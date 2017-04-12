<?php

use yii\db\Migration;

class m170406_065324_alter_sms_message_table extends Migration
{
    public function up()
    {
        $this->dropColumn('sms_message','mobile');
    }

    public function down()
    {
        echo "m170406_065324_alter_sms_message_table cannot be reverted.\n";

        return false;
    }

}
