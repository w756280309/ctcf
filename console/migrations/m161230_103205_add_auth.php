<?php

use yii\db\Migration;

class m161230_103205_add_auth extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'D100203',
            'psn' => 'D100201',
            'level' => '3',
            'auth_name' => '统计用户列表导出',
            'path' => 'datatj/datatj/list-export',
            'type' => '2',
            'auth_description' => '统计用户列表导出',
            'status' => '1',
            'order_code' => '4',
        ]);
    }

    public function down()
    {
        echo "m161230_103205_add_auth cannot be reverted.\n";

        return false;
    }
}
