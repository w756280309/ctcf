<?php

use yii\db\Migration;

class m180115_065647_revise_auth_table extends Migration
{
    public function Up()
    {
        $this->delete('auth', ['path' => 'tool/index']);
        $this->delete('auth', ['path' => 'lhwt/index']);
        $this->insert('auth', [
            'sn' => 'L100200',
            'psn' => 'L100000',
            'level' => '2',
            'auth_name' => '立合旺通充值工具',
            'path' => 'lhwt/index',
            'type' => '1',
            'auth_description' => '立合旺通充值工具',
            'status' => '1',
            'order_code' => '6',
        ]);
        $this->insert('auth', [
            'sn' => 'A102400',
            'psn' => 'A100000',
            'level' => '2',
            'auth_name' => '后台工具',
            'path' => 'tool/index',
            'type' => '1',
            'auth_description' => '后台工具',
            'status' => '1',
            'order_code' => '4',
        ]);
        $this->update('auth', [
            'auth_name' => '立合旺通',
            'auth_description' => '立合旺通',
        ],[
            'path' => 'datatj/issuer/lh-list'
        ]);
    }

    public function Down()
    {
        echo "m180115_065647_revise_auth_table cannot be reverted.\n";

        return false;
    }
}
