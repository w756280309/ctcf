<?php

use yii\db\Migration;

class m170314_094903_insert_auth_table extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'O100105',
            'psn' => 'O100100',
            'level' => '3',
            'auth_name' => '编辑',
            'path' => 'offline/offline/edit',
            'type' => '2',
            'auth_description' => '线下数据编辑客户信息页面',
            'status' => '1',
            'order_code' => '7',
        ]);
        $this->insert('auth', [
            'sn' => 'O100106',
            'psn' => 'O100100',
            'level' => '3',
            'auth_name' => '提交',
            'path' => 'offline/offline/update',
            'type' => '2',
            'auth_description' => '更新编辑数据',
            'status' => '1',
            'order_code' => '7',
        ]);
    }

    public function down()
    {
        echo "m170314_094903_insert_auth_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
