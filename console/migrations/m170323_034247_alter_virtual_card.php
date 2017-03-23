<?php

use yii\db\Migration;

class m170323_034247_alter_virtual_card extends Migration
{
    public function up()
    {
        $this->addColumn('virtual_card', 'isReserved', $this->boolean()->defaultValue(false));
    }

    public function down()
    {
        echo "m170323_034247_alter_virtual_card cannot be reverted.\n";

        return false;
    }
}
