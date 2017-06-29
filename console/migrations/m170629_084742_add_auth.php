<?php

use yii\db\Migration;

class m170629_084742_add_auth extends Migration
{
    public function safeUp()
    {
        $this->insert('auth', [
            'sn' => 'T100000',
            'psn' => '0',
            'level' => '1',
            'auth_name' => '后台工具',
            'path' => 'tool/index',
            'type' => '2',
            'auth_description' => '后台工具',
            'status' => '1',
            'order_code' => '4',
        ]);
        $this->insert('auth', [
            'sn' => 'T100001',
            'psn' => 'T100000',
            'level' => '3',
            'auth_name' => '平台现金账户充值',
            'path' => 'tool/recharge',
            'type' => '2',
            'auth_description' => '平台现金账户充值',
            'status' => '1',
            'order_code' => '4',
        ]);
    }

    public function safeDown()
    {
        echo "m170629_084742_add_auth cannot be reverted.\n";

        return false;
    }
}
