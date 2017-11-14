<?php

use yii\db\Migration;

class m171109_031558_insert_auth_table extends Migration
{
    public function Up()
    {
        $this->insert('auth', [
            'sn' => 'D100700',
            'psn' => 'D100000',
            'level' => '2',
            'auth_name' => '新手标人数统计',
            'path' => 'datatj/datatj/xinshoutj',
            'type' => '1',
            'auth_description' => '新手标人数统计',
            'status' => '1',
            'order_code' => '5',
        ]);
    }

    public function Down()
    {
        echo "m171109_031558_insert_auth_table cannot be reverted.\n";

        return false;
    }
}
