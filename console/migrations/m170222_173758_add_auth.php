<?php

use yii\db\Migration;

class m170222_173758_add_auth extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'A101104',
            'psn' => 'A101100',
            'level' => '3',
            'auth_name' => '精选项目介绍页列表',
            'path' => 'product/jing-xuan/list',
            'type' => '1',
            'auth_description' => '精选项目介绍页列表',
            'status' => '1',
            'order_code' => '4',
        ]);

        $this->insert('auth', [
            'sn' => 'A101105',
            'psn' => 'A101100',
            'level' => '3',
            'auth_name' => '添加精选项目介绍页',
            'path' => 'product/jing-xuan/add',
            'type' => '2',
            'auth_description' => '添加精选项目介绍页',
            'status' => '1',
            'order_code' => '4',
        ]);

        $this->insert('auth', [
            'sn' => 'A101106',
            'psn' => 'A101100',
            'level' => '3',
            'auth_name' => '编辑精选项目介绍页',
            'path' => 'product/jing-xuan/edit',
            'type' => '2',
            'auth_description' => '编辑精选项目介绍页',
            'status' => '1',
            'order_code' => '4',
        ]);
    }

    public function down()
    {
        echo "m170222_173758_add_auth cannot be reverted.\n";

        return false;
    }
}
