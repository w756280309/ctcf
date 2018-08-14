<?php

use yii\db\Migration;

class m180814_052013_insert_auth_table extends Migration
{
    public function safeUp()
    {
        $this->insert('auth', [
            'sn' => 'P200304',
            'psn' => 'P200300',
            'level' => '3',
            'auth_name' => '导出转让列表',
            'path' => 'product/productonline/transfer-export',
            'type' => '2',
            'auth_description' => '导出转让列表',
            'status' => '1',
            'order_code' => '3',
        ]);
    }

    public function safeDown()
    {
        $this->delete('auth', ['path' => 'product/productonline/transfer-export']);
    }
}
