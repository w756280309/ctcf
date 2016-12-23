<?php

use yii\db\Migration;

class m161223_011138_alter_point_order extends Migration
{
    public function up()
    {
       $this->dropColumn('point_order', 'mallUrl');
    }

    public function down()
    {
        echo "m161223_011138_alter_point_order cannot be reverted.\n";

        return false;
    }
}
