<?php

use yii\db\Migration;

class m161212_034752_insert_auth_table extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'A100603',
            'psn' => 'A100600',
            'level' => '3',
            'auth_name' => '活动上下线',
            'path' => 'adv/ranking/online',
            'type' => '2',
            'auth_description' => '活动上下线',
            'status' => '1',
            'order_code' => '4',
        ]);
    }

    public function down()
    {
        echo "m161212_034752_insert_auth_table cannot be reverted.\n";

        return false;
    }
}
