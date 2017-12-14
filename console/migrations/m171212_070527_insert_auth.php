<?php

use yii\db\Migration;

class m171212_070527_insert_auth extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'WX10103',
            'psn' => 'WX10100',
            'level' => '4',
            'auth_name' => '编辑全体消息',
            'path' => 'wechat/reply/edit-whole-message',
            'type' => '2',
            'auth_description' => '编辑全体消息',
            'status' => '1',
            'order_code' => '4',
        ]);
    }

    public function down()
    {
        echo "m171212_070527_insert_auth cannot be reverted.\n";

        return false;
    }
}
