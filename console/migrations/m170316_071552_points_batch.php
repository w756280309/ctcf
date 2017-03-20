<?php

use yii\db\Migration;

class m170316_071552_points_batch extends Migration
{
    public function up()
    {
        $this->createTable('points_batch', [
            'id' => $this->primaryKey(),
            'batchSn' => $this->string(32)->notNull(),
            'createTime' => $this->dateTime(),
            'isOnline' => $this->boolean(),
            'publicMobile' => $this->string(11),
            'safeMobile' => $this->string(),
            'points' => $this->integer()->notNull(),
            'desc' => $this->string(),
            'status' => $this->smallInteger(),
        ]);
    }

    public function down()
    {
        echo "m170316_071552_points_batch cannot be reverted.\n";

        return false;
    }
}
