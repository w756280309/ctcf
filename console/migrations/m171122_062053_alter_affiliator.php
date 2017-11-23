<?php

use yii\db\Migration;

class m171122_062053_alter_affiliator extends Migration
{
    public function up()
    {
        $this->addColumn('affiliator', 'isDel', $this->boolean()->defaultValue(0)->comment('是否删除'));
    }

    public function down()
    {
        echo "m171122_062053_alter_affiliator cannot be reverted.\n";

        return false;
    }
}
