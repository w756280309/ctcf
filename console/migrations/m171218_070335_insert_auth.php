<?php

use yii\db\Migration;

class m171218_070335_insert_auth extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'O100209',
            'psn' => 'O100200',
            'level' => '3',
            'auth_name' => '设置起息日',
            'path' => 'offline/offline/jixi',
            'type' => '2',
            'auth_description' => '设置线下标的的起息日',
            'status' => '1',
            'order_code' => '7',
        ]);
        $this->insert('auth', [
            'sn' => 'O100210',
            'psn' => 'O100200',
            'level' => '3',
            'auth_name' => '还款计划',
            'path' => 'offline/offline/repayment-plan',
            'type' => '2',
            'auth_description' => '查看线下标的还款计划',
            'status' => '1',
            'order_code' => '7',
        ]);
        $this->insert('auth', [
            'sn' => 'ORP1001',
            'psn' => 'O100210',
            'level' => '4',
            'auth_name' => '付息',
            'path' => 'offline/offline/fuxi',
            'type' => '2',
            'auth_description' => '线下标的执行付息',
            'status' => '1',
            'order_code' => '7',
        ]);
    }

    public function down()
    {
        echo "m171218_070335_insert_auth cannot be reverted.\n";

        return false;
    }
}
