<?php

use yii\db\Migration;

class m161117_014225_alter_adv extends Migration
{
    public function up()
    {
        $this->addColumn('adv', 'share_id', $this->integer());
    }

    public function down()
    {
        echo "m161117_014225_alter_adv cannot be reverted.\n";

        return false;
    }
}
