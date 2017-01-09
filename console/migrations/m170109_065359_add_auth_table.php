<?php

use yii\db\Migration;

class m170109_065359_add_auth_table extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'P200118',
            'psn' => 'P200100',
            'level' => '3',
            'auth_name' => '隐藏标的',
            'path' => 'product/productonline/hide-loan',
            'type' => '2',
            'auth_description' => '隐藏标的',
            'status' => '1',
            'order_code' => '2',
        ]);
    }

    public function down()
    {
        echo "m170109_065359_add_auth_table cannot be reverted.\n";

        return false;
    }
}
