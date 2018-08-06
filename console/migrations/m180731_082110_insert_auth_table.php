<?php

use yii\db\Migration;

class m180731_082110_insert_auth_table extends Migration
{
    public function safeUp()
    {
        $this->insert('auth', [
            'sn' => 'H100900',
            'psn' => 'H100000',
            'level' => '2',
            'auth_name' => '底层融资方列表',
            'path' => 'user/user/listob',
            'type' => '1',
            'auth_description' => '底层融资方列表信息',
            'status' => '1',
            'order_code' => '3',
        ]);
        $this->insert('auth', [
            'sn' => 'H100901',
            'psn' => 'H100900',
            'level' => '3',
            'auth_name' => '添加底层融资方',
            'path' => 'user/user/addob',
            'type' => '2',
            'auth_description' => '添加底层融资方信息',
            'status' => '1',
            'order_code' => '3',
        ]);
        $this->insert('auth', [
            'sn' => 'H100902',
            'psn' => 'H100900',
            'level' => '3',
            'auth_name' => '编辑底层融资方',
            'path' => 'user/user/editob',
            'type' => '2',
            'auth_description' => '编辑底层融资方信息',
            'status' => '1',
            'order_code' => '3',
        ]);
        $this->insert('auth', [
            'sn' => 'P200120',
            'psn' => 'P200100',
            'level' => '3',
            'auth_name' => '提交审核',
            'path' => 'product/productonline/submit-check',
            'type' => '2',
            'auth_description' => '标的提交审核',
            'status' => '1',
            'order_code' => '3',
        ]);
        $this->insert('auth', [
            'sn' => 'P200121',
            'psn' => 'P200100',
            'level' => '3',
            'auth_name' => '审核弹窗',
            'path' => 'product/productonline/check',
            'type' => '2',
            'auth_description' => '标的审核弹窗',
            'status' => '1',
            'order_code' => '3',
        ]);
        $this->insert('auth', [
            'sn' => 'P200122',
            'psn' => 'P200100',
            'level' => '3',
            'auth_name' => '审核',
            'path' => 'product/productonline/docheck',
            'type' => '2',
            'auth_description' => '标的审核',
            'status' => '1',
            'order_code' => '3',
        ]);
        $this->insert('auth', [
            'sn' => 'P200123',
            'psn' => 'P200100',
            'level' => '3',
            'auth_name' => '审核备注',
            'path' => 'product/productonline/check-remark',
            'type' => '2',
            'auth_description' => '标的审核备注',
            'status' => '1',
            'order_code' => '3',
        ]);
    }

    public function safeDown()
    {
        $this->delete('auth', ['path' => 'user/user/listob']);
        $this->delete('auth', ['path' => 'user/user/addob']);
        $this->delete('auth', ['path' => 'user/user/editob']);
        $this->delete('auth', ['path' => 'product/productonline/submit-check']);
        $this->delete('auth', ['path' => 'product/productonline/check']);
        $this->delete('auth', ['path' => 'product/productonline/docheck']);
        $this->delete('auth', ['path' => 'product/productonline/check-remark']);
    }
}
