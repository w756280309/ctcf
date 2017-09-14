<?php

use yii\db\Migration;

class m170913_070920_add_auth extends Migration
{
    public function safeUp()
    {
        $this->insert('auth', [
            'sn' => 'P200204',
            'psn' => 'P200100',
            'level' => 3,
            'auth_name' => '标的信息导出',
            'path' => 'product/productonline/export',
            'type' => 2,
            'auth_description' => '标的信息导出',
            'status' => 1,
            'order_code' => 2,
        ]);
    }

    public function safeDown()
    {
        echo "m170913_070920_add_auth cannot be reverted.\n";

        return false;
    }
}
