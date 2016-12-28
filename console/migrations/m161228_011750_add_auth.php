<?php

use yii\db\Migration;

class m161228_011750_add_auth extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'H100500',
            'psn' => 'H100100',
            'level' => '3',
            'auth_name' => '导出全部投资会员',
            'path' => 'user/user/export',
            'type' => '2',
            'auth_description' => '导出全部投资会员',
            'status' => '1',
            'order_code' => '4',
        ]);
    }

    public function down()
    {
        echo "m161228_011750_add_auth cannot be reverted.\n";

        return false;
    }
}
