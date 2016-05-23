<?php

use yii\db\Migration;

class m160523_013926_ranking_auth extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'A100600',
            'psn' => 'A100000',
            'level' => '2',
            'auth_name' => '活动管理',
            'path' => 'adv/ranking/index',
            'type' => '1',
            'auth_description' => '活动管理',
            'status' => '1',
            'order_code' => '4',
            'updated_at' => time(),
            'created_at' => time(),
        ]);
        $this->insert('auth', [
            'sn' => 'A100601',
            'psn' => 'A100600',
            'level' => '3',
            'auth_name' => '新增活动',
            'path' => 'adv/ranking/create',
            'type' => '2',
            'auth_description' => '新增活动',
            'status' => '1',
            'order_code' => '4',
            'updated_at' => time(),
            'created_at' => time(),
        ]);
        $this->insert('auth', [
            'sn' => 'A100602',
            'psn' => 'A100600',
            'level' => '3',
            'auth_name' => '更新活动',
            'path' => 'adv/ranking/update',
            'type' => '1',
            'auth_description' => '更新活动',
            'status' => '1',
            'order_code' => '4',
            'updated_at' => time(),
            'created_at' => time(),
        ]);
        $this->insert('auth', [
            'sn' => 'A100700',
            'psn' => 'A100000',
            'level' => '2',
            'auth_name' => '线下投资管理',
            'path' => 'adv/offline-sale/index',
            'type' => '1',
            'auth_description' => '线下投资管理',
            'status' => '1',
            'order_code' => '4',
            'updated_at' => time(),
            'created_at' => time(),
        ]);
        $this->insert('auth', [
            'sn' => 'A100701',
            'psn' => 'A100700',
            'level' => '3',
            'auth_name' => '添加线下投资',
            'path' => 'adv/offline-sale/create',
            'type' => '2',
            'auth_description' => '添加线下投资',
            'status' => '1',
            'order_code' => '4',
            'updated_at' => time(),
            'created_at' => time(),
        ]);
        $this->insert('auth', [
            'sn' => 'A100702',
            'psn' => 'A100700',
            'level' => '3',
            'auth_name' => '更新线下投资',
            'path' => 'adv/offline-sale/update',
            'type' => '2',
            'auth_description' => '更新线下投资',
            'status' => '1',
            'order_code' => '4',
            'updated_at' => time(),
            'created_at' => time(),
        ]);
    }

    public function down()
    {
        echo "m160523_013926_ranking_auth cannot be reverted.\n";

        return false;
    }
}
