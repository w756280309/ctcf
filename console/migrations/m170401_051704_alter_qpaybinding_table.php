<?php

use yii\db\Migration;

class m170401_051704_alter_qpaybinding_table extends Migration
{
    public function up()
    {
        //邦卡：删除 邦卡记录表 qpaybinding 的 mobile 字段 mobile字段为null
        $this->dropColumn('qpaybinding', 'mobile');
    }

    public function down()
    {
        echo "m170401_051704_alter_qpaybinding_table cannot be reverted.\n";

        return false;
    }

}
