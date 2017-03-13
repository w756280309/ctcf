<?php

use yii\db\Migration;

class m170310_064949_insert_auth_table extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'A101500',
            'psn' => 'A100000',
            'level' => '2',
            'auth_name' => '月度代金券',
            'path' => 'coupon/coupon/month-list',
            'type' => '1',
            'auth_description' => '代金券数据查看导出',
            'status' => '1',
            'order_code' => '4',
        ]);
        $this->insert('auth', [
            'sn' => 'A101501',
            'psn' => 'A101500',
            'level' => '3',
            'auth_name' => 'Excel导出',
            'path' => 'coupon/coupon/export',
            'type' => '2',
            'auth_description' => '代金券数据导出',
            'status' => '1',
            'order_code' => '4',
        ]);
    }


    public function down()
    {
        echo "m170310_064949_insert_auth_table cannot be reverted.\n";

        return false;
    }


}
