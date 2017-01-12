<?php

use yii\db\Migration;

class m170111_060258_alter_point_record extends Migration
{
    public function up()
    {
        $this->addColumn('point_record', 'isOffline', $this->boolean()->defaultValue(false));
        $this->addColumn('point_record', 'offGoodsName', $this->string());
    }

    public function down()
    {
        echo "m170111_060258_alter_point_record cannot be reverted.\n";

        return false;
    }
}
