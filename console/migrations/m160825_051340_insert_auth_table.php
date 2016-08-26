<?php

use yii\db\Migration;

class m160825_051340_insert_auth_table extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'H100106',
            'psn' => 'H100100',
            'level' => '3',
            'auth_name' => '查看用户代金券列表',
            'path' => 'coupon/coupon/list-for-user',
            'type' => '2',
            'auth_description' => '查看用户代金券列表',
            'status' => '1',
            'order_code' => '3',
        ]);

        $this->insert('auth', [
            'sn' => 'H100107',
            'psn' => 'H100100',
            'level' => '3',
            'auth_name' => '给指定用户发放代金券',
            'path' => 'coupon/coupon/issue-for-user',
            'type' => '2',
            'auth_description' => '给指定用户发放代金券',
            'status' => '1',
            'order_code' => '3',
        ]);

        $this->insert('auth', [
            'sn' => 'H100108',
            'psn' => 'H100100',
            'level' => '3',
            'auth_name' => '查询可发放代金券列表',
            'path' => 'coupon/coupon/allow-issue-list',
            'type' => '2',
            'auth_description' => '查询可发放代金券列表',
            'status' => '1',
            'order_code' => '3',
        ]);
    }

    public function down()
    {
        echo "m160825_051340_insert_auth_table cannot be reverted.\n";

        return false;
    }
}
