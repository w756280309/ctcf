<?php

use yii\db\Migration;

class m160901_064105_insert_auth_table extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'H100109',
            'psn' => 'H100100',
            'level' => '3',
            'auth_name' => '查看用户所属分销商',
            'path' => 'fenxiao/fenxiao/get-aff-info',
            'type' => '2',
            'auth_description' => '查看用户所属分销商',
            'status' => '1',
            'order_code' => '3',
        ]);

        $this->insert('auth', [
            'sn' => 'H100110',
            'psn' => 'H100100',
            'level' => '3',
            'auth_name' => '修改用户所属分销商',
            'path' => 'fenxiao/fenxiao/edit-for-user',
            'type' => '2',
            'auth_description' => '修改用户所属分销商',
            'status' => '1',
            'order_code' => '3',
        ]);
    }

    public function down()
    {
        echo "m160901_064105_insert_auth_table cannot be reverted.\n";

        return false;
    }
}
