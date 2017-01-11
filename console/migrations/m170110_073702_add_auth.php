<?php

use yii\db\Migration;

class m170110_073702_add_auth extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'H100500',
            'psn' => 'H100000',
            'level' => '2',
            'auth_name' => '线下会员列表',
            'path' => 'user/offline/list',
            'type' => '1',
            'auth_description' => '线下会员列表',
            'status' => '1',
            'order_code' => '3',
        ]);
        $this->insert('auth', [
            'sn' => 'H100501',
            'psn' => 'H100500',
            'level' => '3',
            'auth_name' => '线下会员详情',
            'path' => 'user/offline/detail',
            'type' => '2',
            'auth_description' => '线下会员详情',
            'status' => '1',
            'order_code' => '3',
        ]);
    }

    public function down()
    {
        echo "m170110_073702_add_auth cannot be reverted.\n";

        return false;
    }
}
