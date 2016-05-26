<?php

use yii\db\Migration;

class m160524_091620_update_auth_table extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'A100800',
            'psn' => 'A100000',
            'level' => '2',
            'auth_name' => '渠道管理',
            'path' => 'fenxiao/fenxiao/list',
            'type' => '1',
            'auth_description' => '渠道管理',
            'status' => '1',
            'order_code' => '4',
        ]);

        $this->insert('auth', [
            'sn' => 'A100801',
            'psn' => 'A100800',
            'level' => '3',
            'auth_name' => '添加渠道',
            'path' => 'fenxiao/fenxiao/add',
            'type' => '2',
            'auth_description' => '添加渠道用户',
            'status' => '1',
            'order_code' => '4',
        ]);

        $this->insert('auth', [
            'sn' => 'A100802',
            'psn' => 'A100800',
            'level' => '3',
            'auth_name' => '编辑渠道',
            'path' => 'fenxiao/fenxiao/edit',
            'type' => '2',
            'auth_description' => '编辑渠道用户',
            'status' => '1',
            'order_code' => '4',
        ]);
    }

    public function down()
    {
        echo "m160524_091620_update_auth_table cannot be reverted.\n";

        return false;
    }
}
