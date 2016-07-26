<?php

use yii\db\Migration;

class m160725_055112_alter_order extends Migration
{
    public function up()
    {
        //投资来源 0表示未知，1表示wap，2表示wx，3表示app，4表示pc
        $this->addColumn('online_order', 'investFrom', $this->integer(1)->defaultValue(0));
    }

    public function down()
    {
        echo "m160725_055112_alter_order cannot be reverted.\n";

        return false;
    }
}
