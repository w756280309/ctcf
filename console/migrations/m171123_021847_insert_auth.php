<?php

use yii\db\Migration;

class m171123_021847_insert_auth extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'H100702',
            'psn' => 'H100700',
            'level' => '3',
            'auth_name' => '查看',
            'path' => 'user/personalinvest/view',
            'type' => '2',
            'auth_description' => '查看',
            'status' => '1',
            'order_code' => '3',
        ]);
        $this->insert('auth', [
            'sn' => 'H100703',
            'psn' => 'H100702',
            'level' => '4',
            'auth_name' => '查看付息',
            'path' => 'user/personalinvest/view-fx',
            'type' => '2',
            'auth_description' => '查看付息',
            'status' => '1',
            'order_code' => '4',
        ]);
    }

    public function down()
    {
        echo "m171123_021847_insert_auth cannot be reverted.\n";

        return false;
    }
}
