<?php

use yii\db\Migration;

class m161108_020150_insert_auth_table extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'A101100',
            'psn' => 'A100000',
            'level' => '2',
            'auth_name' => '发行方管理',
            'path' => 'product/issuer/list',
            'type' => '1',
            'auth_description' => '发行方管理',
            'status' => '1',
            'order_code' => '4',
        ]);

        $this->insert('auth', [
            'sn' => 'A101101',
            'psn' => 'A101100',
            'level' => '3',
            'auth_name' => '添加发行方',
            'path' => 'product/issuer/add',
            'type' => '2',
            'auth_description' => '添加发行方',
            'status' => '1',
            'order_code' => '4',
        ]);

        $this->insert('auth', [
            'sn' => 'A101102',
            'psn' => 'A101100',
            'level' => '3',
            'auth_name' => '编辑发行方',
            'path' => 'product/issuer/edit',
            'type' => '2',
            'auth_description' => '编辑发行方',
            'status' => '1',
            'order_code' => '4',
        ]);
    }

    public function down()
    {
        echo "m161108_020150_insert_auth_table cannot be reverted.\n";

        return false;
    }
}
