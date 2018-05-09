<?php

use yii\db\Migration;

class m180509_110751_insert_auth extends Migration
{
    public function safeUp()
    {
        $this->insert('auth', [
            'sn' => 'H100207',
            'psn' => 'H100200',
            'level' => '3',
            'auth_name' => '导出融资会员信息',
            'path' => 'user/user/org-user-info-export',
            'type' => '2',
            'auth_description' => '导出融资会员信息',
            'status' => '1',
            'order_code' => '3',
        ]);
    }

    public function safeDown()
    {
        echo "m180509_110751_insert_auth cannot be reverted.\n";

        return false;
    }
}
