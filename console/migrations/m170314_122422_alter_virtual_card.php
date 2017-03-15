<?php

use yii\db\Migration;

class m170314_122422_alter_virtual_card extends Migration
{
    public function up()
    {
        $this->addColumn('virtual_card', 'expiredTime', $this->dateTime());
        $this->addColumn('virtual_card', 'affiliator_id', $this->integer());
        $this->addColumn('virtual_card', 'usedMobile', $this->string(20));
    }

    public function down()
    {
        echo "m170314_122422_alter_virtual_card cannot be reverted.\n";

        return false;
    }
}
