<?php

use yii\db\Migration;

class m160531_072333_add_count_link_auth extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'D100500',
            'psn' => 'D100000',
            'level' => '3',
            'auth_name' => '后台统计用户列表',
            'path' => 'datatj/datatj/list',
            'type' => '2',
            'auth_description' => '后台统计用户列表',
            'status' => '1',
            'order_code' => '4',
            'updated_at' => time(),
            'created_at' => time(),
        ]);
    }

    public function down()
    {
        echo "m160531_072333_add_count_link_auth cannot be reverted.\n";

        return false;
    }
}
