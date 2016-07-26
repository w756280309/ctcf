<?php

use yii\db\Migration;

class m160725_020651_add_auth extends Migration
{
    public function up()
    {
        //为分销商统计添加权限
        $this->insert('auth', [
            'sn' => 'D100400',
            'psn' => 'D100000',
            'level' => 2,
            'auth_name' => '分销商统计',
            'path' => 'datatj/datatj/affiliation',
            'type' => 1,
            'auth_description' => '分销商统计',
            'status' => 1,
            'order_code' => 5,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        $this->insert('auth', [
            'sn' => 'D100401',
            'psn' => 'D100400',
            'level' => 3,
            'auth_name' => '分销商统计导出',
            'path' => 'datatj/datatj/affiliation-export',
            'type' => 2,
            'auth_description' => '分销商统计导出',
            'status' => 1,
            'order_code' => 5,
            'created_at' => time(),
            'updated_at' => time(),
        ]);

        //补充旧权限
        $this->insert('auth', [
            'sn' => 'D100203',
            'psn' => 'D100200',
            'level' => 3,
            'auth_name' => '日统计导出',
            'path' => 'datatj/datatj/day-export',
            'type' => 2,
            'auth_description' => '日统计导出',
            'status' => 1,
            'order_code' => 5,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
        $this->insert('auth', [
            'sn' => 'D100301',
            'psn' => 'D100300',
            'level' => 3,
            'auth_name' => '月统计导出',
            'path' => 'datatj/datatj/month-export',
            'type' => 2,
            'auth_description' => '月统计导出',
            'status' => 1,
            'order_code' => 5,
            'created_at' => time(),
            'updated_at' => time(),
        ]);
    }

    public function down()
    {
        echo "m160725_020651_add_auth cannot be reverted.\n";

        return false;
    }
}
