<?php

use yii\db\Migration;

class m180104_075143_insert_auth_table extends Migration
{
    public function Up()
    {
        $this->insert('auth', [
            'sn' => 'P200205',
            'psn' => 'P200100',
            'level' => '3',
            'auth_name' => '基础信息编辑',
            'path' => 'product/productonline/senior-edit',
            'type' => '2',
            'auth_description' => '基础信息编辑',
            'status' => '1',
            'order_code' => '2',
        ]);
    }

    public function Down()
    {
        echo "m180104_075143_insert_auth_table cannot be reverted.\n";

        return false;
    }
}
