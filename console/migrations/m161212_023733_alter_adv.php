<?php

use yii\db\Migration;

class m161212_023733_alter_adv extends Migration
{
    public function up()
    {
        $this->addColumn('adv', 'type', $this->smallInteger()->defaultValue(0));
        $this->addColumn('adv', 'smallImage', $this->string(60));
        $this->addColumn('adv', 'largeImage', $this->string(60));
    }

    public function down()
    {
        echo "m161212_023733_alter_adv cannot be reverted.\n";

        return false;
    }
}
