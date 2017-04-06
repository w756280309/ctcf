<?php

use yii\db\Migration;

class m170401_064636_alter_queue_task extends Migration
{
    public function up()
    {
        $this->addColumn('queue_task', 'runLimit', $this->integer());
        $this->addColumn('queue_task', 'nextRunTime', $this->dateTime());
    }

    public function down()
    {
        echo "m170401_064636_alter_queue_task cannot be reverted.\n";

        return false;
    }
}
