<?php

use yii\db\Migration;

class m180316_090651_alter_online_product extends Migration
{
    public function safeUp()
    {
        $this->addColumn('online_product', 'flexRepay', $this->boolean()->defaultValue(false)->comment('灵活还款1是0否'));
    }

    public function safeDown()
    {
        echo "m180316_090651_alter_online_product cannot be reverted.\n";

        return false;
    }
}
