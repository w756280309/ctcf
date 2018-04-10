<?php

use yii\db\Migration;

class m180408_022753_insert_auth extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'U100400',
            'psn' => 'U100000',
            'level' => '2',
            'auth_name' => '管理员日志',
            'path' => 'adminuser/admin/admin-log',
            'type' => '1',
            'auth_description' => '显示管理员的日志',
            'status' => '1',
            'order_code' => '1',
        ]);
        $this->insert('auth', [
            'sn' => 'H100800',
            'psn' => 'H100000',
            'level' => '2',
            'auth_name' => '用户日志',
            'path' => 'user/user/user-log',
            'type' => '1',
            'auth_description' => '显示用户登录日志',
            'status' => '1',
            'order_code' => '3',
        ]);
    }

    public function down()
    {
        echo "m180408_022753_insert_auth cannot be reverted.\n";

        return false;
    }
}
