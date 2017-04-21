<?php

use yii\db\Migration;

class m170421_024709_alter_queue_task extends Migration
{
    public function up()
    {
        $this->alterColumn('queue_task', 'topic', $this->string());
        $this->renameColumn('queue_task', 'topic', 'runnable');
        $this->renameColumn('queue_task', 'command', 'params');
        $this->dropColumn('queue_task', 'sn');
    }

    public function down()
    {
        echo "m170421_024709_alter_queue_task cannot be reverted.\n";

        return false;
    }
}
