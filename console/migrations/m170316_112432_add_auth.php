<?php

use yii\db\Migration;

class m170316_112432_add_auth extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'A101700',
            'psn' => 'A100000',
            'level' => '2',
            'auth_name' => '批量发放积分',
            'path' => 'growth/points/init',
            'type' => '1',
            'auth_description' => '批量发放积分',
            'status' => '1',
            'order_code' => '4',
        ]);
        $this->insert('auth', [
            'sn' => 'A101701',
            'psn' => 'A101700',
            'level' => '3',
            'auth_name' => '积分导入预览',
            'path' => 'growth/points/preview',
            'type' => '2',
            'auth_description' => '积分导入预览',
            'status' => '1',
            'order_code' => '4',
        ]);
        $this->insert('auth', [
            'sn' => 'A101702',
            'psn' => 'A101700',
            'level' => '3',
            'auth_name' => '添加积分',
            'path' => 'growth/points/confirm',
            'type' => '2',
            'auth_description' => '添加积分',
            'status' => '1',
            'order_code' => '4',
        ]);
    }

    public function down()
    {
        echo "m170316_112432_add_auth cannot be reverted.\n";

        return false;
    }
}
