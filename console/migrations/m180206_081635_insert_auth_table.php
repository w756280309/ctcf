<?php

use yii\db\Migration;

class m180206_081635_insert_auth_table extends Migration
{
    public function Up()
    {
        $this->insert('auth', [
            'sn' => 'H100206',
            'psn' => 'H100200',
            'level' => '3',
            'auth_name' => '融资会员软删除',
            'path' => 'user/user/soft-delete-org-user',
            'type' => '2',
            'auth_description' => '融资会员软删除',
            'status' => '1',
            'order_code' => '3',
        ]);
    }

    public function Down()
    {
        echo "m180206_081635_insert_auth_table cannot be reverted.\n";

        return false;
    }
}
