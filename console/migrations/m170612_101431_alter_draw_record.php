<?php

use yii\db\Migration;

class m170612_101431_alter_draw_record extends Migration
{
    public function up()
    {
        $this->dropColumn('draw_record', 'nextCronCheckTime');
        $this->dropColumn('draw_record', 'checkCount');
    }

    public function down()
    {
        echo "m170612_101431_alter_draw_record cannot be reverted.\n";

        return false;
    }
}
