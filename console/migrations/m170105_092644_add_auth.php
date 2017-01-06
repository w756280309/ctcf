<?php

use yii\db\Migration;

class m170105_092644_add_auth extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'A101300',
            'psn' => 'A100000',
            'level' => '2',
            'auth_name' => '兑换码管理',
            'path' => 'growth/code/goods-list',
            'type' => '1',
            'auth_description' => '兑换码管理',
            'status' => '1',
            'order_code' => '4',
        ]);

        $this->insert('auth', [
            'sn' => 'A101301',
            'psn' => 'A101300',
            'level' => '3',
            'auth_name' => '添加兑换码',
            'path' => 'growth/code/add',
            'type' => '2',
            'auth_description' => '添加兑换码',
            'status' => '1',
            'order_code' => '4',
        ]);

        $this->insert('auth', [
            'sn' => 'A101302',
            'psn' => 'A101300',
            'level' => '3',
            'auth_name' => '查看兑换码列表',
            'path' => 'growth/code/list',
            'type' => '2',
            'auth_description' => '查看兑换码列表',
            'status' => '1',
            'order_code' => '4',
        ]);

        $this->insert('auth', [
            'sn' => 'A101303',
            'psn' => 'A101300',
            'level' => '3',
            'auth_name' => '导出兑换码TXT',
            'path' => 'growth/code/export-all',
            'type' => '2',
            'auth_description' => '导出兑换码TXT',
            'status' => '1',
            'order_code' => '4',
        ]);

        $this->insert('auth', [
            'sn' => 'A101304',
            'psn' => 'A101300',
            'level' => '3',
            'auth_name' => '插入并导出兑换码',
            'path' => 'growth/code/create',
            'type' => '2',
            'auth_description' => '插入并导出兑换码',
            'status' => '1',
            'order_code' => '4',
        ]);

        $this->insert('auth', [
            'sn' => 'A101305',
            'psn' => 'A101300',
            'level' => '3',
            'auth_name' => '补充领取人',
            'path' => 'growth/code/pull-user',
            'type' => '2',
            'auth_description' => '补充领取人',
            'status' => '1',
            'order_code' => '4',
        ]);

        $this->insert('auth', [
            'sn' => 'A101306',
            'psn' => 'A101300',
            'level' => '3',
            'auth_name' => '确认补充领取人',
            'path' => 'growth/code/draw',
            'type' => '2',
            'auth_description' => '确认补充领取人',
            'status' => '1',
            'order_code' => '4',
        ]);
    }

    public function down()
    {
        echo "m170105_092644_add_auth cannot be reverted.\n";

        return false;
    }
}
