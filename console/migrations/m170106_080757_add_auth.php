<?php

use yii\db\Migration;

class m170106_080757_add_auth extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'A101307',
            'psn' => 'A101300',
            'level' => '3',
            'auth_name' => '下载中间页',
            'path' => 'growth/code/refer',
            'type' => '2',
            'auth_description' => '下载中间页',
            'status' => '1',
            'order_code' => '4',
        ]);
    }

    public function down()
    {
        echo "m170106_080757_add_auth cannot be reverted.\n";

        return false;
    }
}
