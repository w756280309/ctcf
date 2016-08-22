<?php

use yii\db\Migration;

class m160819_081634_insert_auth_table extends Migration
{
    public function up()
    {
        $this->insert('auth', [
            'sn' => 'H100104',
            'psn' => 'H100100',
            'level' => '3',
            'auth_name' => '查看用户的银行卡列表',
            'path' => 'user/bank-card/list',
            'type' => '2',
            'auth_description' => '查看用户的银行卡列表',
            'status' => '1',
            'order_code' => '3',
        ]);

        $this->insert('auth', [
            'sn' => 'H100105',
            'psn' => 'H100100',
            'level' => '3',
            'auth_name' => '查询用户银行卡在联动一侧的状态',
            'path' => 'user/bank-card/ump-info',
            'type' => '2',
            'auth_description' => '查询用户银行卡在联动一侧的状态',
            'status' => '1',
            'order_code' => '3',
        ]);
    }

    public function down()
    {
        echo "m160819_081634_insert_auth_table cannot be reverted.\n";

        return false;
    }
}
