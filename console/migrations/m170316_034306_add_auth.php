<?php

use yii\db\Migration;

class m170316_034306_add_auth extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'A101600',
            'psn' => 'A100000',
            'level' => '2',
            'auth_name' => 'O2O商家管理',
            'path' => 'o2o/affiliator/list',
            'type' => '1',
            'auth_description' => '商家列表',
            'status' => '1',
            'order_code' => '4',
        ]);
        $this->insert('auth', [
            'sn' => 'A101601',
            'psn' => 'A101600',
            'level' => '3',
            'auth_name' => '兑换码列表',
            'path' => 'o2o/card/list',
            'type' => '2',
            'auth_description' => '兑换码列表',
            'status' => '1',
            'order_code' => '4',
        ]);
        $this->insert('auth', [
            'sn' => 'A101602',
            'psn' => 'A101600',
            'level' => '3',
            'auth_name' => '补充兑换码',
            'path' => 'o2o/card/supplement',
            'type' => '2',
            'auth_description' => '补充兑换码',
            'status' => '1',
            'order_code' => '4',
        ]);
        $this->insert('auth', [
            'sn' => 'A101603',
            'psn' => 'A101600',
            'level' => '3',
            'auth_name' => '导出筛选结果',
            'path' => 'o2o/card/export',
            'type' => '2',
            'auth_description' => '导出筛选结果',
            'status' => '1',
            'order_code' => '4',
        ]);
    }

    public function down()
    {
        echo "m170316_034306_add_auth cannot be reverted.\n";

        return false;
    }
}