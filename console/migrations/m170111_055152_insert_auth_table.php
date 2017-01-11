<?php

use yii\db\Migration;

class m170111_055152_insert_auth_table extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'O100103',
            'psn' => 'O100100',
            'level' => '3',
            'auth_name' => '编辑线下统计数据',
            'path' => 'offline/offline/edit-stats',
            'type' => '2',
            'auth_description' => '编辑线下统计数据',
            'status' => '1',
            'order_code' => '7',
        ]);
    }

    public function down()
    {
        echo "m170111_055152_insert_auth_table cannot be reverted.\n";

        return false;
    }
}
