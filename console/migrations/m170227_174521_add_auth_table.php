<?php

use yii\db\Migration;

class m170227_174521_add_auth_table extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'H100112',
            'psn' => 'H100100',
            'level' => '3',
            'auth_name' => '补充邀请关系',
            'path' => 'user/user/add-invite',
            'type' => '2',
            'auth_description' => '补充邀请关系',
            'status' => '1',
            'order_code' => '3',
        ]);
    }

    public function down()
    {
        echo "m170227_174521_add_auth_table cannot be reverted.\n";

        return false;
    }
}
