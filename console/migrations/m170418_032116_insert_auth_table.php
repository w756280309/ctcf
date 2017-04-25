<?php

use yii\db\Migration;

class m170418_032116_insert_auth_table extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'O100200',
            'psn' => 'O100000',
            'level' => '2',
            'auth_name' => '标的列表',
            'path' => 'offline/offline/loanlist',
            'type' => '1',
            'auth_description' => '标的列表',
            'status' => '1',
            'order_code' => '7',
        ]);
        $this->insert('auth', [
            'sn' => 'O100201',
            'psn' => 'O100200',
            'level' => '3',
            'auth_name' => '新增标的',
            'path' => 'offline/offline/addloan',
            'type' => '2',
            'auth_description' => '新增标的',
            'status' => '1',
            'order_code' => '7',
        ]);
        $this->insert('auth', [
            'sn' => 'O100202',
            'psn' => 'O100200',
            'level' => '3',
            'auth_name' => '编辑标的',
            'path' => 'offline/offline/editloan',
            'type' => '2',
            'auth_description' => '编辑标的',
            'status' => '1',
            'order_code' => '7',
        ]);
        $this->insert('auth', [
            'sn' => 'O100203',
            'psn' => 'O100200',
            'level' => '3',
            'auth_name' => '删除标的',
            'path' => 'offline/offline/delloan',
            'type' => '2',
            'auth_description' => '删除标的',
            'status' => '1',
            'order_code' => '7',
        ]);
    }

    public function down()
    {
        echo "m170418_032116_insert_auth_table cannot be reverted.\n";

        return false;
    }

}
