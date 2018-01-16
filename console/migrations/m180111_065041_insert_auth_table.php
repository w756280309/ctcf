<?php

use yii\db\Migration;

class m180111_065041_insert_auth_table extends Migration
{
    public function Up()
    {
        $this->insert('auth', [
            'sn' => 'D100800',
            'psn' => 'D100000',
            'level' => '2',
            'auth_name' => '渠道用户信息',
            'path' => 'datatj/datatj/channel-user-info',
            'type' => '1',
            'auth_description' => '渠道用户信息',
            'status' => '1',
            'order_code' => '5',
        ]);
    }

    public function Down()
    {
        echo "m180111_065041_insert_auth_table cannot be reverted.\n";

        return false;
    }
}
