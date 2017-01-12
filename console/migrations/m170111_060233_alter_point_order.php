<?php

use yii\db\Migration;

class m170111_060233_alter_point_order extends Migration
{
    public function up()
    {
        $this->addColumn('point_order', 'isOffline', $this->boolean()->defaultValue(false));
        $this->addColumn('point_order', 'offGoodsName', $this->string());
    }

    public function down()
    {
        echo "m170111_060233_alter_point_order cannot be reverted.\n";

        return false;
    }
}
