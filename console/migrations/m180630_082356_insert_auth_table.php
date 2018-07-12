<?php

use yii\db\Migration;

class m180630_082356_insert_auth_table extends Migration
{

    public function up()
    {
        $this->insert('auth', [
            'sn' => 'H100115',
            'psn' => 'H100100',
            'level' => '3',
            'auth_name' => '用户锁定(禁止访问)',
            'path' => 'user/user/user-access',
            'type' => '2',
            'auth_description' => '用户锁定(禁止访问)',
            'status' => '1',
            'order_code' => '4',
        ]);
    }

    public function down()
    {
        $this->delete('auth', ['sn' => 'H100115']);

        return true;
    }

}
