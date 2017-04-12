<?php

use yii\db\Migration;

class m170406_064148_alter_sms_table extends Migration
{
    public function up()
    {
        $this->dropColumn('sms','mobile');
    }

    public function down()
    {
        echo "m170406_064148_alter_sms_table cannot be reverted.\n";

        return false;
    }

}
