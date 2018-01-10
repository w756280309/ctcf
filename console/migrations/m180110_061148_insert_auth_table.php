<?php

use yii\db\Migration;

class m180110_061148_insert_auth_table extends Migration
{
    public function Up()
    {
        $this->insert('auth', [
            'sn' => 'H100117',
            'psn' => 'H100100',
            'level' => '3',
            'auth_name' => '同步换卡信息',
            'path' => 'user/bank-card/update-bank-card-status',
            'type' => '2',
            'auth_description' => '同步换卡信息',
            'status' => '1',
            'order_code' => '3',
        ]);
    }

    public function Down()
    {
        echo "m180110_061148_insert_auth_table cannot be reverted.\n";

        return false;
    }

}
