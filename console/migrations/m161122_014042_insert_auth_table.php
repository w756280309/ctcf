<?php

use yii\db\Migration;

class m161122_014042_insert_auth_table extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'H100111',
            'psn' => 'H100100',
            'level' => '3',
            'auth_name' => '投资者用户列表（当月超过指定提现次数）',
            'path' => 'user/user/draw-limit-list',
            'type' => '2',
            'auth_description' => '查询当月提现次数超过多少次的用户列表',
            'status' => '1',
            'order_code' => '3',
        ]);
    }

    public function down()
    {
        echo "m161122_014042_insert_auth_table cannot be reverted.\n";

        return false;
    }
}
