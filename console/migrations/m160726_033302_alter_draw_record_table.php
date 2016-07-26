<?php

use yii\db\Migration;

class m160726_033302_alter_draw_record_table extends Migration
{
    public function up()
    {
        $this->alterColumn('draw_record', 'sub_bank_name', $this->string());
        $this->alterColumn('draw_record', 'province', $this->string(30));
        $this->alterColumn('draw_record', 'city', $this->string(30));
    }

    public function down()
    {
        echo "m160726_033302_alter_draw_record_table cannot be reverted.\n";

        return false;
    }
}
