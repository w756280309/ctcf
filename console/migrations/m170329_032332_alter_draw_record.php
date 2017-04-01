<?php

use yii\db\Migration;

class m170329_032332_alter_draw_record extends Migration
{
    public function up()
    {
        //提现：删除提现表（draw_record）的mobile
        $this->dropColumn('draw_record', 'mobile');
    }

    public function down()
    {
        echo "m170329_032332_alter_draw_record cannot be reverted.\n";

        return false;
    }

}
