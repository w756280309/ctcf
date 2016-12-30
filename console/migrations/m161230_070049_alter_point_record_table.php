<?php

use yii\db\Migration;

class m161230_070049_alter_point_record_table extends Migration
{
    public function up()
    {
        $this->addColumn('point_record', 'userLevel', $this->integer());
    }

    public function down()
    {
        echo "m161230_070049_alter_point_record_table cannot be reverted.\n";

        return false;
    }
}
