<?php

use yii\db\Migration;

class m180828_093001_insert_auth_table extends Migration
{
    public function safeUp()
    {
        $this->insert('auth', [
            'sn' => 'H100208',
            'psn' => 'H100200',
            'level' => '3',
            'auth_name' => '显示融资会员列表',
            'path' => 'user/user/show-org-list',
            'type' => '2',
            'auth_description' => '融资会员列表初始显示',
            'status' => '1',
            'order_code' => '3',
        ]);
    }

    public function safeDown()
    {
        $this->delete('auth', ['path' => 'user/user/org-list']);
    }
}
