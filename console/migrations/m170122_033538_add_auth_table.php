<?php

use yii\db\Migration;

class m170122_033538_add_auth_table extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'A101400',
            'psn' => 'A100000',
            'level' => '2',
            'auth_name' => '首页开屏',
            'path' => 'adv/adv/kaiping-list',
            'type' => '1',
            'auth_description' => '首页开屏',
            'status' => '1',
            'order_code' => '4',
        ]);
        $this->insert('auth', [
            'sn' => 'H100001',
            'psn' => 'A101400',
            'level' => '3',
            'auth_name' => '首页开屏添加编辑',
            'path' => 'adv/adv/kaiping-edit',
            'type' => '2',
            'auth_description' => '首页开屏添加编辑',
            'status' => '1',
            'order_code' => '4',
        ]);
    }

    public function down()
    {
        echo "m170122_033538_add_auth_table cannot be reverted.\n";

        return false;
    }
}
