<?php

use yii\db\Migration;

class m170324_014403_add_auth extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'A101703',
            'psn' => 'A101700',
            'level' => '3',
            'auth_name' => '插入积分记录',
            'path' => 'growth/points/add',
            'type' => '2',
            'auth_description' => '插入积分记录',
            'status' => '1',
            'order_code' => '4',
        ]);
        $this->insert('auth', [
            'sn' => 'A101704',
            'psn' => 'A101700',
            'level' => '3',
            'auth_name' => '积分发放记录',
            'path' => 'growth/points/list',
            'type' => '2',
            'auth_description' => '积分发放记录',
            'status' => '1',
            'order_code' => '4',
        ]);
    }

    public function down()
    {
        echo "m170324_014403_add_auth cannot be reverted.\n";

        return false;
    }
}
