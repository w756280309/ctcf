<?php

use yii\db\Migration;

class m160704_065638_insert_auth_table extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'A101000',
            'psn' => 'A100000',
            'level' => '2',
            'auth_name' => '文件上传管理',
            'path' => 'adminupload/upload/index',
            'type' => '1',
            'auth_description' => '文件上传管理',
            'status' => '1',
            'order_code' => '4',
        ]);
        $this->insert('auth', [
            'sn' => 'A101001',
            'psn' => 'A101000',
            'level' => '3',
            'auth_name' => '新增及编辑',
            'path' => 'adminupload/upload/edit',
            'type' => '2',
            'auth_description' => '新增及编辑上传文件',
            'status' => '1',
            'order_code' => '4',
        ]);
        $this->insert('auth', [
            'sn' => 'A101002',
            'psn' => 'A101000',
            'level' => '3',
            'auth_name' => '删除',
            'path' => 'adminupload/upload/delete',
            'type' => '2',
            'auth_description' => '删除上传文件',
            'status' => '1',
            'order_code' => '4',
        ]);
    }

    public function down()
    {
        echo "m160704_065638_insert_auth_table cannot be reverted.\n";

        return false;
    }
}
