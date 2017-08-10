<?php

use yii\db\Migration;

class m170808_061948_add_auth extends Migration
{
    public function safeUp()
    {
        $this->update('auth', [
            'order_code' => 8
        ], [
            'sn' => 'T100000',
        ]);

        $this->insert('auth', [
            'sn' => 'P200202',
            'psn' => 'P200100',
            'level' => 3,
            'auth_name' => '打印确认函',
            'path' => 'product/growth/letter',
            'type' => 2,
            'auth_description' => '打印确认函',
            'status' => 1,
            'order_code' => 2,
        ]);
    }

    public function safeDown()
    {
        echo "m170808_061948_add_auth cannot be reverted.\n";

        return false;
    }
}
