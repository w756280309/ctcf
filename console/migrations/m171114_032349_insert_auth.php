<?php

use yii\db\Migration;

class m171114_032349_insert_auth extends Migration
{
    public function Up()
    {
        $this->insert('auth', [
            'sn' => 'H100700',
            'psn' => 'H100000',
            'level' => '2',
            'auth_name' => '个人投资详情导出',
            'path' => 'user/personalinvest/index',
            'type' => '1',
            'auth_description' => '个人投资详情导出',
            'status' => '1',
            'order_code' => '3',
        ]);
    }

    public function Down()
    {
        echo "m171114_032349_insert_auth cannot be reverted.\n";

        return false;
    }
}
