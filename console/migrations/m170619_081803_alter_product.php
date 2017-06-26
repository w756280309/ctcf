<?php

use yii\db\Migration;

class m170619_081803_alter_product extends Migration
{
    public function up()
    {
        $this->addColumn('online_product', 'isJixiExamined', $this->boolean()->comment('计息审核')->defaultValue(true));
    }

    public function down()
    {
        echo "m170619_081803_alter_product cannot be reverted.\n";

        return false;
    }
}
