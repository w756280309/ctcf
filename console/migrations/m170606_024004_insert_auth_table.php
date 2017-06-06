<?php

use yii\db\Migration;

class m170606_024004_insert_auth_table extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'A102200',
            'psn' => 'A100000',
            'level' => '2',
            'auth_name' => '渠道管理',
            'path' => 'growth/referral/index',
            'type' => '1',
            'auth_description' => '渠道管理',
            'status' => '1',
            'order_code' => '4',
        ]);
        $this->insert('auth', [
            'sn' => 'A102201',
            'psn' => 'A102200',
            'level' => '3',
            'auth_name' => '渠道管理',
            'path' => 'growth/referral/index',
            'type' => '3',
            'auth_description' => '渠道管理列表',
            'status' => '1',
            'order_code' => '4',
        ]);
        $this->insert('auth', [
            'sn' => 'A102202',
            'psn' => 'A102200',
            'level' => '3',
            'auth_name' => '新增渠道',
            'path' => 'growth/referral/add',
            'type' => '2',
            'auth_description' => '新增渠道',
            'status' => '1',
            'order_code' => '4',
        ]);
        $this->insert('auth', [
            'sn' => 'A102203',
            'psn' => 'A102200',
            'level' => '3',
            'auth_name' => '编辑渠道信息',
            'path' => 'growth/referral/edit',
            'type' => '2',
            'auth_description' => '编辑渠道信息',
            'status' => '1',
            'order_code' => '4',
        ]);
        $this->insert('auth', [
            'sn' => 'A102204',
            'psn' => 'A102200',
            'level' => '3',
            'auth_name' => '删除渠道',
            'path' => 'growth/referral/delete',
            'type' => '2',
            'auth_description' => '删除渠道',
            'status' => '1',
            'order_code' => '4',
        ]);
    }

    public function down()
    {
        echo "m170606_024004_insert_auth_table cannot be reverted.\n";

        return false;
    }

}
