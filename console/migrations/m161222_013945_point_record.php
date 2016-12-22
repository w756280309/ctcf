<?php

use yii\db\Migration;

class m161222_013945_point_record extends Migration
{
    public function up()
    {
        $this->createTable('point_record', [
            'id' => $this->primaryKey(),
            'sn' => $this->string(32),
            'user_id' => $this->integer(),
            'ref_type' => $this->string(32),
            'ref_id' => $this->integer(),
            'incr_points' => $this->integer(),
            'decr_points' => $this->integer(),
            'final_points' => $this->integer(),
            'recordTime' => $this->dateTime(),
        ]);
    }

    public function down()
    {
        echo "m161222_013945_point_record cannot be reverted.\n";

        return false;
    }
}
