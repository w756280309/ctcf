<?php

use yii\db\Migration;

class m161221_114104_add_use extends Migration
{
    public function up()
    {
        $this->addColumn('user', 'registerIp', $this->string());
    }

    public function down()
    {
        echo "m161221_114104_add_use cannot be reverted.\n";

        return false;
    }

}
