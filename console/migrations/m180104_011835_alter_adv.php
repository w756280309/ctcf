<?php

use yii\db\Migration;

class m180104_011835_alter_adv extends Migration
{
    public function up()
    {
        $this->addColumn('adv', 'investLeast', $this->decimal(14, 2)->notNull()->defaultValue(0)->comment('最低投资可见'));
    }

    public function down()
    {
        echo "m180104_011835_alter_adv cannot be reverted.\n";

        return false;
    }
}
