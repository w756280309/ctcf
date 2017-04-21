<?php

use yii\db\Migration;

class m170419_081213_insert_auth_table extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'O100204',
            'psn' => 'O100200',
            'level' => '3',
            'auth_name' => '分期列表',
            'path' => 'offline/offline/repayment',
            'type' => '2',
            'auth_description' => '标的分期列表',
            'status' => '1',
            'order_code' => '7',
        ]);
        $this->insert('auth', [
            'sn' => 'O100205',
            'psn' => 'O100200',
            'level' => '3',
            'auth_name' => '删除分期',
            'path' => 'offline/offline/delrpm',
            'type' => '2',
            'auth_description' => '删除标的分期',
            'status' => '1',
            'order_code' => '7',
        ]);
        $this->insert('auth', [
            'sn' => 'O100206',
            'psn' => 'O100200',
            'level' => '3',
            'auth_name' => '编辑分期',
            'path' => 'offline/offline/editrpm',
            'type' => '2',
            'auth_description' => '编辑分期',
            'status' => '1',
            'order_code' => '7',
        ]);
        $this->insert('auth', [
            'sn' => 'O100207',
            'psn' => 'O100200',
            'level' => '3',
            'auth_name' => '增加分期',
            'path' => 'offline/offline/addrpm',
            'type' => '2',
            'auth_description' => '增加分期',
            'status' => '1',
            'order_code' => '7',
        ]);
    }

    public function down()
    {
        echo "m170419_081213_insert_auth_table cannot be reverted.\n";

        return false;
    }


}
