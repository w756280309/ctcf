<?php

use yii\db\Migration;

class m170615_055231_alter_callout extends Migration
{
    public function up()
    {
        $this->addColumn('callout', 'callerOpenId', $this->string(128)->null());
    }

    public function down()
    {
        echo "m170615_055231_alter_callout cannot be reverted.\n";

        return false;
    }
}
