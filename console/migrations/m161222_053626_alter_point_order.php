<?php

use yii\db\Migration;

class m161222_053626_alter_point_order extends Migration
{
    public function up()
    {
        $this->addColumn('point_order', 'type', $this->string());
    }

    public function down()
    {
        echo "m161222_053626_alter_point_order cannot be reverted.\n";

        return false;
    }
}
