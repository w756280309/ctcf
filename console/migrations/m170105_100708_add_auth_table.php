<?php

use yii\db\Migration;

class m170105_100708_add_auth_table extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'P200300',
            'psn' => 'P200000',
            'level' => '2',
            'auth_name' => '转让列表',
            'path' => 'product/productonline/sponsoredtransfer',
            'type' => '1',
            'auth_description' => '转让列表',
            'status' => '1',
            'order_code' => '2',
        ]);

        $this->insert('auth', [
            'sn' => 'P200301',
            'psn' => 'P200300',
            'level' => '3',
            'auth_name' => '购买转让列表',
            'path' => 'product/productonline/buytransfer',
            'type' => '2',
            'auth_description' => '购买转让列表',
            'status' => '1',
            'order_code' => '2',
        ]);
    }

    public function down()
    {
        echo "m170105_100708_add_auth_table cannot be reverted.\n";

        return false;
    }
}
