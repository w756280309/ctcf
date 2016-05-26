<?php

use yii\db\Migration;

class m160525_021623_insert_auth_table extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'A100900',
            'psn' => 'A100000',
            'level' => '2',
            'auth_name' => '代金券管理',
            'path' => 'coupon/coupon/list',
            'type' => '1',
            'auth_description' => '代金券管理',
            'status' => '1',
            'order_code' => '4',
        ]);

        $this->insert('auth', [
            'sn' => 'A100901',
            'psn' => 'A100900',
            'level' => '3',
            'auth_name' => '添加',
            'path' => 'coupon/coupon/add',
            'type' => '2',
            'auth_description' => '添加代金券',
            'status' => '1',
            'order_code' => '4',
        ]);

        $this->insert('auth', [
            'sn' => 'A100902',
            'psn' => 'A100900',
            'level' => '3',
            'auth_name' => '编辑',
            'path' => 'coupon/coupon/edit',
            'type' => '2',
            'auth_description' => '编辑代金券',
            'status' => '1',
            'order_code' => '4',
        ]);

        $this->insert('auth', [
            'sn' => 'A100903',
            'psn' => 'A100900',
            'level' => '3',
            'auth_name' => '发放',
            'path' => 'coupon/coupon/issue',
            'type' => '2',
            'auth_description' => '设置发放条件',
            'status' => '1',
            'order_code' => '4',
        ]);

        $this->insert('auth', [
            'sn' => 'A100904',
            'psn' => 'A100900',
            'level' => '3',
            'auth_name' => '领取记录',
            'path' => 'coupon/coupon/owner-list',
            'type' => '2',
            'auth_description' => '领取代金券记录',
            'status' => '1',
            'order_code' => '4',
        ]);

        $this->insert('auth', [
            'sn' => 'A100905',
            'psn' => 'A100900',
            'level' => '3',
            'auth_name' => '审核记录',
            'path' => 'coupon/coupon/audit',
            'type' => '2',
            'auth_description' => '审核代金券记录',
            'status' => '1',
            'order_code' => '4',
        ]);
    }

    public function down()
    {
        echo "m160525_021623_insert_auth_table cannot be reverted.\n";

        return false;
    }
}
