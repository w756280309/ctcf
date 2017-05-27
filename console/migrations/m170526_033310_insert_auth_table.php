<?php

use yii\db\Migration;

class m170526_033310_insert_auth_table extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'A102100',
            'psn' => 'A100000',
            'level' => '2',
            'auth_name' => '应用信息',
            'path' => 'growth/app-meta/index',
            'type' => '1',
            'auth_description' => '应用信息',
            'status' => '1',
            'order_code' => '4',
        ]);
        $this->insert('auth', [
            'sn' => 'A102101',
            'psn' => 'A100000',
            'level' => '3',
            'auth_name' => '编辑应用信息',
            'path' => 'growth/app-meta/edit',
            'type' => '2',
            'auth_description' => '活动统计编辑',
            'status' => '1',
            'order_code' => '4',
        ]);
    }

    public function down()
    {
        echo "m170526_033310_insert_auth_table cannot be reverted.\n";

        return false;
    }

}
