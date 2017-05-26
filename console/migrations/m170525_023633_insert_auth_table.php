<?php

use yii\db\Migration;

class m170525_023633_insert_auth_table extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'A101900',
            'psn' => 'A100000',
            'level' => '2',
            'auth_name' => '外链管理',
            'path' => 'source/referral-source/index',
            'type' => '1',
            'auth_description' => '外链管理',
            'status' => '1',
            'order_code' => '4',
        ]);
        $this->insert('auth', [
            'sn' => 'A101901',
            'psn' => 'A101900',
            'level' => '3',
            'auth_name' => '添加外链',
            'path' => 'source/referral-source/add',
            'type' => '2',
            'auth_description' => '添加外链',
            'status' => '1',
            'order_code' => '4',
        ]);
    }

    public function down()
    {
        echo "m170525_023633_insert_auth_table cannot be reverted.\n";

        return false;
    }

}
