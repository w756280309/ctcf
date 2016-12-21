<?php

use yii\db\Migration;

class m161220_022159_insert_auth_table extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'A101200',
            'psn' => 'A100000',
            'level' => '2',
            'auth_name' => '页面META',
            'path' => 'growth/page-meta/list',
            'type' => '1',
            'auth_description' => '页面META',
            'status' => '1',
            'order_code' => '4',
        ]);

        $this->insert('auth', [
            'sn' => 'A101201',
            'psn' => 'A101200',
            'level' => '3',
            'auth_name' => '添加META',
            'path' => 'growth/page-meta/add',
            'type' => '2',
            'auth_description' => '添加META',
            'status' => '1',
            'order_code' => '4',
        ]);

        $this->insert('auth', [
            'sn' => 'A101202',
            'psn' => 'A101200',
            'level' => '3',
            'auth_name' => '编辑META',
            'path' => 'growth/page-meta/edit',
            'type' => '2',
            'auth_description' => '编辑META',
            'status' => '1',
            'order_code' => '4',
        ]);
    }

    public function down()
    {
        echo "m161220_022159_insert_auth_table cannot be reverted.\n";

        return false;
    }
}
