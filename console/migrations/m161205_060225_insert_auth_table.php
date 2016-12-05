<?php

use yii\db\Migration;

class m161205_060225_insert_auth_table extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'P200117',
            'psn' => 'P200100',
            'level' => '3',
            'auth_name' => '查看标的信息',
            'path' => 'product/productonline/show',
            'type' => '2',
            'auth_description' => '查看标的信息',
            'status' => '1',
            'order_code' => '2',
        ]);
    }

    public function down()
    {
        echo "m161205_060225_insert_auth_table cannot be reverted.\n";

        return false;
    }
}
