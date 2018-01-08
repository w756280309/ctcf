<?php

use yii\db\Migration;

class m180108_085307_alter_auth extends Migration
{
    public function safeUp()
    {
        $this->insert('auth', [
            'sn' => 'R100000',
            'psn' => '0',
            'level' => '1',
            'auth_name' => '立合旺通充值工具',
            'path' => 'lhwt/index',
            'type' => '2',
            'auth_description' => '立合旺通充值工具',
            'status' => '1',
            'order_code' => '9',
        ]);
        $this->insert('auth', [
            'sn' => 'R100001',
            'psn' => 'R100000',
            'level' => '3',
            'auth_name' => '立合旺通投资者账户充值',
            'path' => 'lhwt/recharge',
            'type' => '2',
            'auth_description' => '立合旺通投资者账户充值',
            'status' => '1',
            'order_code' => '9',
        ]);
    }

    public function safeDown()
    {
        echo "m180108_085307_alter_auth cannot be reverted.\n";

        return false;
    }
}
