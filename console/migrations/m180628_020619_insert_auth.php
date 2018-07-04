<?php

use yii\db\Migration;

class m180628_020619_insert_auth extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'P200602',
            'psn' => 'P200600',
            'level' => '3',
            'auth_name' => '发布标的',
            'path' => 'product/asset/create-product',
            'type' => '2',
            'auth_description' => '资产包发布标的',
            'status' => '1',
            'order_code' => '2',
        ]);
        $this->insert('auth', [
            'sn' => 'P200600',
            'psn' => 'P200000',
            'level' => '2',
            'auth_name' => '资产包管理',
            'path' => 'product/asset/index',
            'type' => '1',
            'auth_description' => '资产包列表',
            'status' => '1',
            'order_code' => '2',
        ]);
    }

    public function safeDown()
    {
        echo "m180628_020619_insert_auth cannot be reverted.\n";

        return false;
    }
}
