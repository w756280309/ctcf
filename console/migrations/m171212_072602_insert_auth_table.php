<?php

use yii\db\Migration;

class m171212_072602_insert_auth_table extends Migration
{
    public function Up()
    {
        $this->insert('auth', [
            'sn' => 'A102300',
            'psn' => 'A100000',
            'level' => '2',
            'auth_name' => '闪屏图',
            'path' => 'adv/splash/index',
            'type' => '1',
            'auth_description' => '闪屏图',
            'status' => '1',
            'order_code' => '4',
        ]);

        $this->insert('auth', [
            'sn' => 'A102301',
            'psn' => 'A102300',
            'level' => '3',
            'auth_name' => '闪屏图添加编辑',
            'path' => 'adv/splash/edit',
            'type' => '2',
            'auth_description' => '闪屏图添加编辑',
            'status' => '1',
            'order_code' => '4',
        ]);
    }

    public function Down()
    {
        echo "m171212_072602_insert_auth_table cannot be reverted.\n";

        return false;
    }
}
