<?php

use yii\db\Migration;

class m161027_093521_insert_auth_table extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'O100102',
            'psn' => 'O100100',
            'level' => '3',
            'auth_name' => '删除',
            'path' => 'offline/offline/delete',
            'type' => '2',
            'auth_description' => '删除',
            'status' => '1',
            'order_code' => '7',
        ]);
    }

    public function down()
    {
        echo "m161027_093521_insert_auth_table cannot be reverted.\n";

        return false;
    }
}
