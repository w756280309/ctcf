<?php

use yii\db\Migration;

class m161222_013937_point_order extends Migration
{
    public function up()
    {
        $this->createTable('point_order', [
            'id' => $this->primaryKey(),
            'sn' => $this->string(32),
            'orderNum' => $this->string(),
            'user_id' => $this->integer(),
            'points' => $this->integer(),
            'orderTime' => $this->dateTime(),
            'isPaid' => $this->boolean(),
            'status' => $this->smallInteger(),
            'mallUrl' => $this->string(),
            'createdAt' => $this->integer(),
            'updatedAt' => $this->integer(),
        ]);
    }

    public function down()
    {
        echo "m161222_013937_point_order cannot be reverted.\n";

        return false;
    }
}
