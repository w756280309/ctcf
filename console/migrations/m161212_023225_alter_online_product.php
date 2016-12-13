<?php

use yii\db\Migration;

class m161212_023225_alter_online_product extends Migration
{
    public function up()
    {
        $this->addColumn('online_product', 'tags', $this->string(255));
    }

    public function down()
    {
        echo "m161212_023225_alter_online_product cannot be reverted.\n";

        return false;
    }
}
