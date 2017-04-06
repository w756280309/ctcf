<?php

use yii\db\Migration;

class m170330_060038_queue_task extends Migration
{
    public function up()
    {
        $this->createTable('queue_task', [
            'id' => $this->primaryKey(),
            'sn' => $this->string(),
            'topic' => $this->string(32),
            'command' => $this->text(),
            'status' => $this->smallInteger()->defaultValue(0),
            'weight' => $this->smallInteger()->defaultValue(1),
            'runCount' => $this->integer()->defaultValue(0),
            'lastRunTime' => $this->dateTime(),
            'createTime' => $this->dateTime(),
            'finishTime' => $this->dateTime(),
        ]);
    }

    public function down()
    {
        echo "m170330_060038_queue_task cannot be reverted.\n";

        return false;
    }
}
