<?php

use yii\db\Migration;

class m180404_052707_alter_transfer extends Migration
{
    public function safeUp()
    {
        $this->createIndex('idx_lastCronCheckTime', 'transfer', 'lastCronCheckTime');
    }

    public function safeDown()
    {
        echo "m180404_052707_alter_transfer cannot be reverted.\n";

        return false;
    }
}
