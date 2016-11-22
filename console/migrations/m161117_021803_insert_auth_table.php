<?php

use yii\db\Migration;

class m161117_021803_insert_auth_table extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'P200116',
            'psn' => 'P200100',
            'level' => '3',
            'auth_name' => '新增标的',
            'path' => 'product/productonline/add',
            'type' => '2',
            'auth_description' => '新增标的',
            'status' => '1',
            'order_code' => '2',
        ]);

        $this->update('auth', [
            'auth_name' => '编辑标的',
            'auth_description' => '编辑标的',
        ], [
            'path' => 'product/productonline/edit',
        ]);
    }

    public function down()
    {
        echo "m161117_021803_insert_auth_table cannot be reverted.\n";

        return false;
    }
}
