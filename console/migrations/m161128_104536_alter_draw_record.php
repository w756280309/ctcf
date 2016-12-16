<?php

use yii\db\Migration;

class m161128_104536_alter_draw_record extends Migration
{
    public function up()
    {
        $this->addColumn('draw_record', 'checkCount', $this->smallInteger()->defaultValue(0));
        $this->addColumn('draw_record', 'nextCronCheckTime', $this->integer());
    }

    public function down()
    {
        echo "m161128_104536_alter_draw_record cannot be reverted.\n";

        return false;
    }
}
