<?php

use yii\db\Migration;

class m170114_060226_add_auth extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'O100104',
            'psn' => 'O100100',
            'level' => '3',
            'auth_name' => '确认起息日',
            'path' => 'offline/offline/confirm',
            'type' => '2',
            'auth_description' => '确认起息日',
            'status' => '1',
            'order_code' => '7',
        ]);
    }

    public function down()
    {
        echo "m170114_060226_add_auth cannot be reverted.\n";

        return false;
    }
}
