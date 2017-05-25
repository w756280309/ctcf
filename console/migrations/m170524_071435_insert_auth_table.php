<?php

use yii\db\Migration;

class m170524_071435_insert_auth_table extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'D100500',
            'psn' => 'D100000',
            'level' => '2',
            'auth_name' => '复投新增数据统计',
            'path' => 'datatj/user/index',
            'type' => '1',
            'auth_description' => '复投新增数据统计',
            'status' => '1',
            'order_code' => '5',
        ]);
    }

    public function down()
    {
        echo "m170524_071435_insert_auth_table cannot be reverted.\n";

        return false;
    }
}
