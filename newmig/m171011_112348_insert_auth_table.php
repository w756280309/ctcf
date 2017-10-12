<?php

use yii\db\Migration;

class m171011_112348_insert_auth_table extends Migration
{
    public function Up()
    {
        $this->insert('auth', [
            'sn' => 'D100600',
            'psn' => 'D100000',
            'level' => '2',
            'auth_name' => '复投率',
            'path' => 'datatj/datatj/platform-rate',
            'type' => '1',
            'auth_description' => '复投率',
            'status' => '1',
            'order_code' => '5',
        ]);
    }

    public function Down()
    {
        echo "m171011_112348_insert_auth_table cannot be reverted.\n";

        return false;
    }
}
