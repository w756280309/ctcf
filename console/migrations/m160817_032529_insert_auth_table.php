<?php

use yii\db\Migration;

class m160817_032529_insert_auth_table extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'O100000',
            'psn' => '0',
            'level' => '1',
            'auth_name' => '线下数据',
            'path' => 'offline/offline/list',
            'type' => '1',
            'auth_description' => '线下数据',
            'status' => '1',
            'order_code' => '7',
        ]);
        $this->insert('auth', [
            'sn' => 'O100100',
            'psn' => 'O100000',
            'level' => '2',
            'auth_name' => '线下数据',
            'path' => 'offline/offline/list',
            'type' => '1',
            'auth_description' => '线下数据',
            'status' => '1',
            'order_code' => '7',
        ]);
        $this->insert('auth', [
            'sn' => 'O100101',
            'psn' => 'O100100',
            'level' => '3',
            'auth_name' => '导入新数据',
            'path' => 'offline/offline/add',
            'type' => '2',
            'auth_description' => '导入新数据',
            'status' => '1',
            'order_code' => '7',
        ]);
    }

    public function down()
    {
        echo "m160817_032529_insert_auth_table cannot be reverted.\n";

        return false;
    }
}
