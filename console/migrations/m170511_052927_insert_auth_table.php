<?php

use yii\db\Migration;

class m170511_052927_insert_auth_table extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'H100113',
            'psn' => 'H100100',
            'level' => '3',
            'auth_name' => '修改用户注册IP位置信息',
            'path' => 'user/user/update-reg-location',
            'type' => '2',
            'auth_description' => '修改用户注册IP位置信息',
            'status' => '1',
            'order_code' => '3',
        ]);

        $this->insert('auth', [
            'sn' => 'D100102',
            'psn' => 'D100100',
            'level' => '3',
            'auth_name' => '换卡记录异常提醒',
            'path' => 'datatj/bank/count-for-update',
            'type' => '2',
            'auth_description' => '换卡记录异常提醒',
            'status' => '1',
            'order_code' => '5',
        ]);

        $this->update('auth', [
            'auth_name' => '换卡记录异常列表',
            'auth_description' => '换卡记录异常列表',
        ], [
            'path' => 'datatj/bank/update-list',
        ]);

        $this->insert('auth', [
            'sn' => 'H100114',
            'psn' => 'H100100',
            'level' => '3',
            'auth_name' => '当月超过指定提现次数异常提醒',
            'path' => 'user/user/draw-stats-count',
            'type' => '2',
            'auth_description' => '当月超过指定提现次数异常提醒',
            'status' => '1',
            'order_code' => '3',
        ]);
    }

    public function down()
    {
        echo "m170511_052927_insert_auth_table cannot be reverted.\n";

        return false;
    }
}
