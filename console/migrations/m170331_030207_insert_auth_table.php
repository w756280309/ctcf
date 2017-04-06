<?php

use yii\db\Migration;

class m170331_030207_insert_auth_table extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'H100604',
            'psn' => 'H100600',
            'level' => '3',
            'auth_name' => '线上会员',
            'path' => 'user/offline/online-user',
            'type' => '2',
            'auth_description' => '线上会员列表',
            'status' => '1',
            'order_code' => '3',
        ]);
        $this->insert('auth', [
            'sn' => 'H100311',
            'psn' => 'H100300',
            'level' => '3',
            'auth_name' => '线下会员',
            'path' => 'user/user/offline-user',
            'type' => '2',
            'auth_description' => '更新补充领取人操作',
            'status' => '1',
            'order_code' => '3',
        ]);
    }

    public function down()
    {
        echo "m170331_030207_insert_auth_table cannot be reverted.\n";

        return false;
    }
}
