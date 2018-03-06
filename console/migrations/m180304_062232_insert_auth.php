<?php

use yii\db\Migration;

class m180304_062232_insert_auth extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'A100804',
            'psn' => 'A100800',
            'level' => '3',
            'auth_name' => '分销商二维码',
            'path' => 'fenxiao/fenxiao/code-view',
            'type' => '2',
            'auth_description' => '查看分销商的二维码',
            'status' => '1',
            'order_code' => '3',
        ]);
    }

    public function down()
    {
        echo "m180304_062232_insert_auth cannot be reverted.\n";

        return false;
    }
}
