<?php

use yii\db\Migration;

class m170421_085158_insert_auth_table extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'D100101',
            'psn' => 'D100100',
            'level' => '3',
            'auth_name' => '换卡记录异常提醒',
            'path' => 'datatj/bank/update-list',
            'type' => '2',
            'auth_description' => '换卡记录异常提醒',
            'status' => '1',
            'order_code' => '5',
        ]);
    }

    public function down()
    {
        echo "m170421_085158_insert_auth_table cannot be reverted.\n";

        return false;
    }
}
